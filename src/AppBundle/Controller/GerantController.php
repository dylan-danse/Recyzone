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

        $em = $this->getDoctrine()->getManager();
        $wasteTypes = $em->getRepository('AppBundle:WasteType')->findAll();

        return $this->render('gerant/statistiques.html.twig',array(
            'waste_types' => $wasteTypes
        ));
    }

    /**
     * @Route("/manage", name="manage")
     */
    public function manage(Request $request)
    {
        $this->redirectIfNotGerant();

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQueryBuilder();

        $query->select('u')
            ->from('AppBundle:User', 'u')
            ->leftJoin('u.park', 'p')
            ->where(($this->getUser()->getRole()->getName() === "CEO")?'0=0':'p.id = '.$this->getUser()->getPark()->getId()
            );
        $users = $query->getQuery()->getResult();

        $roles = $em->getRepository("AppBundle:Role")->findAll();

        return $this->render('gerant/manage.html.twig',array(
            'users' => $users,
            'roles' => $roles
        ));
    }

    /**
     * @Route("/updateRole/{userId}/{roleId}", name="updateRole")
     */
    public function updateRole($userId, $roleId)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("AppBundle:User")->find($userId);

        $oldRole = $user->getRole()->getName();

        $role = $em->getRepository("AppBundle:Role")->find($roleId);
        $user->setRole($role);
        $em->flush();

        return new JsonResponse($user->getFirstName()." ".$user->getLastName()." role updated successfully (".$oldRole." -> ".$role->getName().")", 200);
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
            ->where(($this->getUser()->getRole()->getName() === "CEO")?'0=0':'p.id = '.$parkId)
            ->andWhere(($wasteDQL === "")?'0=0':'w.id='.$wasteDQL)
            ->groupBy('key');

        $stats = $query->getQuery()->getResult();

        return new JsonResponse(array('data' => json_encode($stats)));
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
