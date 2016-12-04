<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TodoController extends Controller
{
    /**
     * @Route("/todo", name="todo_list")
     */
    public function indexAction()
    {
        $todos = $this->getDoctrine()
                ->getRepository('AppBundle:Todo')
                ->findAll();
        
        return $this->render('todo/index.html.twig', array(
            'todos' => $todos
        ));
    }
    
    /**
     * @Route("/todo/create", name="todo_create")
     */
    public function createAction(Request $request)
    {
        $todo = new \AppBundle\Entity\Todo();
        $atrributes = array('class' => 'form-control' , 'style' => 'margin-bottom:15px');
        $choices = array('Low' => 'Low', 'Normal' => 'Normal', 'High' => 'High');
        $form = $this->createFormBuilder($todo)
                ->add('name', TextType::class, array('attr' => $atrributes))
                ->add('category', TextType::class, array('attr' => $atrributes))
                ->add('description', TextareaType::class, array('attr' => $atrributes))
                ->add('priority', ChoiceType::class, array('choices' => $choices, 'attr' => $atrributes))
                ->add('due_date', DateTimeType::class, array('attr' => array('style' => 'margin-bottom:15px')))
                ->add('save', SubmitType::class, array('label' => 'Create Todo', 'attr' => array('class' => 'btn btn-primary')))
                ->getForm();
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            $todo->setName($form['name']->getData());
            $todo->setCategory($form['category']->getData());
            $todo->setDescription($form['description']->getData());
            $todo->setPriority($form['priority']->getData());
            $todo->setDueDate($form['due_date']->getData());
            $todo->setCreateDate(new \DateTime('now'));
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($todo);
            $em->flush();
            
            $this->addFlash('notice', 'Todo Added');
            
            return $this->redirectToRoute('todo_list');
        }
        
        return $this->render('todo/create.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    /**
     * @Route("/todo/edit/{id}", name="todo_edit")
     */
    public function editAction($id, Request $request)
    {
        $todo = $this->getDoctrine()
                ->getRepository('AppBundle:Todo')
                ->find($id);
        
        if (empty($todo)) {
            $this->addFlash('error', 'Todo not found');
            
            return $this->redirectToRoute('todo_list');
        }
        
        $atrributes = array('class' => 'form-control' , 'style' => 'margin-bottom:15px');
        $choices = array('Low' => 'Low', 'Normal' => 'Normal', 'High' => 'High');
        $form = $this->createFormBuilder($todo)
                ->add('name', TextType::class, array('attr' => $atrributes))
                ->add('category', TextType::class, array('attr' => $atrributes))
                ->add('description', TextareaType::class, array('attr' => $atrributes))
                ->add('priority', ChoiceType::class, array('choices' => $choices, 'attr' => $atrributes))
                ->add('due_date', DateTimeType::class, array('attr' => array('style' => 'margin-bottom:15px')))
                ->add('save', SubmitType::class, array('label' => 'Update Todo', 'attr' => array('class' => 'btn btn-primary')))
                ->getForm();
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            $todo->setName($form['name']->getData());
            $todo->setCategory($form['category']->getData());
            $todo->setDescription($form['description']->getData());
            $todo->setPriority($form['priority']->getData());
            $todo->setDueDate($form['due_date']->getData());
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($todo);
            $em->flush();
            
            $this->addFlash('notice', 'Todo updated');
            
            return $this->redirectToRoute('todo_list');
        }
        
        return $this->render('todo/edit.html.twig', array(
            'form' => $form->createView(),
            'todo' => $todo
        ));
    }
    
    /**
     * @Route("/todo/details/{id}", name="todo_details")
     */
    public function detailsAction($id)
    {
        $todo = $this->getDoctrine()
                ->getRepository('AppBundle:Todo')
                ->find($id);
        if (empty($todo)) {
            $this->addFlash('error', 'Todo not found');
            
            return $this->redirectToRoute('todo_list');
        }
        
        return $this->render('todo/detail.html.twig', array(
            'todo' => $todo
        ));
    }

    /**
     * @Route("/todo/delete/{id}", name="todo_delete")
     */
    public function deleteAction($id)
    {
        $todo = $this->getDoctrine()
                ->getRepository('AppBundle:Todo')
                ->find($id);
        
        if (empty($todo)) {
            $this->addFlash('error', 'Todo not found');
            
            return $this->redirectToRoute('todo_list');
        }
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($todo);
        $em->flush();
        
        $this->addFlash('notice', 'Todo removed');
       
        return $this->redirectToRoute('todo_list');
    }
}
