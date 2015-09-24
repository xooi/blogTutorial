<?php
// src/Blogger/BlogBundle/Controller/PostController.php

namespace Blogger\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Blogger\BlogBundle\Entity\Blog;
use Blogger\BlogBundle\Form\BlogType;

/**
 * Comment controller.
 */
class PostController extends Controller
{
    /*public function newAction($blog_id)
    {
        $blog = $this->getBlog($blog_id);

        $comment = new Comment();
        $comment->setBlog($blog);
        $form   = $this->createForm(new CommentType(), $comment);

        return $this->render('BloggerBlogBundle:Comment:form.html.twig', array(
            'comment' => $comment,
            'form'   => $form->createView()
        ));
    }

    public function createAction($blog_id)
    {
        $blog = $this->getBlog($blog_id);

        $comment  = new Comment();
        $comment->setBlog($blog);
        $request = $this->getRequest();
        $form    = $this->createForm(new CommentType(), $comment);
        $form->bindRequest($request);

        if ($form->isValid()) {
            // PENDIENTE: Persistir la entidad comentario

            return $this->redirect($this->generateUrl('BloggerBlogBundle_blog_show', array(
                'id' => $comment->getBlog()->getId())) .
                '#comment-' . $comment->getId()
            );
        }

        return $this->render('BloggerBlogBundle:Comment:create.html.twig', array(
            'comment' => $comment,
            'form'    => $form->createView()
        ));
    }

    protected function getBlog($blog_id)
    {
        $em = $this->getDoctrine()
                    ->getEntityManager();

        $blog = $em->getRepository('BloggerBlogBundle:Blog')->find($blog_id);

        if (!$blog) {
            throw $this->createNotFoundException('Unable to find Blog post.');
        }

        return $blog;
    }*/
    
    public function createAction()
    {
    $blog = new Blog();
    $form = $this->createForm(new BlogType(), $blog);

    $request = $this->getRequest();
    if ($request->getMethod() == 'POST') {
        $form->bind($request);

        if ($form->isValid()) {
            
            $em = $this->getDoctrine()
                       ->getManager();
            $em->persist($blog);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('post-notice', 'Your post was successfully create. Thank you!');
            // Redirige - Esto es importante para prevenir que el usuario
            // reenvíe el formulario si actualiza la página
            return $this->redirect($this->generateUrl('BloggerBlogBundle_post'));
        }
    }

    return $this->render('BloggerBlogBundle:Post:show.html.twig', array(
        'form' => $form->createView()
    ));
    }
    
    /*public function newAction()
    {
        $post = new Blog();
        $form = $this->createForm(new BlogType(), $post);
        
        return $this->render('BloggerBlogBundle:Comment:form.html.twig', array(
            'comment' => $comment,
            'form'   => $form->createView()
        ));
    }*/
}