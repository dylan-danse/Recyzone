<?php

namespace AppBundle\Controller;

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
            return $this->render('menage/index.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            ]);
        }
    }
}
