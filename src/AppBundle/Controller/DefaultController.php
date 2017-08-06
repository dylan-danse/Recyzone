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
use Symfony\Component\HttpFoundation\Response;
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

    /**
     * @Route("/DB", name="db")
     */
    public function addDefaultValueInDatabase(){

        $em = $this->getDoctrine()->getManager();
        $userManager = $this->container->get('fos_user.user_manager');

        $LIEGE = new Commune('Liège');
        $PEPINSTER = new Commune('Pepinster');
        $FLERON = new Commune('Fléron');
        $HERVE = new Commune('Herve');

        $CEO = new Role('CEO');
        $GERANT = new Role('Gérant');
        $EMPLOYE = new Role('Employé');
        $MENAGE = new Role('Ménage');

        $PARK1 = new Park('04/221.23.56','Rue du Péry','14','4000','Liège', $LIEGE);
        $PARK2 = new Park('04/220.53.49','Avenue de l\'Avenir','45','4020','Wandre', $LIEGE);
        $PARK3 = new Park('04/220.76.29','Rue Georges Crolon','5','4031','Angleur', $LIEGE);
        $PARK4 = new Park('04/221.14.15','Rue du Tir','78','4020','Liège', $LIEGE);
        $PARK5 = new Park('087/46.59.23','Avenue des Libertés','4','4860','Pepinster', $PEPINSTER);
        $PARK6 = new Park('087/12.15.69','Rue des Clochers','56','4861','Soiron', $PEPINSTER);
        $PARK7 = new Park('04/355.25.19','Rue de la Fosse','78','4620','Fléron', $FLERON);
        $PARK8 = new Park('04/355.78.61','Rue des Gaillettes','95','4624','Romsée', $FLERON);
        $PARK9 = new Park('087/69.36.56','Rue Jean-Marie Sitter','45','4650','Herve', $HERVE);
        $PARK10 = new Park('087/69.12.13','Avenue du Cardon','9','4652','Xhendelesse', $HERVE);

        $JARDIN = new WasteType('déchets de jardin', 13);
        $ENCOMBRANT = new WasteType('encombrants',4);
        $BOIS = new WasteType('bois',3);
        $BRIQUE = new WasteType('briques et briquaillons',2.5);
        $TERRE = new WasteType('terres et sables',2.5);
        $METAUX = new WasteType('métaux','');
        $PAPIER = new WasteType('papiers et cartons','');

        $CONTAINER1 = new Container('50',0,$JARDIN,$PARK1);
        $CONTAINER2 = new Container('40',0,$JARDIN,$PARK1);
        $CONTAINER3 = new Container('50',0,$ENCOMBRANT,$PARK1);
        $CONTAINER4 = new Container('25',0,$ENCOMBRANT,$PARK1);
        $CONTAINER5 = new Container('40',0,$BOIS,$PARK1);
        $CONTAINER6 = new Container('50',0,$BRIQUE,$PARK1);
        $CONTAINER7 = new Container('40',0,$TERRE,$PARK1);
        $CONTAINER8 = new Container('50',0,$METAUX,$PARK1);
        $CONTAINER9 = new Container('40',0,$PAPIER,$PARK1);
        $CONTAINER10 = new Container('25',0,$PAPIER,$PARK1);

        if(count($this->getDoctrine()->getRepository('AppBundle:Commune')->findAll()) === 0){
            $em->persist($LIEGE);
            $em->persist($PEPINSTER);
            $em->persist($FLERON);
            $em->persist($HERVE);
        }
        if(count($this->getDoctrine()->getRepository('AppBundle:Role')->findAll()) === 0){
            $em->persist($CEO);
            $em->persist($GERANT);
            $em->persist($EMPLOYE);
            $em->persist($MENAGE);
        }
        if(count($this->getDoctrine()->getRepository('AppBundle:Park')->findAll()) === 0){
            $em->persist($PARK1);
            $em->persist($PARK2);
            $em->persist($PARK3);
            $em->persist($PARK4);
            $em->persist($PARK5);
            $em->persist($PARK6);
            $em->persist($PARK7);
            $em->persist($PARK8);
            $em->persist($PARK9);
            $em->persist($PARK10);
        }
        if(count($this->getDoctrine()->getRepository('AppBundle:User')->findAll()) === 0){
            $userManager->updateUser(new User(
                    'dylan','d.danse@recyzone.be','dylan',
                    'Dylan', 'Danse',
                    '','','','','',
                    '','', $EMPLOYE, $PARK1)
            );

            $userManager->updateUser(new User(
                    'azerty','g.herant@recyzone.be','gherant',
                    'Guillaume', 'Hérant',
                    '','','','','',
                    '','', $CEO, $PARK1)
            );

            $userManager->updateUser(new User(
                    'azerty','l.joiris@recyzone.be','ljoiris',
                    'Joiris', 'Luc',
                    '','','','','',
                    '','', $GERANT, $PARK1)
            );
        }
        if(count($this->getDoctrine()->getRepository('AppBundle:WasteType')->findAll()) === 0){
            $em->persist($JARDIN);
            $em->persist($ENCOMBRANT);
            $em->persist($BOIS);
            $em->persist($BRIQUE);
            $em->persist($TERRE);
            $em->persist($METAUX);
            $em->persist($PAPIER);
        }
        if(count($this->getDoctrine()->getRepository('AppBundle:Container')->findAll()) === 0){
            $em->persist($CONTAINER1);
            $em->persist($CONTAINER2);
            $em->persist($CONTAINER3);
            $em->persist($CONTAINER4);
            $em->persist($CONTAINER5);
            $em->persist($CONTAINER6);
            $em->persist($CONTAINER7);
            $em->persist($CONTAINER8);
            $em->persist($CONTAINER9);
            $em->persist($CONTAINER10);
        }

        $em->flush();
        return new Response('DB successfully filled !',200);
    }
}
