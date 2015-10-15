<?php
// src/Blogger/BlogBundle/Controller/PostController.php

namespace Blogger\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Blogger\BlogBundle\Entity\Blog;
use Blogger\BlogBundle\Form\BlogType;

use Blogger\BlogBundle\Entity\Picture;
use Blogger\BlogBundle\Form\PictureType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Comment controller.
 */
class PostController extends Controller
{
    public function newAction()
    {
        $blog = new Blog();
        $post_form = $this->createForm(new BlogType(), $blog);
        
        $picture = new Picture();
        $picture_form = $this->createForm(new PictureType(), $picture);
        
        return $this->render('BloggerBlogBundle:Upload:show.html.twig', array(
            'post_form' => $post_form->createView(),
            'picture_form' => $picture_form->createView()    
        ));        
    }
    
    public function createAction($picture_id)
    {
        $blog = new Blog();
        $blog->setImage($picture_id);
        $post_form = $this->createForm(new BlogType(), $blog);

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $post_form->bind($request);

            if ($post_form->isValid()) {
            
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
            'post_form' => $post_form->createView()
        ));
    }
    
    
    /**
 * @Template()
 */
    public function uploadAction()
    {
        $picture = new Picture();
        $picture_form = $this->createForm(new PictureType(), $picture);

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $picture_form->bind($request);

            if ($picture_form->isValid()) {
            
                $em = $this->getDoctrine()
                       ->getManager();
                $em->persist($picture);
                $em->flush();
            
                $this->get('session')->getFlashBag()->add('picture-notice', 'Your picture was successfully updated. Thank you!');
                // Redirige - Esto es importante para prevenir que el usuario
                // reenvíe el formulario si actualiza la página
                return $this->redirect($this->generateUrl('BloggerBlogBundle_create_post'), array(
                    'picture_id'    => $picture->getId(),
                ));
            }
        }

        return $this->render('BloggerBlogBundle:Post:show.html.twig', array(
            'picture_form' => $picture_form->createView()
        ));
    }
    //sólo me aparece el formulario del blog
    //cómo hacer que en la plantilla twig el botón upload image sea para
    //enviar el formulario de la imagen y el de submit para el del post entero
    //en mi base de datos ya tengo creado el campo picture
    
    /**
    * @Template()
    */
    /*public function uploadAction()
    {
        $picture = new Picture();
        $form = $this->createFormBuilder($file)
            ->add('name')
            ->add('file')
            ->getForm()
        ;

        if ($this->getRequest()->isMethod('POST')) {
            $form->bind($this->getRequest());
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $em->persist($file);
                $em->flush();

            return $this->redirect($this->generateUrl('BloggerBlogBundle_post'));
            }
        }

        return $this->render('BloggerBlogBundle:Post:show.html.twig', array(
        'form' => $form->createView()
        ));
    }*/
}