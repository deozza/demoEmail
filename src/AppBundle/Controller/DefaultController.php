<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Email;
use AppBundle\Entity\EmailAttachment;
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
            $emailAttachment = new EmailAttachment($email->getId(), $request->files->getPathname(), $binaryContent);
            $this->em->persist($binaryContent);
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
