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
        if ($this->getUser() == null){
            return $this->redirect('./login');
        }
        elseif ($this->getUser()->getRole()->getName() != 'CEO'){
            return $this->redirect('./error');
        }
        else{
            return $this->render('ceo/index.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            ]);
        }
    }
}
