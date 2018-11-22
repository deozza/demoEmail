<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;


/**
 * Class DefaultControllerTest
 * @package Tests\AppBundle\Controller
 *
 * @group legacy
 */
class DefaultControllerTest extends WebTestCase
{
    public function getAllEmail()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testNoPj()
    {
        $client = static::createClient();

        $valuesNoPj = [
            "recipient" => "recipient@email.com",
            "sender" => "sender@email.com",
            "subject" => "Subject is cool",
            "body-html" => "<p>I's me the body</p>"
        ];

        $crawler = $client->request('POST', '/email', $valuesNoPj);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $crawler = $client->request('GET', '/email/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Subject is cool', $crawler->filter('h5')->text());

    }


    public function testLightPj()
    {
        $client = static::createClient();

        $valuesLightPj = [
            "recipient" => "recipient@email.com",
            "sender"    => "sender@email.com",
            "subject"   => "Subject is great",
            "body-html" => "<p>Hi I'm the body</p>"
        ];
        $fileLightPj = new UploadedFile(
            __DIR__ . '/../../../web/img_testing/light.png',
            "light.png",
            "image/png"
        );

        $crawler = $client->request('POST', '/email', $valuesLightPj, ['file'=>$fileLightPj]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $crawler = $client->request('GET', '/email/2');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Télécharger light.png', $crawler->filter('a')->text());
    }
/*
    public function testHeavyPj()
    {
        $client = static::createClient();

        $valuesHeavyPj = [
            "recipient" => "recipient@email.com",
            "sender"    => "sender@email.com",
            "subject"   => "Subject is awesome",
            "body-html" => "<p>You read the body</p>"
        ];

        $fileHeavyPj = new UploadedFile(
            __DIR__.'/../../../web/img_testing/heavy.jpg',
            "heavy.jpg",
            "image/jpg"
        );

        $crawler = $client->request('POST', '/email', $valuesHeavyPj, ['file'=>$fileHeavyPj]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $crawler = $client->request('GET', '/email/3');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('heavy.jpg : UPLOAD_ERR_INI_SIZE', $crawler->filter('div > .col')->text());

    }

    public function testDualPj()
    {
        $client = static::createClient();

        $valuesDualPj = [
            "recipient" => "recipient@email.com",
            "sender"    => "sender@email.com",
            "subject"   => "Subject is dual",
            "body-html" => "<p>Look 2 attachments !</p>"
        ];

        $fileHeavyPj = new UploadedFile(
            __DIR__.'/../../../web/img_testing/heavy.jpg',
            "heavy.jpg",
            "image/jpg"
        );
        $fileLightPj = new UploadedFile(
            __DIR__.'/../../../web/img_testing/light.png',
            "light.png",
            "image/png"
        );

        $crawler = $client->request('GET', '/email/4');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $crawler = $client->request('POST', '/email', $valuesDualPj, ['file1'=>$fileHeavyPj,'file2'=>$fileLightPj]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertContains('heavy.jpg : UPLOAD_ERR_INI_SIZE', $crawler->filter('div > .col')->text());
        $this->assertContains('Télécharger light.png', $crawler->filter('a')->text());
    }
*/
}
