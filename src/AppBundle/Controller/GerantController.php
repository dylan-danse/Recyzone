<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
}
