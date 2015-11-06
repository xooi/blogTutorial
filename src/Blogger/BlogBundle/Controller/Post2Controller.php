<?php
// src/Blogger/BlogBundle/Controller/Post2Controller.php

namespace Blogger\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Blogger\BlogBundle\Entity\Blog;
use Blogger\BlogBundle\Form\BlogType;

use Blogger\BlogBundle\Entity\Picture;
use Blogger\BlogBundle\Form\PictureType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;

/**
 * Comment controller.
 */
class Post2Controller extends Controller
{
    //muestra el formulario post
    public function newAction()
    {
        
        $blog = new Blog();
        $post_form = $this->createForm(new BlogType(), $blog);
        
        return $this->render('BloggerBlogBundle:Post:show.html.twig', array(
            'post_form' => $post_form->createView()
        ));        
    }
    
    //crea el post y redirecciona a subir foto
    public function createAction()
    {      
        
        $blog = new Blog();
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
                return $this->redirect($this->generateUrl('BloggerBlogBundle_upload_image2', array(
                    'blog_id'    => $blog->getId()
                )));
            }
        }
    }
    
    //muestra el formulario foto y la sube y relaciona con el blog
    public function uploadAction($blog_id)
    {
        $blog = $this->getBlog($blog_id);

        $picture  = new Picture();
        $picture->setBlog($blog);
        $request = $this->getRequest();
        $picture_form = $this->createForm(new PictureType(), $picture);
        $picture_form->bind($request);

        if ($picture_form->isValid()) {
            $em = $this->getDoctrine()
                       ->getManager();
            $em->persist($picture);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('picture-notice', 'Your picture was successfully updated. Thank you!');

            return $this->redirect($this->generateUrl('BloggerBlogBundle_blog_show', array(
                 'id'    => $picture->getBlog()->getId(),
                 'slug'  => $picture->getBlog()->getSlug()))
            );
        }
        
        return $this->render('BloggerBlogBundle:Upload:show.html.twig', array(
            'blog_id'=> $blog_id,
            'picture_form' => $picture_form->createView()
        ));
    }
        //////
        /*$picture = new Picture();
        $picture_form = $this->createForm(new PictureType(), $picture);

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $picture_form->bind($request);

            if ($picture_form->isValid()) {
            
                $em = $this->getDoctrine()
                       ->getManager();
                
                $em->persist($picture);
                $em->flush();
                
                //una vez tengo la imagen persistida
                //seteo la imagen al blog
                $em = $this->getDoctrine()
                    ->getManager();

                $blog = $em->getRepository('BloggerBlogBundle:Blog')->find($blog_id);

                if (!$blog) {
                throw $this->createNotFoundException('Unable to find Blog post.');
                }
                
                $blog->setImage($picture);
                //
                //seteo la blog_id de la imagen a la id del post
                

                $this->get('session')->getFlashBag()->add('picture-notice', 'Your picture was successfully updated. Thank you!');
               
                return $this->redirect($this->generateUrl('BloggerBlogBundle_homepage'));
            }
        }
        
        return $this->render('BloggerBlogBundle:Upload:show.html.twig', array(
            'blog_id'=> $blog_id,
            'picture_form' => $picture_form->createView()
        ));*/
    
    public function edit_imageAction($blog_id)
    {
        $post = $this->getBlog($blog_id);
        $picture_id = $post->getImage()->getId();
       
        
        $em = $this->getDoctrine()
                    ->getManager();

        $pictureOld = $em->getRepository('BloggerBlogBundle:Picture')->findOneById($picture_id);
        $em->remove($pictureOld);
        $em->flush();
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

            return $this->redirect($this->generateUrl('BloggerBlogBundle_blog_show', array(
                 'id'    => $picture->getBlog()->getId(),
                 'slug'  => $picture->getBlog()->getSlug()))
            );
        }
        
        return $this->render('BloggerBlogBundle:Edit:show.html.twig', array(
            'blog_id'=> $blog_id,
            'picture_form' => $picture_form->createView()
        ));  
    }
    }
    
    public function edit2Action($blog_id)
    {
        //Entity Manager
        $em = $this->getDoctrine()->getManager();
        
        /*//Repositorios de entidades a utilizar
        $postRepository=$em->getRepository("BloggerBlogBundle:Blog");
        
        //conseguimos el objeto del Blog
        $post = $postRepository->findOneById($blog_id);*/
        $post = $this->getBlog($blog_id);
        
        //Creamos el formulario, asociado a la entidad
        $form = $this->createForm(new BlogType(), $post);
        
        //utilizamos el manejador de peticiones
        $request = $this->getRequest();
        $form->handleRequest($request);
        
        //Si el formulario ha sido enviado
        if ($form->isSubmitted()) {
            //Metemos en variables los datos que llegan desde el formulario
            $title = $form->get('title')->getData();
            $author = $form->get('author')->getData();
            $blog = $form->get('blog')->getData();
            $tags = $form->get('tags')->getData();
            
            //Llamamos a los metodos set de la entidad y les metemos los valores del formulario
            /*$post->setTitle($title);
            $post->setAuthor($author);
            $post->setBlog($blog);
            $post->setTags($tags);*/
        }
 
        //Si el formulario es valido tras aplicar la validacion de la entidad
        if ($form->isValid()) {
            //$post->upload();
            //$persist = $em->persist($post);
            //$flush = $em->flush();
            //$em->persist($post);
            //$em->persist($form->getData());
            $em->flush();
        //Mensaje flash
        $this->get('session')->getFlashBag()->add('post-notice', 'Your post was successfully edit post. Thank you!');
        
        //Redirigir a la home
        return $this->redirect($this->generateUrl('BloggerBlogBundle_edit_image', array(
                    'blog_id'    => $post->getId()
                )));
        }
        
        else{
            //Si el formulario está enviado
            if ($form->isSubmitted()) {   
                //Mensaje flash
                $this->session->getFlashBag()->add('new', 'Rellena correctamente el formulario');
            }
        }
        
        //Renderizar vista
        return $this->render('BloggerBlogBundle:Post:show.html.twig', array(
            'post_form' => $form->createView()
        ));

    }
    
    public function edit_postAction($blog_id)
    {
        //Entity Manager
        $em = $this->getDoctrine()->getManager();
        //a partir del blog_id obtenemos blog
        $post = $this->getBlog($blog_id);
        //creamos un nuevo formulario blog que estará relleno
        $form = $this->createForm(new BlogType(), $post);
        //utilizamos el manejador de peticiones
        $request = $this->getRequest();
        $form->handleRequest($request);
        
        if ($form->isSubmitted()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add('post-notice', 'Your post was successfully edit post. Thank you!');
            //Redirigir a la home
            return $this->redirect($this->generateUrl('BloggerBlogBundle_edit_image', array(
                    'blog_id'    => $post->getId()
                )));
        }
        
        //Renderizar vista
        return $this->render('BloggerBlogBundle:Post_Edit:show.html.twig', array(
            'blog_id'  => $blog_id, 
            'post_form' => $form->createView()
        ));
        
    }
    
    
    protected function getBlog($blog_id)
    {
        $em = $this->getDoctrine()
                    ->getManager();

        $blog = $em->getRepository('BloggerBlogBundle:Blog')->find($blog_id);

        if (!$blog) {
            throw $this->createNotFoundException('Unable to find Blog post.');
        }

        return $blog;
    }
    
}