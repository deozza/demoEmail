<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Email;
use AppBundle\Serializer\FormErrorSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    private $em;
    private $formErrorSerializer;

    public function __construct(EntityManagerInterface $entityManager, FormErrorSerializer $formErrorSerializer)
    {
        $this->em = $entityManager;
        $this->formErrorSerializer = $formErrorSerializer;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {

        $emails = $this->em->getRepository('AppBundle:Email')->findAll();

        return $this->render('default/index.html.twig', [
            'emails' => $emails,
        ]);
    }

    /**
     * @Route("/email", name="email_registering", methods={"POST"})
     */
    public function postEmailAction(Request $request)
    {
        $postedEmail = json_decode($request->getContent(), true);

        $alreadyRegistered = $this->em->getRepository('AppBundle:Email')->findOneByTimestamp($postedEmail['timestamp']);

        if(!empty($alreadyRegistered)) return new JsonResponse(["message" => "Already registered"], "400");

        if(empty($postedEmail['sender']) ||
            empty($postedEmail['recipient']) ||
            empty($postedEmail['attachment-count']) ||
            empty($postedEmail['timestamp']) ) return new JsonResponse(["message" => "Required parameter missing"], "400");

        if(empty($postedEmail['subject'])) $postedEmail['subject'] = "";
        if(empty($postedEmail['body-html'])) $postedEmail['body-html'] = "";

        $email = new Email();
        $email->setSenderEmail($postedEmail['sender']);
        $email->setRecipientEmail($postedEmail['recipient']);
        $email->setObject($postedEmail['subject']);
        $email->setBody($postedEmail['body-html']);
        $email->setNbAttachment($postedEmail['attachment-count']);
        $email->setTimestamp($postedEmail['timestamp']);

        $this->em->persist($email);
        $this->em->flush();

        $response = new JsonResponse($email, "200");
        return $response;
    }

}
