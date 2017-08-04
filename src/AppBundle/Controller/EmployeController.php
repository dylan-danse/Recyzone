<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EmployeController extends Controller
{
    /**
     * @Route("/employe", name="employe")
     */
    public function indexAction(Request $request)
    {
        if ($this->getUser() == null){
            return $this->redirect('./login');
        }
        elseif ($this->getUser()->getRole()->getName() != 'Employé'){
            return $this->redirect('./error');
        }
        else{
            return $this->render('employe/index.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            ]);
        }
    }

    /**
     * @Route("/addHousehold", name="addHousehold")
     */
    public function addHousehold(Request $request)
    {
        if ($this->getUser() == null){
            return $this->redirect('./login');
        }
        elseif ($this->getUser()->getRole()->getName() != 'Employé'){
            return $this->redirect('./error');
        }
        else{

            $household = new User();
            $form = $this->createFormBuilder($household)
                ->add('firstname', TextType::class, array('attr' => array('class' => 'form-control')))
                ->add('lastName', TextType::class, array('attr' => array('class' => 'form-control')))
                ->add('email', EmailType::class, array('attr' => array('class' => 'form-control')))
                ->add('save', SubmitType::class, array('label' => 'Create Household', 'attr' => array('class' => 'btn btn-primary')))
                ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()){

                $firstname = $form['firstname']->getData();
                $lastName = $form['lastName']->getData();
                $email = $form['email']->getData();

                $userManager = $this->container->get('fos_user.user_manager');
                $userAdmin = $userManager->createUser();

                $userAdmin->setUsername(explode("@", $email, 2)[0]);
                $userAdmin->setEmail($email);
                $userAdmin->setPlainPassword('random'); // TODO : replace with random generation
                $userAdmin->setEnabled(true);
                $userAdmin->setFirstName($firstname);
                $userAdmin->setLastName($firstname);

                $userManager->updateUser($userAdmin, $lastName);

                //return $this->redirectToRoute('employe');
            }

            return $this->render('employe/addHousehold.html.twig', array(
                'form' => $form->createView()
            ));
        }
    }

    /**
     * @Route("/makeDeposit", name="makeDeposit")
     */
    public function makeDeposit(Request $request)
    {
        if ($this->getUser() == null){
            return $this->redirect('./login');
        }
        elseif ($this->getUser()->getRole()->getName() != 'Employé'){
            return $this->redirect('./error');
        }
        else{
            $userManager = $this->container->get('fos_user.user_manager');
            $users = $userManager->findUsers();
            return $this->render('employe/makeDeposit.html.twig', array(
                'users' => $users
            ));
        }
    }

}
