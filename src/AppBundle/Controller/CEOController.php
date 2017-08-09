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

    public function redirectIfNotCEO(){
        if ($this->getUser() == null){
            return $this->redirect('./login');
        }
        elseif ($this->getUser()->getRole()->getName() != 'CEO'){
            return $this->redirect('./error');
        }
    }
}
