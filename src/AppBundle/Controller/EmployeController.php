<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use function MongoDB\BSON\toJSON;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;

class EmployeController extends Controller
{
    /**
     * @Route("/employe", name="employe")
     */
    public function indexAction(Request $request)
    {
        /* TODO : TO BE REACTIVATED, DESACTIVATED FOR TESTING PURPOSE
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
        }*/

        if ($this->getUser() == null){
            return $this->redirect('./login');
        }
        elseif ($this->getUser()->getRole()->getName() != 'Employé'){
            return $this->redirect('./error');
        }
        else{
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
    }

    /**
     * @Route("/addHousehold", name="addHousehold")
     */
    public function addHousehold(Request $request)
    {
        if ($this->getUser() == null){
            return $this->redirect('./login');
        }
        elseif ($this->getUser()->getRole()->getName() != 'Employé'){
            return $this->redirect('./error');
        }
        else{

            $household = new User();
            $form = $this->createFormBuilder($household)
                ->add('firstname', TextType::class, array('attr' => array('class' => 'form-control')))
                ->add('lastName', TextType::class, array('attr' => array('class' => 'form-control')))
                ->add('email', EmailType::class, array('attr' => array('class' => 'form-control')))
                ->add('streetName', TextType::class, array('attr' => array('class' => 'form-control')))
                ->add('houseNumber', TextType::class, array('attr' => array('class' => 'form-control')))
                ->add('houseBox', TextType::class, array('attr' => array('class' => 'form-control')))
                ->add('commune', TextType::class, array('attr' => array('class' => 'form-control')))//choiceType
                ->add('city', TextType::class, array('attr' => array('class' => 'form-control')))
                ->add('numberOfAdult', IntegerType::class, array('attr' => array('class' => 'form-control')))
                ->add('numberOfChild', IntegerType::class, array('attr' => array('class' => 'form-control')))
                ->add('save', SubmitType::class, array('label' => 'Create Household', 'attr' => array('class' => 'btn btn-primary')))
                ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()){

                $firstname = $form['firstname']->getData();
                $lastName = $form['lastName']->getData();
                $email = $form['email']->getData();
                $streetName = $form['streetName']->getData();
                $houseNumber = $form['houseNumber']->getData();
                $houseBox = $form['houseBox']->getData();
                $commune = $form['commune']->getData();
                $city = $form['city']->getData();
                $numberOfAdult = $form['numberOfAdult']->getData();
                $numberOfChild = $form['numberOfChild']->getData();

                $userManager = $this->container->get('fos_user.user_manager');
                $userAdmin = $userManager->createUser();

                $userAdmin->setUsername(explode("@", $email, 2)[0]);
                $userAdmin->setEmail($email);
                $userAdmin->setPlainPassword('random'); // TODO : replace with random generation
                $userAdmin->setEnabled(true);
                $userAdmin->setFirstName($firstname);
                $userAdmin->setLastName($lastName);
                $userAdmin->setStreetName($streetName);
                $userAdmin->setHouseNumber($houseNumber);
                $userAdmin->setHouseBox($houseBox);
                $userAdmin->setCommune($commune);
                $userAdmin->setCity($city);
                $userAdmin->setNumberOfAdult($numberOfAdult);
                $userAdmin->setNumberOfChild($numberOfChild);

                $userManager->updateUser($userAdmin);

                return $this->redirectToRoute('employe');
            }

            return $this->render('employe/addHousehold.html.twig', array(
                'form' => $form->createView()
            ));
        }
    }

    /**
     * @Route("/makeDeposit", name="makeDeposit")
     */
    public function makeDeposit(Request $request)
    {
        if ($this->getUser() == null){
            return $this->redirect('./login');
        }
        elseif ($this->getUser()->getRole()->getName() != 'Employé'){
            return $this->redirect('./error');
        }
        else{
            $userManager = $this->container->get('fos_user.user_manager');
            $users = $userManager->findUsers();
            return $this->render('employe/makeDeposit.html.twig', array(
                'users' => $users
            ));
        }
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

}
