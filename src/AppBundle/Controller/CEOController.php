<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CEOController extends Controller
{
    /**
     * @Route("/ceo", name="CEO")
     */
    public function indexAction(Request $request)
    {
        $this->redirectIfNotCEO();
        $em = $this->getDoctrine()->getManager();
        //TODO : ONLY TX DE REMPLISSQUE > 50
        $query = $em->createQuery(
            "select n from AppBundle\Entity\Container n left join n.park as p left join n.waste_type as w order by w.name ASC"
        );
        $query->setParameters(array(
            //'parkID' => $this->getUser()->getPark()->getId()
        ));
        $containers = $query->getResult();
        return $this->render('ceo/index.html.twig',array(
            'containers' => $containers
        ));
    }

    /**
     * @Route("/statistiquesGlobales", name="statistiquesGlobales")
     */
    public function v(Request $request)
    {
        $this->redirectIfNotCEO();
        return $this->render('gerant/statistiques.html.twig',array(

        ));
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
