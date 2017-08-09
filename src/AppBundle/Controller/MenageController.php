<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MenageController extends Controller
{
    /**
     * @Route("/menage", name="menage")
     */
    public function indexAction(Request $request)
    {
        $this->redirectIfNotHousehold();

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($this->getUser()->getId());
        $query = $em->createQueryBuilder();

        $query->select('w.name as type, d.quantity as deposed, 
                        w.annual_quota + (w.annual_quota/100*h.correctionCoeff) as authorized, 
                        w.annual_quota + (w.annual_quota/100*h.correctionCoeff)-d.quantity as remaining, 
                        h.id')
            ->from('AppBundle:WasteType', 'w')
            ->leftJoin('w.deposits','d')
            ->leftJoin('d.household','h')
            ->where('h.id = ?1')
            ->orderBy('w.name')
            ->setParameters(array(
                1 => $this->getUser()->getId()
            ));
        print_r($query->getQuery()->getResult());
        //print_r($query->getQuery()->getSql());
        $quotas = $query->getQuery()->getResult();

        return $this->render('menage/index.html.twig',array(
            'user' => $user,
            'quotas' => $quotas
        ));
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
