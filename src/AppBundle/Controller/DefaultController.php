<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Email;
use AppBundle\Entity\EmailAttachment;
use AppBundle\Serializer\FormErrorSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    private $em;
    private $formErrorSerializer;

    public function __construct(EntityManagerInterface $entityManager, FormErrorSerializer $formErrorSerializer, LoggerInterface $logger)
    {
        $this->em = $entityManager;
        $this->formErrorSerializer = $formErrorSerializer;
        $this->logger = $logger;
    }

    /**
     * @Route("/", name="homepage", methods={"GET"})
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
        $this->logger->debug('start of saving');
        $requestContent = file_get_contents("php://input");
        $postedEmail = $request->request->all();

        $requiredKeyWithDefaultValue = [
            'from' => "none@none.none",
            'recipient' =>"none@none.none",
            "subject" => "no subject",
            "body-html" => "<html><body>No body</body></html>",
            "timestamp" => new \DateTime()
        ];

        foreach($requiredKeyWithDefaultValue as $item=>$value)
        {
            if(!array_key_exists($item,$postedEmail) || empty($postedEmail[$item]))
            {
                $postedEmail[$item] = $value;
            }
        }

        if(!$postedEmail['timestamp'] instanceof \DateTime)
        {
            $date = new \DateTime();
            $date->setTimestamp($postedEmail['timestamp']);
            $postedEmail["timestamp"] = $date;
        }

        $email = new Email();
        $email->setSenderEmail($postedEmail['from']);
        $email->setRecipientEmail($postedEmail['recipient']);
        $email->setSubject($postedEmail['subject']);
        $email->setBody($postedEmail['body-html']);
        $email->setTimestamp($postedEmail['timestamp']);
        $email->setPostRequest($requestContent);

        $this->em->persist($email);
        $this->em->flush();
        $this->logger->debug($email->getId());

        $files = $request->files->all();

        if(empty($files)) return new JsonResponse('Email saved without attachment', 200);

        foreach ($files as $file)
        {
            $this->logger->debug($file);

            $emailAttachment = new EmailAttachment($email, $file);

            $this->em->persist($emailAttachment);
            $this->logger->debug($emailAttachment->getFilename());
            $this->logger->debug(serialize($emailAttachment));

        }

        $this->em->flush();
        $this->logger->debug('attachments saved');

        return new JsonResponse('Email saved with attachment', 200);
    }

    /**
     * @Route("/email/store", name="email_store", methods={"POST"})
     */
    public function storeEmailAction(Request $request)
    {
        $this->logger->info($request);
        die;
    }

    /**
     * @Route("/email/{id}", name="get_email_content", methods={"GET"})
     */
    public function getEmailContentAction($id)
    {
        $email = $this->em->getRepository('AppBundle:Email')->findOneById($id);
        $attachments = $email->getAttachments();

        $this->logger->debug(serialize($attachments));
        $this->logger->debug(serialize($email));

        return $this->render('default/email.html.twig', [
            'email' => $email,
            'attachments' => $attachments
        ]);
    }

    /**
     * @Route("/attachment{id}", name="dl_attachment", methods={"GET"})
     */
    public function getAttachmentAction($id)
    {

        $attachment = $this->em->getRepository('AppBundle:EmailAttachment')->find($id);

        $response = new Response(stream_get_contents($attachment->getAttachment()), 200, [
            'Content-type'=>'text/plain',
            'Content-Length' => fstat($attachment->getAttachment())['size'],
            'Content-Disposition'=> 'attachment; filename="'.$attachment->getFilename().'"'
        ]);

        return $response;
    }


}
