<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Bill;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\BillDetails;
use Symfony\Component\HttpFoundation\JsonResponse;

class CEOController extends Controller
{
    /**
     * @Route("/ceo", name="CEO")
     */
    public function indexAction(Request $request)
    {
        $this->redirectIfNotCEO();
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQueryBuilder();

        $query->select('c')
            ->from('AppBundle:Container', 'c')
            ->leftJoin('c.park','p')
            ->leftJoin('c.waste_type','w')
            ->where('(c.usedVolume/c.capacity*100) >= 75')
            ->orderBy('c.usedVolume/c.capacity*100', 'DESC');

        $containers = $query->getQuery()->getResult();
        return $this->render('ceo/index.html.twig',array(
            'containers' => $containers
        ));
    }

    /**
     * @Route("/generateTournee", name="generateTournee")
     */
    public function emptyContainers(Request $request){
        //not compulsory, so for now clean all container instead of only the selected ones
        $this->redirectIfNotCEO();
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $query = $qb->update('AppBundle:Container', 'c')
            ->set('c.usedVolume', '0')
            ->getQuery();
        $query->execute();
        return new Response("Containers vidés avec succès ! (plz reload)",200);
    }

    /**
     * @Route("/generateBills", name="generateBills")
     */
    public function generateBills(Request $request){

        $responseBills = [];
        $em = $this->getDoctrine()->getManager();

        $sql = " 
            select d.waste_type_id as wasteTypeId, d.household_id as userId, SUM(d.quantity) as quantity
            from deposit d
            where d.bill_details_id IS NULL
            group by d.household_id, d.waste_type_id
            ";

        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll();

        $userId = '';
        $sameUser = false;

        if(count($results) == 0){
            return new Response("Nothing to be generated...",500);
        }

        foreach ($results as $detail){
            if($userId != '' && $userId == $detail['userId']){
                $sameUser = true;
            }
            $userId = $detail['userId'];

            $sql = "
                select q.volume - d.quantity as total, q.volume as quota
                from deposit d
                left join waste_type w on w.id = d.waste_type_id
                left join fos_user f on f.id = d.household_id
                left join quota q on q.user_id = f.id
                where f.id = ".$userId." AND d.waste_type_id = ".$detail['wasteTypeId']."  AND q.waste_type_id = ".$detail['wasteTypeId']." AND YEAR(d.creation_date)=YEAR(CURDATE())
                group by d.id
            ";

            $stmt = $em->getConnection()->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch();

            if ($result['total'] >= 0 || $result['quota'] == 0){
                $billDetails = new BillDetails(0,0, null);
            }else{

                $exceed = (-$result['total'])/$result['quota']*100;
                $wasteType = $em->getRepository("AppBundle:WasteType")->find($detail['wasteTypeId']);
                $forfait = $this->calculateForfait($wasteType, $exceed);
                $variable = $this->calculateVariable($wasteType, $exceed)*0.25*(-$result['total']);
                $billDetails = new BillDetails($forfait,$variable, null);

                $bill = $em->getRepository("AppBundle:Bill")->findOneBy(
                    array(),
                    array('id' => 'DESC')
                );

                if($sameUser && $bill){
                    $bill->setAmount($bill->getAmount() + $billDetails->getForfait() + $billDetails->getVariable());
                } else {
                    $household = $em->getRepository("AppBundle:User")->find($userId);
                    $bill = new Bill(
                        new \DateTime('now'),
                        new \DateTime('now'),
                        $billDetails->getForfait() + $billDetails->getVariable(),
                        "A payé",
                        $household
                    );
                    $em->persist($bill);
                    array_push($responseBills,$bill);
                    $em->flush();
                }
                $billDetails->setBill($bill);

                $em->persist($billDetails);

                $query = $em->createQueryBuilder()
                    ->select('d')
                    ->from('AppBundle:Deposit','d')
                    ->leftJoin('d.waste_type','w')
                    ->leftJoin('d.household','h')
                    ->where('h.id = '.$userId)
                    ->andWhere('w.id = '.$detail['wasteTypeId']);
                $deposits = $query->getQuery()->getResult();

                foreach ($deposits as $deposit){
                    $deposit->setBillDetails($billDetails);
                }

                $em->flush();
            }
        }

        return new JsonResponse(array('data' => json_encode($responseBills)),200);
    }

    public function calculateForfait($wasteType, $exceed){
        switch ($wasteType->getName()){
            case "déchets de jardin":
            case "bois":
                return ($exceed <= 20) ? 10 : 20 ;
            case "encombrants":
                return ($exceed <= 20) ? 15 : 30 ;
            case "briques et briquaillons":
            case "terres et sables":
                return ($exceed <= 20) ? 7.5 : 15 ;
            default:
                return 0;
        }
    }

    public function calculateVariable($wasteType, $exceed){
        switch ($wasteType->getName()){
            case "déchets de jardin":
                return ($exceed <= 20) ? 2.5 : 4 ;
            case "encombrants":
            case "bois":
                return ($exceed <= 20) ? 4 : 4 ;
            case "briques et briquaillons":
            case "terres et sables":
                return ($exceed <= 20) ? 3 : 5 ;
            default:
                return 0;
                break;
        }
    }

    public function redirectIfNotCEO(){
        if ($this->getUser() == null){
            return $this->redirect('./login');
        }
        elseif ($this->getUser()->getRole()->getName() != 'CEO'){
            return $this->redirect('./error');
        }
    }
}
