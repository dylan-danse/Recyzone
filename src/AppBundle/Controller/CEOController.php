<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

    public function redirectIfNotCEO(){
        if ($this->getUser() == null){
            return $this->redirect('./login');
        }
        elseif ($this->getUser()->getRole()->getName() != 'CEO'){
            return $this->redirect('./error');
        }
    }
}
