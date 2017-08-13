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
        $METAUX = new WasteType('métaux',null);
        $PAPIER = new WasteType('papiers et cartons',null);

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

        $CONTAINER11 = new Container('50',0,$JARDIN,$PARK2);
        $CONTAINER12 = new Container('40',0,$ENCOMBRANT,$PARK2);
        $CONTAINER13 = new Container('50',0,$BOIS,$PARK2);
        $CONTAINER14 = new Container('25',0,$BRIQUE,$PARK2);
        $CONTAINER15 = new Container('40',0,$TERRE,$PARK2);
        $CONTAINER16 = new Container('50',0,$METAUX,$PARK2);
        $CONTAINER17 = new Container('40',0,$PAPIER,$PARK2);

        $CONTAINER18 = new Container('40',0,$JARDIN,$PARK3);
        $CONTAINER19 = new Container('40',0,$ENCOMBRANT,$PARK3);
        $CONTAINER20 = new Container('40',0,$BOIS,$PARK3);
        $CONTAINER21 = new Container('25',0,$BRIQUE,$PARK3);
        $CONTAINER22 = new Container('40',0,$TERRE,$PARK3);
        $CONTAINER23 = new Container('50',0,$METAUX,$PARK3);
        $CONTAINER24 = new Container('60',0,$PAPIER,$PARK3);

        $CONTAINER25 = new Container('40',0,$JARDIN,$PARK4);
        $CONTAINER26 = new Container('40',0,$JARDIN,$PARK4);
        $CONTAINER27 = new Container('40',0,$ENCOMBRANT,$PARK4);
        $CONTAINER28 = new Container('25',0,$BOIS,$PARK4);
        $CONTAINER29 = new Container('40',0,$BRIQUE,$PARK4);
        $CONTAINER30 = new Container('50',0,$TERRE,$PARK4);
        $CONTAINER31 = new Container('40',0,$METAUX,$PARK4);
        $CONTAINER32 = new Container('25',0,$PAPIER,$PARK4);

        $CONTAINER33 = new Container('40',0,$JARDIN,$PARK5);
        $CONTAINER34 = new Container('40',0,$ENCOMBRANT,$PARK5);
        $CONTAINER35 = new Container('25',0,$ENCOMBRANT,$PARK5);
        $CONTAINER36 = new Container('25',0,$BOIS,$PARK5);
        $CONTAINER37 = new Container('40',0,$BRIQUE,$PARK5);
        $CONTAINER38 = new Container('50',0,$TERRE,$PARK5);
        $CONTAINER39 = new Container('40',0,$METAUX,$PARK5);
        $CONTAINER40 = new Container('40',0,$PAPIER,$PARK5);

        $CONTAINER41 = new Container('40',0,$JARDIN,$PARK6);
        $CONTAINER42 = new Container('40',0,$JARDIN,$PARK6);
        $CONTAINER43 = new Container('25',0,$ENCOMBRANT,$PARK6);
        $CONTAINER44 = new Container('25',0,$ENCOMBRANT,$PARK6);
        $CONTAINER45 = new Container('40',0,$BOIS,$PARK6);
        $CONTAINER46 = new Container('50',0,$BRIQUE,$PARK6);
        $CONTAINER47 = new Container('40',0,$TERRE,$PARK6);
        $CONTAINER48 = new Container('40',0,$METAUX,$PARK6);
        $CONTAINER49 = new Container('40',0,$PAPIER,$PARK6);

        $CONTAINER50 = new Container('40',0,$JARDIN,$PARK7);
        $CONTAINER51 = new Container('40',0,$JARDIN,$PARK7);
        $CONTAINER52 = new Container('25',0,$ENCOMBRANT,$PARK7);
        $CONTAINER53 = new Container('25',0,$ENCOMBRANT,$PARK7);
        $CONTAINER54 = new Container('40',0,$BOIS,$PARK7);
        $CONTAINER55 = new Container('50',0,$BRIQUE,$PARK7);
        $CONTAINER56 = new Container('40',0,$TERRE,$PARK7);
        $CONTAINER57 = new Container('40',0,$METAUX,$PARK7);
        $CONTAINER58 = new Container('40',0,$PAPIER,$PARK7);

        $CONTAINER59 = new Container('40',0,$JARDIN,$PARK8);
        $CONTAINER60 = new Container('40',0,$JARDIN,$PARK8);
        $CONTAINER61 = new Container('25',0,$ENCOMBRANT,$PARK8);
        $CONTAINER62 = new Container('25',0,$BOIS,$PARK8);
        $CONTAINER63 = new Container('40',0,$BOIS,$PARK8);
        $CONTAINER64 = new Container('50',0,$BRIQUE,$PARK8);
        $CONTAINER65 = new Container('40',0,$TERRE,$PARK8);
        $CONTAINER66 = new Container('40',0,$METAUX,$PARK8);
        $CONTAINER67 = new Container('40',0,$PAPIER,$PARK8);

        $CONTAINER68 = new Container('40',0,$JARDIN,$PARK9);
        $CONTAINER69 = new Container('40',0,$JARDIN,$PARK9);
        $CONTAINER70 = new Container('25',0,$ENCOMBRANT,$PARK9);
        $CONTAINER71 = new Container('25',0,$BOIS,$PARK9);
        $CONTAINER72 = new Container('40',0,$BOIS,$PARK9);
        $CONTAINER73 = new Container('50',0,$BRIQUE,$PARK9);
        $CONTAINER74 = new Container('40',0,$TERRE,$PARK9);
        $CONTAINER75 = new Container('50',0,$METAUX,$PARK9);
        $CONTAINER76 = new Container('40',0,$PAPIER,$PARK9);

        $CONTAINER77 = new Container('40',0,$JARDIN,$PARK10);
        $CONTAINER78 = new Container('40',0,$JARDIN,$PARK10);
        $CONTAINER79 = new Container('25',0,$ENCOMBRANT,$PARK10);
        $CONTAINER80 = new Container('25',0,$BOIS,$PARK10);
        $CONTAINER81 = new Container('40',0,$BOIS,$PARK10);
        $CONTAINER82 = new Container('50',0,$BRIQUE,$PARK10);
        $CONTAINER83 = new Container('40',0,$TERRE,$PARK10);
        $CONTAINER84 = new Container('50',0,$METAUX,$PARK10);
        $CONTAINER85 = new Container('40',0,$PAPIER,$PARK10);


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

            /* CEO */

            $userManager->updateUser(new User(
                    'dylan','d.danse@recyzone.be','dylan',
                    'Dylan', 'Danse',
                    '','','','','',
                    '',0,0, $EMPLOYE, $PARK1)
            );

            $userManager->updateUser(new User(
                    'azerty','g.herant@recyzone.be','gherant',
                    'Guillaume', 'Hérant',
                    '','','','','',
                    '',0,0, $CEO, $PARK1)
            );

            /* PARK 1 */
            $userManager->updateUser(new User(
                    'azerty','l.joiris@recyzone.be','ljoiris',
                    'Joiris', 'Luc',
                    '','','','','',
                    '',0,0, $GERANT, $PARK1)
            );

            $userManager->updateUser(new User(
                    'azerty','g.turbal@recyzone.be','gturbal',
                    'Turbal', 'Guy',
                    '','','','','',
                    '',0,0, $EMPLOYE, $PARK1)
            );

            $userManager->updateUser(new User(
                    'azerty','j.doe@recyzone.be','jdoe',
                    'John', 'Doe',
                    '','','','','',
                    '',0,0, $EMPLOYE, $PARK1)
            );

            /* PARK 2 */
/*
            - Janlet, Myriam, m.janlet@recyzone.be, mjanlet, azerty ,2, gérant
            - Hurot, Martin, m.hurot@recyzone.be, mhurot, azerty ,2, employé
            - Bichat, Pauline, p.bichat@recyzone.be, pbichat, azerty ,2, employé
            - Dutour, Sophie, s.dutour@recyzone.be, sdutour, azerty ,3, gérant
            - Janlet, Marc, ma.janlet@recyzone.be, majanlet, azerty ,3, employé
            - Brasson, Guillaume, g.brasson@recyzone.be, gbrasson, azerty ,3, employé
            - Oligat, Marc, m.oligat@recyzone.be, moligat, azerty ,4, gérant
            - Krupper, Mario, m.krupper@recyzone.be, mkrupper, azerty ,4, employé
            - Vandeberg, Pierre, p.vandeberg@recyzone.be, pvandeberg, azerty ,5, gérant
            - Sitro, Maria, m.sitro@recyzone.be, msitro, azerty ,5, employé
            - Jaminet, Paul, p.jaminet@recyzone.be, pjaminet, azerty ,6, gérant
            - Bryon, Sylvie, s.bryon@recyzone.be, sbryon, azerty ,6, employé
            - Lacasse, Martine, m.lacasse@recyzone.be, mlacasse, azerty ,7, gérant
            - Paulus, Sergio, s.paulus@recyzone.be, spaulus, azerty ,7, employé
            - Drion, Brigitte, b.drion@recyzone.be, bdrion, azerty ,8, gérant
            - Demoulin, Serge, s.demoulin@recyzone.be, sdemoulin, azerty ,8, employé
            - Duchamp, Pierre, p.duchamp@recyzone.be, pduchamp, azerty ,9, gérant
            - Franzi, Marc, m.franzi@recyzone.be, mfranzi, azerty ,9, employé
            - Douagou, Marceline, m.douagou@recyzone.be, mdouagou, azerty ,10, gérant
            - Duroux, Nathalie, n.duroux@recyzone.be, nduroux, azerty ,10, employé
*/
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
            $em->persist($CONTAINER11);
            $em->persist($CONTAINER12);
            $em->persist($CONTAINER13);
            $em->persist($CONTAINER14);
            $em->persist($CONTAINER15);
            $em->persist($CONTAINER16);
            $em->persist($CONTAINER17);
            $em->persist($CONTAINER18);
            $em->persist($CONTAINER19);
            $em->persist($CONTAINER20);
            $em->persist($CONTAINER21);
            $em->persist($CONTAINER22);
            $em->persist($CONTAINER23);
            $em->persist($CONTAINER24);
            $em->persist($CONTAINER25);
            $em->persist($CONTAINER26);
            $em->persist($CONTAINER27);
            $em->persist($CONTAINER28);
            $em->persist($CONTAINER29);
            $em->persist($CONTAINER30);
            $em->persist($CONTAINER31);
            $em->persist($CONTAINER32);
            $em->persist($CONTAINER33);
            $em->persist($CONTAINER34);
            $em->persist($CONTAINER35);
            $em->persist($CONTAINER36);
            $em->persist($CONTAINER37);
            $em->persist($CONTAINER38);
            $em->persist($CONTAINER39);
            $em->persist($CONTAINER40);
            $em->persist($CONTAINER41);
            $em->persist($CONTAINER42);
            $em->persist($CONTAINER43);
            $em->persist($CONTAINER44);
            $em->persist($CONTAINER45);
            $em->persist($CONTAINER46);
            $em->persist($CONTAINER47);
            $em->persist($CONTAINER48);
            $em->persist($CONTAINER49);
            $em->persist($CONTAINER50);
            $em->persist($CONTAINER51);
            $em->persist($CONTAINER52);
            $em->persist($CONTAINER53);
            $em->persist($CONTAINER54);
            $em->persist($CONTAINER55);
            $em->persist($CONTAINER56);
            $em->persist($CONTAINER57);
            $em->persist($CONTAINER58);
            $em->persist($CONTAINER59);
            $em->persist($CONTAINER60);
            $em->persist($CONTAINER61);
            $em->persist($CONTAINER62);
            $em->persist($CONTAINER63);
            $em->persist($CONTAINER64);
            $em->persist($CONTAINER65);
            $em->persist($CONTAINER66);
            $em->persist($CONTAINER67);
            $em->persist($CONTAINER68);
            $em->persist($CONTAINER69);
            $em->persist($CONTAINER70);
            $em->persist($CONTAINER71);
            $em->persist($CONTAINER72);
            $em->persist($CONTAINER73);
            $em->persist($CONTAINER74);
            $em->persist($CONTAINER75);
            $em->persist($CONTAINER76);
            $em->persist($CONTAINER77);
            $em->persist($CONTAINER78);
            $em->persist($CONTAINER79);
            $em->persist($CONTAINER80);
            $em->persist($CONTAINER81);
            $em->persist($CONTAINER82);
            $em->persist($CONTAINER83);
            $em->persist($CONTAINER84);
            $em->persist($CONTAINER85);

        }

        $em->flush();
        return new Response('DB successfully filled !',200);
    }
}
