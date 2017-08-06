<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Notification;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class GerantController extends Controller
{
    /**
     * @Route("/gerant", name="gerant")
     */
    public function indexAction(Request $request)
    {
        if ($this->getUser() == null){
            return $this->redirect('./login');
        }
        elseif ($this->getUser()->getRole()->getName() != 'Gérant'){
            return $this->redirect('./error');
        }
        else{
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
}
