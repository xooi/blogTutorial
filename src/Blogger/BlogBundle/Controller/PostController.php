<?php
// src/Blogger/BlogBundle/Controller/PostController.php

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
class PostController extends Controller
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
    public function create_postAction()
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
                return $this->redirect($this->generateUrl('BloggerBlogBundle_upload_image', array(
                    'blog_id'    => $blog->getId()
                )));
            }
        }
        return $this->render('BloggerBlogBundle:Post:show.html.twig', array(
        'post_form' => $post_form->createView()
        ));
    }
    
    //muestra el formulario foto y la sube y relaciona con el blog
    public function upload_imageAction($blog_id)
    {
        $blog = $this->getBlog($blog_id);

        $picture  = new Picture();
        $picture->setBlog($blog);
        $picture_form = $this->createForm(new PictureType(), $picture);
        $request = $this->getRequest();
        $picture_form->handleRequest($request);

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
        
        return $this->render('BloggerBlogBundle:Image:show.html.twig', array(
            'blog_id'=> $blog_id,
            'picture_form' => $picture_form->createView()
        ));
    }
        
    public function edit_image_showAction($blog_id)
    {
        $post = $this->getBlog($blog_id);
        $picture_id = $post->getImage()->getId();
        
        $picture = new Picture();
        $picture_form = $this->createForm(new PictureType(), $picture);
        
        return $this->render('BloggerBlogBundle:Image_Edit:show.html.twig', array(
            'picture_id'   => $picture_id,
            'picture_form' => $picture_form->createView(),
            'id'    => $blog_id,
            'slug'  => $post->getSlug()
        ));
    }
    
    public function edit_imageAction($blog_id)
    {
        //obtengo el picture_id para poder borrar la foto antigua
        $em = $this->getDoctrine()->getManager();
        $blog = $em->getRepository('BloggerBlogBundle:Blog')->find($blog_id);       
        $picture_id = $blog->getImage()->getId();       
        
        
        //obtengo la foto y la borro
        $pictureOld = $em->getRepository('BloggerBlogBundle:Picture')->findOneById($picture_id);
        $em->remove($pictureOld);
        $em->flush();
              

        $picture  = new Picture();
        //seteo el blog para relacionarlo
        $picture->setBlog($blog);
        $picture_form = $this->createForm(new PictureType(), $picture);
        $request = $this->getRequest();
        $picture_form->bind($request);

        if ($picture_form->isValid()) {
            $em->persist($picture);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('picture-notice', 'Your picture was successfully updated. Thank you!');

            return $this->redirect($this->generateUrl('BloggerBlogBundle_blog_show', array(
                 'id'    => $blog->getId(),
                 'slug'  => $blog->getSlug()))
            );
        }
        
        return $this->render('BloggerBlogBundle:Upload:show.html.twig', array(
            'blog_id'=> $blog_id,
            'picture_form' => $picture_form->createView()
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
            $this->get('session')->getFlashBag()->add('post-edit-notice', 'Your post was successfully edit. Choose new image or continue with previous');
            //Redirigir a editar imagen
            return $this->redirect($this->generateUrl('BloggerBlogBundle_edit_image_show', array(
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