<?php
// src/Blogger/BlogBundle/Controller/PageController.php

namespace Blogger\BlogBundle\Controller;

use Blogger\BlogBundle\Entity\Enquiry;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
// Importa el nuevo espacio de nombres

use Blogger\BlogBundle\Form\EnquiryType;

class PageController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()
                   ->getManager();

        $blogs = $em->getRepository('BloggerBlogBundle:Blog')
                    ->getLatestBlogs();
  
        return $this->render('BloggerBlogBundle:Page:index.html.twig', array(
            'blogs' => $blogs
        ));
    }
    
    public function aboutAction()
    {
        return $this->render('BloggerBlogBundle:Page:about.html.twig');
    }  
    
    public function contactAction()
    {
    $enquiry = new Enquiry();
    $form = $this->createForm(new EnquiryType(), $enquiry);

    $request = $this->getRequest();
    if ($request->getMethod() == 'POST') {
        $form->bind($request);

        if ($form->isValid()) {
            // realiza alguna acción, como enviar un correo electrónico
            
            $message = \Swift_Message::newInstance()
            ->setSubject('Contact enquiry from symblog')
            ->setFrom('enquiries@symblog.co.uk')
            ->setTo($this->container->getParameter('blogger_blog.emails.contact_email'))
            ->setBody($this->renderView('BloggerBlogBundle:Page:contactEmail.txt.twig', array('enquiry' => $enquiry)));
            $this->get('mailer')->send($message);

            $this->get('session')->getFlashBag()->add('blogger-notice', 'Your contact enquiry was successfully sent. Thank you!');
            // Redirige - Esto es importante para prevenir que el usuario
            // reenvíe el formulario si actualiza la página
            return $this->redirect($this->generateUrl('BloggerBlogBundle_contact'));
        }
    }

    return $this->render('BloggerBlogBundle:Page:contact.html.twig', array(
        'form' => $form->createView()
    ));
    }
    
   public function sidebarAction()
    {
    $em = $this->getDoctrine()
               ->getManager();

    $tags = $em->getRepository('BloggerBlogBundle:Blog')
               ->getTags();

    $tagWeights = $em->getRepository('BloggerBlogBundle:Blog')
                     ->getTagWeights($tags);
    
    $commentLimit   = $this->container
                           ->getParameter('blogger_blog.comments.latest_comment_limit');
    $latestComments = $em->getRepository('BloggerBlogBundle:Comment')
                         ->getLatestComments($commentLimit);

    return $this->render('BloggerBlogBundle:Page:sidebar.html.twig', array(
        'latestComments'    => $latestComments,
        'tags' => $tagWeights
    ));
    } 
    
    /*public function postAction()
    {
    return $this->render('BloggerBlogBundle:Post:show.html.twig');
    }*/
}
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

