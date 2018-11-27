<?php

namespace Tests\AppBundle\Controller;

use AppBundle\DataFixtures\DataFixturesTestCase;


/**
 * Class DefaultControllerTest
 * @package Tests\AppBundle\Controller
 *
 * @group legacy
 */
class EmailControllerTest extends DataFixturesTestCase
{
    public function testGetAllEmail()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testNoPj()
    {
        $client = static::createClient();

        $crawler = $client->request('POST', '/email/store', json_decode(file_get_contents(__DIR__."/payload/nopj.json"), true));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $crawler = $client->request('GET', '/conversation/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        //$this->assertContains('Subject is cool', $crawler->filter('h5')->text());

    }


    public function testLightPj()
    {
        $client = static::createClient();

        $crawler = $client->request('POST', '/email/store',json_decode(file_get_contents(__DIR__."/payload/lightpj.json"), true));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $crawler = $client->request('GET', '/conversation/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        //$this->assertContains('Télécharger light.png', $crawler->filter('a')->text());
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

    public function testReplyToEmail()
    {

        $client = static::createClient();

        $crawler = $client->request('POST', '/email/store', json_decode(file_get_contents(__DIR__."/payload/nopj.json"), true));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $crawler = $client->request('GET', '/conversation/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $crawler = $client->request('POST', '/conversation/1/email/1/reply',json_decode(file_get_contents(__DIR__."/payload/reply.json"), true));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

}
