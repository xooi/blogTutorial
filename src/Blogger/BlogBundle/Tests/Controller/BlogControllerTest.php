<?php
// src/Blogger/BlogBundle/Tests/Controller/BlogControllerTest.php

namespace Blogger\BlogBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BlogControllerTest extends WebTestCase
{
    public function testAddBlogComment()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/6/a-day-with-symfony');

        $this->assertEquals(1, $crawler->filter('h2:contains("A day with Symfony2")')->count());

        // Selecciona basándose en el valor del botón, o el id o el nombre de los botones
        $form = $crawler->selectButton('Submit')->form();

        $crawler = $client->submit($form, array(
            'blogger_blogbundle_comment[user]'          => 'name',
            'blogger_blogbundle_comment[comment]'       => 'comment',
        ));

        // Necesita seguir la redirección
        $crawler = $client->followRedirect();

        // Comprueba que el comentario ahora se está mostrando en la página,
        // como la entrada más reciente. Esto garantiza que los comentarios
        // se están ordenando del más antiguo al nuevo
        $articleCrawler = $crawler->filter('section .previous-comments article')->last();

        $this->assertEquals('name', $articleCrawler->filter('header span.highlight')->text());
        $this->assertEquals('comment', $articleCrawler->filter('p')->last()->text());

        // Comprueba la barra lateral para asegurarse de que se muestra el comentario
        // más reciente y que hay 10 de ellos

        $this->assertEquals(10, $crawler->filter('aside.sidebar section')->last()
                                        ->filter('article')->count()
        );

        $this->assertEquals('name', $crawler->filter('aside.sidebar section')->last()
                                            ->filter('article')->first()
                                            ->filter('header span.highlight')->text()
        );
    }
}