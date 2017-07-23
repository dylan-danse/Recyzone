<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Test;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TestController extends Controller
{
    /**
     * @Route("/test", name="test")
     */
    public function indexAction(Request $request)
    {
        $tests = $this->getDoctrine()
            ->getRepository('AppBundle:Test')
            ->findAll();
        return $this->render('test/index.html.twig', array(
            'tests' => $tests
        ));
    }

    /**
     * @Route("/createTest", name="createTest")
     */
    public function createAction(Request $request)
    {
        $test = new Test();

        $form = $this->createFormBuilder($test)
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('description', TextareaType::class, array('attr' => array('class' => 'form-control')))
            ->add('creationDate', DateTimeType::class, array('attr' => array('class' => 'form-control')))
            ->add('save', SubmitType::class, array('label' => 'Create Test', 'attr' => array('class' => 'btn btn-primary')))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $name = $form['name']->getData();
            $description = $form['description']->getData();
            $creationDate = $form['creationDate']->getData();

            $test->setName($name);
            $test->setDescription($description);
            $test->setCreationDate($creationDate);

            $em = $this->getDoctrine()->getManager();
            $em->persist($test);
            $em->flush();

            $this->addFlash(
              'notice',
              'TODO ADDED'
            );

            return $this->redirectToRoute('test');
        }

        return $this->render('test/create.html.twig', array(
            'form' => $form->createView()
            ));
    }
}
