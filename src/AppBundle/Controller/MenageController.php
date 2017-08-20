<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class MenageController extends Controller
{
    /**
     * @Route("/menage", name="menage")
     */
    public function indexAction(Request $request)
    {
        $mustRedirect = $this->redirectIfNotHousehold();
        if($mustRedirect) return $mustRedirect;

        $id = $this->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("AppBundle:User")->find($id);

        $sql = " 
            SELECT w.name as type, 
                    (SELECT volume FROM quota where waste_type_id = w.id AND user_id = ".$id.") as total,
                    (SELECT SUM(quantity) FROM deposit where waste_type_id = w.id AND household_id = ".$id.") as deposed
            FROM waste_type w
            left JOIN deposit d on w.id = d.waste_type_id
            left join fos_user u on u.id = d.household_id
            left join quota q on u.id = q.user_id
            GROUP BY type,total,deposed
            ";

        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll();


        return $this->render('menage/index.html.twig',array(
            'user' => $user,
            'quotas' => $results
        ));
    }

    /**
     * @Route("/factures", name="factures")
     */
    public function factureAction(Request $request)
    {
        $mustRedirect = $this->redirectIfNotHousehold();
        if($mustRedirect) return $mustRedirect;

        $em = $this->getDoctrine()->getManager();
        $bills = $em->getRepository("AppBundle:Bill")->findBy(array("household" => $this->getUser()->getId()));


        return $this->render('menage/factures.html.twig',array(
            'bills' => $bills
        ));
    }

    /**
     * @Route("/deposits", name="deposits")
     */
    public function depositHistory(Request $request)
    {
        $mustRedirect = $this->redirectIfNotHousehold();
        if($mustRedirect) return $mustRedirect;

        $em = $this->getDoctrine()->getManager();
        $result =
            $em->createQuery("
                SELECT d 
                FROM AppBundle:Deposit d
                LEFT JOIN d.household h 
                LEFT JOIN d.waste_type w
                WHERE h.id = :billId"
            )->setParameter('billId', $this->getUser()->getId())
                ->getResult();

        return $this->render('menage/deposits.html.twig',array(
            'deposits' => $result
        ));
    }

    /**
     * @Route("/billdetails/{id}", name="billdetails")
     */
    public function getBillDetails($id){
        $mustRedirect = $this->redirectIfNotHousehold();
        if($mustRedirect) return new Response("Unauthorized",400);

        $em = $this->getDoctrine()->getManager();
        $result =
                $em->createQuery("
                SELECT bd FROM AppBundle:BillDetails bd
                JOIN bd.bill b WHERE b.id = :billId"
                )->setParameter('billId', $id)
                    ->getResult();


        foreach ($result as $detail){

            $qb = $em->createQueryBuilder();
            $qb->select('d')
                ->from('AppBundle:Deposit', 'd')
                ->leftJoin('d.billDetails','b')
                ->where('b.id = '.$detail->getId());
            $new = $qb->getQuery()->getResult();
            foreach($new as $deposit){
                $deposit->setBillDetails(null);
            }
            $detail->setDeposits($new);
        }

        return new JsonResponse(array('data'=>json_encode($result)));
    }



    public function redirectIfNotHousehold(){
        if ($this->getUser() == null){
            return $this->redirect('./login');
        }
        elseif ($this->getUser()->getRole()->getName() != 'Ménage'){
            return $this->redirect('./error');
        }
    }
}
