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
     * @Route("/email/{id}", name="get_email_content")
     */
    public function getEmailContentAction($id)
    {

        $email = $this->em->getRepository('AppBundle:Email')->findOneById($id);

        $attachments = $this->em->getRepository(EmailAttachment::class)->findByEmail($id);

        return $this->render('default/email.html.twig', [
            'email' => $email,
            "attachments" => $attachments
        ]);
    }

    /**
     * @Route("/attachment{id}", name="dl_attachment")
     */
    public function getAttachmentAction($id)
    {

        $attachment = $this->em->getRepository('AppBundle:EmailAttachment')->find($id);

        $finfo = new \finfo(FILEINFO_MIME_TYPE);

        $mimetype = $finfo->buffer($attachment);
        $fileName = $mimetype == 'image/png' ? 'logo.png' : 'logo.jpeg';
        $headers = [
            'Content-Type'     => $mimetype,
            'Content-Disposition' => 'inline; filename="'.$fileName.'"'
        ];

        return new Response($attachment, 200, $headers);

    }

    /**
     * @Route("/email", name="email_registering", methods={"POST"})
     */
    public function postEmailAction(Request $request)
    {
        $postedEmail = $request->request->all();


        if(strpos($request->headers->get('content-type'), "multipart") === false)
        {
            return $this->emailWithoutAttachment($postedEmail, $request);
        }
        else {
            return $this->emailWithAttachment($postedEmail, $request);
        }
    }

    private function emailWithAttachment($postedEmail, Request $request)
    {
        $requiredKeyWithDefaultValue = [
            'from' => "none@none.none",
            'recipient' =>"none@none.none",
            "subject" => "no subject",
            "body-html" => "<html><body>No body</body></html>",
            "timestamp" => new \DateTime(),
            "attachment-count" => 0];

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
        $email->setNbAttachment($postedEmail['attachment-count']);
        $email->setTimestamp($postedEmail['timestamp']);
        $email->setPostRequest(var_export($request->files->all(), true));

        $this->em->persist($email);

        $files = $request->files->all();

        foreach ($files as $file)
        {
            $binaryContent= file_get_contents($file->getPathname());
            $emailAttachment = new EmailAttachment($email->getId(), $file->getClientOriginalName(), $binaryContent);
            $this->em->persist($emailAttachment);
        }

        $this->em->flush();

        return new JsonResponse($email->getPostRequest(), "200");
    }

    private function emailWithoutAttachment($postedEmail, Request $request)
    {
        $requiredKeyWithDefaultValue = [
            'from' => "none@none.none",
            'recipient' =>"none@none.none",
            "subject" => "no subject",
            "body-html" => "<html><body>No body</body></html>",
            "timestamp" => new \DateTime(),
            "attachment-count" => 0];

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
        $email->setNbAttachment($postedEmail['attachment-count']);
        $email->setTimestamp($postedEmail['timestamp']);
        $email->setPostRequest($request->getContent());

        $this->em->persist($email);
        $this->em->flush();

        return new JsonResponse($email->getPostRequest(), "200");
    }

}
