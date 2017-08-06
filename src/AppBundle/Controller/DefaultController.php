<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Commune;
use AppBundle\Entity\Role;
use AppBundle\Entity\Park;
use AppBundle\Entity\User;
use AppBundle\Entity\WasteType;
use AppBundle\Entity\Container;
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
        $this->addDefaultValueInDatabase();

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

    private function addDefaultValueInDatabase(){

        $em = $this->getDoctrine()->getManager();

        if(count($this->getDoctrine()->getRepository('AppBundle:Commune')->findAll()) === 0){
            $em->persist(new Commune('Liège'));
            $em->persist(new Commune('Pepinster'));
            $em->persist(new Commune('Fléron'));
            $em->persist(new Commune('Herve'));
        }
        if(count($this->getDoctrine()->getRepository('AppBundle:Role')->findAll()) === 0){

        }
        if(count($this->getDoctrine()->getRepository('AppBundle:Park')->findAll()) === 0){
            $communes = $em->getRepository('AppBundle:Commune');
            $em->persist(new Park('04/221.23.56','Rue du Péry','14','4000','Liège', $communes->findOneByName('Liège')));
            $em->persist(new Park('04/220.53.49','Avenue de l\'Avenir','45','4020','Wandre', $communes->findOneByName('Liège')));
            $em->persist(new Park('04/220.76.29','Rue Georges Crolon','5','4031','Angleur', $communes->findOneByName('Liège')));
            $em->persist(new Park('04/221.14.15','Rue du Tir','78','4020','Liège', $communes->findOneByName('Liège')));
            $em->persist(new Park('087/46.59.23','Avenue des Libertés','4','4860','Pepinster', $communes->findOneByName('Pepinster')));
            $em->persist(new Park('087/12.15.69','Rue des Clochers','56','4861','Soiron', $communes->findOneByName('Pepinster')));
            $em->persist(new Park('04/355.25.19','Rue de la Fosse','78','4620','Fléron', $communes->findOneByName('Fléron')));
            $em->persist(new Park('04/355.78.61','Rue des Gaillettes','95','4624','Romsée', $communes->findOneByName('Fléron')));
            $em->persist(new Park('087/69.36.56','Rue Jean-Marie Sitter','45','4650','Herve', $communes->findOneByName('Herve')));
            $em->persist(new Park('087/69.12.13','Avenue du Cardon','9','4652','Xhendelesse', $communes->findOneByName('Herve')));
        }
        if(count($this->getDoctrine()->getRepository('AppBundle:User')->findAll()) === 0){
            //- Hérant, Guillaume, g.herant@recyzone.be, gherant, azerty, gérant de l’intercommunale.
            $userManager = $this->container->get('fos_user.user_manager');
            $userAdmin = $userManager->createUser();

            $userAdmin;
            /*$userAdmin->setEmail($email);
            $userAdmin->setPlainPassword('random'); // TODO : replace with random generation
            $userAdmin->setEnabled(true);
            $userAdmin->setFirstName($firstname);
            $userAdmin->setLastName($firstname);
            $userAdmin->setStreetName($streetName);
            $userAdmin->setHouseNumber($houseNumber);
            $userAdmin->setHouseBox($houseBox);
            $userAdmin->setCommune($commune);
            $userAdmin->setCity($city);
            $userAdmin->setNumberOfAdult($numberOfAdult);
            $userAdmin->setNumberOfChild($numberOfChild);*/

            $userManager->updateUser($userAdmin, $lastName);
        }
        if(count($this->getDoctrine()->getRepository('AppBundle:WasteType')->findAll()) === 0){

        }
        if(count($this->getDoctrine()->getRepository('AppBundle:Container')->findAll()) === 0){

        }

        $em->flush();
    }
}
