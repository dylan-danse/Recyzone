<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BillDetails;
use AppBundle\Entity\Deposit;
use AppBundle\Entity\Notification;
use AppBundle\Entity\Quota;
use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use AppBundle\Entity\WasteType;
use function MongoDB\BSON\toJSON;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Form\FormError;


class EmployeController extends Controller
{
    /**
     * @Route("/employe", name="employe")
     */
    public function indexAction(Request $request)
    {
        $this->redirectIfNotEmploye();

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery("select n from AppBundle\Entity\Notification n inner join n.park as p where p.id = :parkID and n.isDeleted=0");
        $query->setParameters(array(
            'parkID' => $this->getUser()->getPark()->getId()
        ));
        $notifications = $query->getResult();

        return $this->render('employe/index.html.twig',array(
            'notifications' => $notifications
        ));
    }

    /**
     * @Route("/addHousehold", name="addHousehold")
     */
    public function addHousehold(Request $request)
    {
        $this->redirectIfNotEmploye();

        $em = $this->getDoctrine()->getManager();
        $communes = $em->getRepository("AppBundle:Commune")->findAll();
        $comm = array();
        $cities = [];
        $cps = [];

        foreach ($communes as $commune){
            $comm[$commune->getName()] = $commune->getName();
            $name = $this->str_to_noaccent($commune->getName());
            $json = json_decode(file_get_contents('http://geoservices.wallonie.be/geolocalisation/rest/searchCommunes/'.$name));

            foreach($json->communes[0]->cps as $code){
                $cps[$code] = $commune->getName();;
            }
        }

        foreach ($cps as $key => $value){
            if(!isset($cities[$value])){
                $cities[$value] = [];
            }
            $json = json_decode(file_get_contents('http://geoservices.wallonie.be/geolocalisation/rest/getListeLocalitesByCp/'.$key));
            foreach ($json->localites as $locality){
                array_push($cities[$value],$key." : ".ucfirst(mb_strtolower($locality->nom)));
            }
        }

        $form = $this->createFormBuilder()
            ->add('firstName', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('lastName', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('email', EmailType::class, array('attr' => array('class' => 'form-control')))
            ->add('streetName', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('houseNumber', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('houseBox', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('commune', ChoiceType::class, array(
                'attr' => array('class' => 'form-control'),
                'choices' => $comm
            ))
            ->add('city', ChoiceType::class, array(
                'attr' => array('class' => 'form-control'),
                'choices' => $cities,
                'choice_label' => function($category) {
                    return $category;
                }
            ))
            ->add('numberOfAdult', IntegerType::class, array(
                'constraints' => [
                    new Range(
                        [
                            'min' => 1,
                            'minMessage' => "Le nombre d'adulte ne peut pas être moins de {{ limit }}"
                        ]
                    )
                ],
                'attr' => array('class' => 'form-control',  'value' => 1))
            )
            ->add('numberOfChild', IntegerType::class, array(
                    'constraints' => [
                        new Range(
                            [
                                'min' => 0,
                                'minMessage' => "Le nombre d'enfants ne peut pas être moins de {{ limit }}"
                            ]
                        )
                    ],
                    'attr' => array('class' => 'form-control',  'value' => 0))
            )
            ->add('save', SubmitType::class, array('label' => 'Create Household', 'attr' => array('class' => 'btn btn-primary')))
            ->getForm();

        $form->handleRequest($request);

        $email = $form['email']->getData();
        $username = explode("@", $email, 2)[0];

        $alreadyTaken = $em->getRepository("AppBundle:User")->findBy(array('username' => $username));
        if(count($alreadyTaken) > 0){
            $form->get('email')->addError(new FormError('Ce nom d\'utilisateur est déja utilisé'));
            return $this->render('employe/addHousehold.html.twig', array(
                'form' => $form->createView()
            ));
        }

        if ($form->isSubmitted() && $form->isValid()){

            $firstname = $form['firstName']->getData();
            $lastName = $form['lastName']->getData();
            $streetName = $form['streetName']->getData();
            $houseNumber = $form['houseNumber']->getData();
            $houseBox = $form['houseBox']->getData();
            $commune = $form['commune']->getData();
            $temp = explode(':',$form['city']->getData());
            $postCode = trim($temp[0]);
            $city = trim($temp[1]);
            $numberOfAdult = $form['numberOfAdult']->getData();
            $numberOfChild = $form['numberOfChild']->getData();

            $userManager = $this->container->get('fos_user.user_manager');
            $userAdmin = new User('random',$email,$username,
                $firstname,$lastName,$streetName,$houseNumber,$houseBox,$commune,$city,$postCode,$numberOfChild,$numberOfAdult,
                $this->getDoctrine()->getRepository("AppBundle:Role")->findOneByName(array("Ménage")),
                $this->getUser()->getPark());
            // TODO : replace with random password generation

            $userManager->updateUser($userAdmin);
            $this->createQuotasFor($userAdmin);

            return $this->render('divers/userAdded.html.twig', array(
                'user' => $userAdmin
            ));
        }

        return $this->render('employe/addHousehold.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/makeDeposit", name="makeDeposit")
     */
    public function makeDeposit(Request $request)
    {
        $this->redirectIfNotEmploye();

        $userManager = $this->container->get('fos_user.user_manager');
        $users = $userManager->findUsers();
        $em = $this->getDoctrine();
        $wasteTypes = $em->getRepository('AppBundle:WasteType')->findAll();
        return $this->render('employe/makeDeposit.html.twig', array(
            'users' => $users,
            'waste_types' => $wasteTypes
        ));
    }

    /**
     * @Route("/refreshUsersList", name="refreshUsersList")
     */
    public function refreshUsersList(Request $request)
    {
        if($request->isXmlHttpRequest())
        {
            $param = $request->get("id");

            $userManager = $this->container->get('fos_user.user_manager');
            $usersTemp = $userManager->findUsers();
            $usersFinal = array();
            foreach ($usersTemp as $user){
                if(strpos($user->getUsername(), $param) !== false && $user->getRole()->getName()==="Ménage"){
                    array_push($usersFinal,$user);
                }
            }
            return new JsonResponse(array('data' => json_encode($usersFinal)));
        }

        return new Response("Erreur : Ce n'est pas une requête Ajax", 400);
    }

    /**
     * @Route("/addWastes/{userId}", name="addWastes")
     */
    public function addWastes(Request $request, $userId)
    {
        $temp = json_decode($request->getContent(), true);
        $total=0;
        foreach($temp as $item) {
            $total += intval($item['volume']);
        }
        $responseCheck = $this->checkIfDepositIsAuthorized($userId, $total);
        if($responseCheck){
            return new Response($responseCheck,400);
        }

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQueryBuilder();

        foreach($temp as $item){
            $houseHold = $em->getRepository("AppBundle:User")->find($userId);
            $wasteType = $em->getRepository("AppBundle:WasteType")->findOneBy(array('name' => $item['type']));
            $volume = $item['volume'];
            $query->select('c')
                ->from('AppBundle:Container', 'c')
                ->leftJoin('c.park', 'p')
                ->leftJoin('c.waste_type','w')
                ->where("w.name = ?2")
                ->andWhere('p.id = ?1')
                ->andWhere('c.capacity-c.usedVolume >= ?3')
                ->orderBy('c.capacity-c.usedVolume', 'ASC')
                ->setParameters(
                    array(
                        1 => $this->getUser()->getPark()->getId(),
                        2 => $wasteType->getName(),
                        3 => $volume
                    )
                )
                ->setMaxResults(1);
            try{
                $container = $query->getQuery()->getSingleResult();
            } catch (\Exception $ex){
                return new Response("Dépôt impossible pour ".$volume."m3 (".$item['type'].") ! Aucun conteneur ne possède la capacité suffisante", 500);
            }

            $deposit = new Deposit($volume,new \DateTime("now"),$wasteType,$houseHold,$container,null);
            $em->persist($deposit);

            $container->setUsedVolume($container->getUsedVolume()+$volume);
            if($container->getCompletionPercentage() >= 80){
                $em->persist(new Notification(new \DateTime("now"),"Conteneur".$container->getId()." rempli à ".$container->getCompletionPercentage()."%", false,$this->getUser()->getPark()));
            }
            $em->flush();
        }
        return new JsonResponse("Dépôts effectué avec succès !", 200);
    }


    function createQuotasFor($user){
        $em = $this->getDoctrine()->getManager();
        $wasteTypes = $em->getRepository(WasteType::class)->findAll();
        foreach ($wasteTypes as $wasteType){
            $em->persist(new Quota($wasteType->getAnnualQuota()/100*(100+$user->getCorrectionCoeff()),$user,$wasteType));
        }
        $em->flush();
    }

    function checkIfDepositIsAuthorized($userId, $total){
        //check quota journalier
        if($total>4){
            return "Dépôts refusé ! Vous ne pouvez pas déposer plus de 4 m3 par jour";
        }

        $em = $this->getDoctrine()->getManager();

        //check visite journalière
        $query = $em->createQueryBuilder();
        $now = new \DateTime();
        $query->select('d')
            ->from('AppBundle:Deposit', 'd')
            ->leftJoin('d.household','h')
            ->where('h.id = ?1')
            ->andWhere('d.creationDate BETWEEN ?2 AND ?3')
            ->setParameters(
                array(
                    1 => $userId,
                    2 => new \DateTime($now->format("Y-m-d")." 00:00:00"),
                    3 => new \DateTime($now->format("Y-m-d")." 23:59:59")
                )
            );
        $result = $query->getQuery()->getResult();
        if(count($result) > 0){
            return "Dépôts refusé ! Vous avez déja effectué un dépôt aujourd'hui";
        }

        //check quota hebdomadaire
        $query = $em->createQueryBuilder();
        $query->select('SUM(d.quantity) as total')
            ->from('AppBundle:Deposit', 'd')
            ->leftJoin('d.household','h')
            ->where('h.id = ?1')
            ->andWhere('YEAR(d.creationDate) = YEAR(?2)')
            ->andWhere('WEEK(d.creationDate) = WEEK(?2)')
            ->setParameters(
                array(
                    1 => $userId,
                    2 => $now->format("Y-m-d")." 00:00:00"
                )
            );

        $result = $query->getQuery()->getSingleResult();
        $alreadyUsed = intval($result['total']);

        if($alreadyUsed + $total > 15){
            return "Dépôts refusé ! Vous ne pouvez pas déposer plus de 15 m3 par semaine (".$alreadyUsed." m3 déja utilisé)";
        }
    }

    function redirectIfNotEmploye(){
        if ($this->getUser() == null){
            return $this->redirect('./login');
        }
        elseif ($this->getUser()->getRole()->getName() != 'Employé'){
            return $this->redirect('./error');
        }
    }

    function redirectIfParkClosed(){
        $currentTime = date('H:i a');
        $openingWeekMorning =         \DateTime::createFromFormat('H:i', "09:00");
        $closingWeekMorning =         \DateTime::createFromFormat('H:i', "12:15");
        $openingWeekAfternoon =       \DateTime::createFromFormat('H:i', "13:00");
        $closingWeekAfternoon =       \DateTime::createFromFormat('H:i', "15:45");
        $openingSaturdayMorning =     \DateTime::createFromFormat('H:i', "08:30");
        $closingSaturdayMorning =     \DateTime::createFromFormat('H:i', "12:15");
        $openingSaturdayAfternoon =   \DateTime::createFromFormat('H:i', "13:00");
        $closingSaturdayAfternon =    \DateTime::createFromFormat('H:i', "16:45");
        switch (date('D')){
            case 'Sun':
            case 'Mon':
                return $this->render('employe/depositClosed.html.twig');
            case 'Tue':
            case 'Wed':
            case 'Thu':
            case 'Fri':
                if ($currentTime < $openingWeekMorning && $currentTime > $closingWeekMorning ||
                    $currentTime < $openingWeekAfternoon && $currentTime > $closingWeekAfternoon){
                    return $this->render('employe/depositClosed.html.twig');
                }
                break;
            case 'Sat':
                if ($currentTime < $openingSaturdayMorning && $currentTime > $closingSaturdayMorning ||
                    $currentTime < $openingSaturdayAfternoon && $currentTime > $closingSaturdayAfternon){
                    return $this->render('employe/depositClosed.html.twig');
                }
                break;
        }
    }

    function str_to_noaccent($str)
    {
        $url = $str;
        $url = preg_replace('#Ç#', 'C', $url);
        $url = preg_replace('#ç#', 'c', $url);
        $url = preg_replace('#è|é|ê|ë#', 'e', $url);
        $url = preg_replace('#È|É|Ê|Ë#', 'E', $url);
        $url = preg_replace('#à|á|â|ã|ä|å#', 'a', $url);
        $url = preg_replace('#@|À|Á|Â|Ã|Ä|Å#', 'A', $url);
        $url = preg_replace('#ì|í|î|ï#', 'i', $url);
        $url = preg_replace('#Ì|Í|Î|Ï#', 'I', $url);
        $url = preg_replace('#ð|ò|ó|ô|õ|ö#', 'o', $url);
        $url = preg_replace('#Ò|Ó|Ô|Õ|Ö#', 'O', $url);
        $url = preg_replace('#ù|ú|û|ü#', 'u', $url);
        $url = preg_replace('#Ù|Ú|Û|Ü#', 'U', $url);
        $url = preg_replace('#ý|ÿ#', 'y', $url);
        $url = preg_replace('#Ý#', 'Y', $url);

        return ($url);
    }
}
