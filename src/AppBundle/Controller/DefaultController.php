<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations As Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends FOSRestController
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {



        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_OK)
     * @Rest\Post("/email")
     */
    public function postEmailAction()
    {

    }

}
