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
    public function newAction()
    {
        
        $picture = new Picture();
        $picture_form = $this->createForm(new PictureType(), $picture);
        
        return $this->render('BloggerBlogBundle:Upload:show.html.twig', array(
            /*'post_form' => $post_form->createView(),*/
            'picture_form' => $picture_form->createView()    
        ));        
    }
    
    public function createAction($picture_id)
    {
        //a partir de la picture_id obtenemos el fichero imagen
        //la función getPicture está al final del controller
        $picture = $this->getPicture($picture_id);
        
    //Le pasamos el id de la imagen, comprueba si existe un blog ya creado con ese id de imagen
    //si existe, muestra el formulario(aparecerá relleno, podrémos editarlo), si no, cra un blog nuevo    
        $em = $this->getDoctrine()
                    ->getManager();
        
        $blog = $picture->getBlog();

        /*$blog = $em->getRepository('BloggerBlogBundle:Blog')->findOneById($picture_id->getBlog()->getId());*/
        
        if($blog == null){
           $blog = new Blog(); 
        }
        
        $blog->setImage($picture);
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
            'picture_id'=> $picture_id,
            'post_form' => $post_form->createView()
        ));
    }
    
    
    /**
 * @Template()
 */
    //primer paso cuando quieres crear un post nuevo -> subir imagen
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
                //una vez ha persistido la imagen, pasa la id de esta a la ruta create_post 
                //que se encarga de mostrar el formulario de rellenar post
                return $this->redirect($this->generateUrl('BloggerBlogBundle_create_post', array(
                    'picture_id'    => $picture->getId()
                )));
            }
        }
    }
    
    public function upload_editAction($picture_id)
    {
        
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
                //paco
                //$picture->getId();
                //
                $em->persist($picture);
                $em->flush();
                //paco
                //$picture->setFuncionQueMetaLaUrl("url" . $picture->getId() . ".jpg");
                //
                $this->get('session')->getFlashBag()->add('picture-notice', 'Your picture was successfully updated. Thank you!');
                // Redirige - Esto es importante para prevenir que el usuario
                // reenvíe el formulario si actualiza la página
                return $this->redirect($this->generateUrl('BloggerBlogBundle_create_post', array(
                    'picture_id'    => $picture->getId()
                )));
            }
        }
    }
    
    public function editAction($blog_id)
    {
        /*$picture = $this->getPicture($picture_id);
        
        $picture = new Picture();
        $picture_form = $this->createForm(new PictureType(), $picture);

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $picture_form->bind($request);

            if ($picture_form->isValid()) {
            
                $em = $this->getDoctrine()
                       ->getManager();
                //paco
                $picture->getId();
                //
                $em->persist($picture);
                $em->flush();
                //paco
                //$picture->setFuncionQueMetaLaUrl("url" . $picture->getId() . ".jpg");
                //
                $this->get('session')->getFlashBag()->add('picture-notice', 'Your picture was successfully updated. Thank you!');
                // Redirige - Esto es importante para prevenir que el usuario
                // reenvíe el formulario si actualiza la página
                return $this->redirect($this->generateUrl('BloggerBlogBundle_edit_post', array(
                    'picture_id'    => $picture->getId()
                )));
            }
        }*/
        $picture = new Picture();
        $picture_form = $this->createForm(new PictureType(), $picture);
        
        return $this->render('BloggerBlogBundle:Edit:show.html.twig', array(
            'blog_id' => $blog_id,
            'picture_form' => $picture_form->createView()    
        ));       
        
    }
    
    protected function getPicture($picture_id)
    {
        $em = $this->getDoctrine()
                    ->getManager();

        $picture = $em->getRepository('BloggerBlogBundle:Picture')->find($picture_id);

        if (!$picture) {
            throw $this->createNotFoundException('Unable to find Blog post.');
        }

        return $picture;
    }
    
}