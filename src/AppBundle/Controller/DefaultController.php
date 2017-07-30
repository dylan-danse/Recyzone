<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        if ($this->getUser() == null){
            return $this->redirect('./login');
        }
        else{
            switch ($this->getUser()->getRole()->getName()){
                case "Employé" :
                    return $this->redirect('./employe');
                case "Gérant" :
                    return $this->redirect('./gerant');
                case "Ménage" :
                    return $this->redirect('./menage');
                case "CEO" :
                    return $this->redirect('./ceo');
                default :
                    return $this->redirect('./error');
            }
        }
        // replace this example code with whatever you need
        /*return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);*/
    }
}
