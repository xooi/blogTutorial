<?php
// src/Blogger/BlogBundle/Tests/Controller/PageControllerTest.php

namespace Blogger\BlogBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PageControllerTest extends WebTestCase
{
    public function testAbout()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/about');

        $this->assertEquals(1, $crawler->filter('h1:contains("About symblog")')->count());
    }
    
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        // Busca el primer enlace, obtiene el título, se asegura que
        // este se carga en la siguiente página
        $blogLink   = $crawler->filter('article.blog h2 a')->first();;
        $blogTitle  = $blogLink->text();
        $crawler    = $client->click($blogLink->link());

        // Comprueba que el H2 tiene el título del blog en él
        $this->assertEquals(1, $crawler->filter('h2:contains("' . $blogTitle .'")')->count());
        
    }
    
    public function testContact()
    {
    $client = static::createClient();

    $crawler = $client->request('GET', '/contact');

    $this->assertEquals(1, $crawler->filter('h1:contains("Contact symblog")')->count());

    // Selecciona basándose en el valor del botón, o el id o el nombre de los botones
    $form = $crawler->selectButton('Submit')->form();

    $form['contact[name]'] = 'name';
    $form['contact[email]'] = 'email@example.com';
    $form['contact[subject]'] = 'subject';
    $form['contact[body]'] = 'The comment body must be at least 50 characters long as there is a validation constrain on the Enquiry entity';

    $crawler = $client->submit($form);
    
    // Comprueba que se ha enviado el correo electrónico
    if ($profile = $client->getProfile())
    {
        $swiftMailerProfiler = $profile->getCollector('swiftmailer');

        // Únicamente se debe haber enviado 1 mensaje
        $this->assertEquals(1, $swiftMailerProfiler->getMessageCount());

        // Obtiene el primer mensaje
        $messages = $swiftMailerProfiler->getMessages();
        $message  = array_shift($messages);

        $symblogEmail = $client->getContainer()->getParameter('blogger_blog.emails.contact_email');
        // Comprueba que el mensaje se está enviando a la dirección correcta
        $this->assertArrayHasKey($symblogEmail, $message->getTo());
    }

    // Necesita seguir la redirección
    $crawler = $client->followRedirect();
    
    $this->assertSame($crawler->filter('.blogger-notice')->text(), 'Your contact enquiry was successfully sent. Thank you!');
    
    //$this->assertEquals(1, $crawler->filter('.blogger-notice:contains(
        //"Your contact enquiry was successfully sent. Thank you!")')->count());
    }
}