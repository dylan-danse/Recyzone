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
        if ($this->getUser() == null){
            return $this->redirect('./login');
        }
        elseif ($this->getUser()->getRole()->getName() != 'Ménage'){
            return $this->redirect('./error');
        }
        else{
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository(User::class)->find($this->getUser()->getId());
            return $this->render('menage/index.html.twig',array(
                'user' => $user
            ));
        }
    }
}
