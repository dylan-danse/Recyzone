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
            return $this->render('gerant/index.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            ]);
        }
    }
}
