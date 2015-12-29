<?php
// src/Blogger/BlogBundle/Controller/DemoController.php

namespace Blogger\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Blogger\BlogBundle\Entity\Blog;
use Blogger\BlogBundle\Form\BlogType;

use Blogger\BlogBundle\Entity\Picture;
use Blogger\BlogBundle\Form\PictureType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;


class DemoController extends Controller
{
    
    public function newAction()
    {
        $blog = new Blog();
        $post_form = $this->createForm(new BlogType(), $blog);
        
        return $this->render('BloggerBlogBundle:Demo:new.html.twig', array(
            'post_form' => $post_form->createView()
        ));        
    }
 

    public function createAction(Request $request)
    {
        //This is optional. Do not do this check if you want to call the same action using a regular request.
        // DE AQUÍ NO PASA!!!!
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(array('message' => 'You can access this only using Ajax!'), 400);
        }
 
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
 
                $response = new JsonResponse(
                    array(
                'message' => 'Ok',
                'post_form' => $this->generateUrl('BloggerBlogBundle_upload_image', array(
                    'blog_id'    => $blog->getId(),
                ))), 400);
 
        return $response;
        }
 
        $response = new JsonResponse(
                array(
            'message' => 'Error',
            'post_form' => $this->renderView('BloggerBlogBundle:Demo:show.html.twig',
                array(
            'post_form' => $post_form->createView(),
        ))), 400);
 
        return $response;
    }
    }


}
