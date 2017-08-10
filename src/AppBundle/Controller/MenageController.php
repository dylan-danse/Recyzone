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

        $sql = " 
            SELECT w.name as type, 
                    (SELECT volume FROM quota where waste_type_id = w.id) as total,
                    (SELECT SUM(quantity) FROM deposit where waste_type_id = w.id) as deposed
            FROM waste_type w
            left JOIN deposit d on w.id = d.waste_type_id
            left join fos_user u on u.id = d.household_id
            left join quota q on u.id = q.user_id
            GROUP BY w.id
            ";

        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll();

        return $this->render('menage/index.html.twig',array(
            'user' => $user,
            'quotas' => $results
        ));
    }

    /**
     * @Route("/factures", name="factures")
     */
    public function factureAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $bills = $em->getRepository("AppBundle:Bill")->findBy(array("household" => $this->getUser()->getId()));

        return $this->render('menage/factures.html.twig',array(
            'bills' => $bills
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
