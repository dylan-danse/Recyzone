<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Notification;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;


class GerantController extends Controller
{
    /**
     * @Route("/gerant", name="gerant")
     */
    public function indexAction(Request $request)
    {
        $this->redirectIfNotGerant();

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery("select n from AppBundle\Entity\Notification n inner join n.park as p where p.id = :parkID and n.isDeleted=0");
        $query->setParameters(array(
            'parkID' => $this->getUser()->getPark()->getId()
        ));
        $notifications = $query->getResult();

        return $this->render('gerant/index.html.twig',array(
            'notifications' => $notifications
        ));
    }

    /**
     * @Route("/archiveNotifications", name="archiveNotifications")
     */
    public function archiveNotifications(Request $request)
    {
        if($request->isXmlHttpRequest())
        {
            $param = $request->get("id");
            $param = substr($param,1, strlen($param)-2);
            $param = explode(",", $param);
            foreach ($param as $id) {
                $em = $this->getDoctrine()->getManager();
                $current = $em->getRepository('AppBundle:Notification')->find(intval(substr($id,1,2)));
                if($current){
                    $current->setIsDeleted(true);
                }
                $em->flush();
            }
            return new Response("Supprimé avec succès", 200);
        }
        return new Response("Erreur : Ce n'est pas une requête Ajax", 400);
    }

    /**
     * @Route("/occupations", name="occupations")
     */
    public function occupations(Request $request)
    {
        $this->redirectIfNotGerant();

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            "select n from AppBundle\Entity\Container n left join n.park as p left join n.waste_type as w where p.id = :parkID order by w.name ASC"
        );
        $query->setParameters(array(
            'parkID' => $this->getUser()->getPark()->getId()
        ));
        $containers = $query->getResult();
        return $this->render('gerant/occupations.html.twig',array(
            'containers' => $containers
        ));
    }

    /**
     * @Route("/statistiques", name="statistiques")
     */
    public function statistiques(Request $request)
    {
        $this->redirectIfNotGerant();

        $em = $this->getDoctrine();
        $wasteTypes = $em->getRepository('AppBundle:WasteType')->findAll();

        return $this->render('gerant/statistiques.html.twig',array(
            'waste_types' => $wasteTypes
        ));
    }

    /**
     * @Route("/statistiques/{period}/{type}/{waste_id}", name="statistiques2")
     */
    public function getStatistiques($period,$type,$waste_id){

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQueryBuilder();
        $parkId = $this->getUser()->getPark()->getId();
        
        $periodDQL = "";
        $volumeOrVisiteDQL = "";
        $wasteDQL = "";

        if($period == "journalier"){
            $periodDQL = "DAYNAME";
        }
        else if($period == "mensuel") {
            $periodDQL = "MONTHNAME";
        }

        if($type == "volume"){
            $volumeOrVisiteDQL = "SUM";
            if($waste_id){
                $wasteDQL = $waste_id;
            }
        }
        else if($type == "visite"){
            $volumeOrVisiteDQL = "COUNT";
        }

        $query->select($periodDQL.'(d.creationDate) as key, '.$volumeOrVisiteDQL.'(d.quantity) as value')
            ->from('AppBundle:Deposit', 'd')
            ->leftJoin('d.container','c')
            ->leftJoin('c.park','p')
            ->leftJoin('d.waste_type','w')
            ->where('p.id = '.$parkId)
            ->andWhere(($wasteDQL === "")?'true':'w.id='.$wasteDQL)
            ->groupBy('key');

        $stats = $query->getQuery()->getResult();

        print_r($stats);
        return new Response();
    }

    public function redirectIfNotGerant(){
        if ($this->getUser() == null){
            return $this->redirect('./login');
        }
        elseif ($this->getUser()->getRole()->getName() != 'Gérant'){
            return $this->redirect('./error');
        }
    }
}
