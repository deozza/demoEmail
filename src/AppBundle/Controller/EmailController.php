<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Email;
use AppBundle\Entity\EmailAttachment;
use AppBundle\Entity\EmailConversation;
use AppBundle\Form\ReplyFormType;
use AppBundle\Serializer\FormErrorSerializer;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Mailgun\Mailgun;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EmailController extends Controller
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

        $conversations = $this->em->getRepository('AppBundle:EmailConversation')->findAll();

        return $this->render('default/index.html.twig', [
            'conversations' => $conversations,
        ]);
    }

    /**
     * @Route("/email", name="email_registering", methods={"POST"})
     */
    public function postEmailAction(Request $request)
    {
        $requestContent = file_get_contents("php://input");

        $postedEmail = $this->checkIsKeyExists($request->request->all());

        $email = $this->em->getRepository(Email::class)->saveIncomingEmail($postedEmail, $requestContent);
        $this->em->persist($email);
        $this->em->flush();

        $message = "Email saved";

        $files = $request->files->all();

        if(!empty($files))
        {
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
            $message .= " with attachment(s).";
        }

        return new JsonResponse($message, 200);
    }

    /**
     * @Route("/email/store", name="email_store", methods={"POST"})
     */
    public function storeEmailAction(Request $request)
    {
        $postedRequest = base64_encode(json_encode($request->request->all()));
        $postedEmail = $this->checkIsKeyExists($request->request->all());

        $email = $this->em->getRepository(Email::class)->saveIncomingEmail($postedEmail, $postedRequest);
        $this->em->persist($email);
        $this->em->flush();

        $message ="Email saved";

        if(!empty($postedEmail['attachments']))
        {
            $client = new Client([
                'base_uri' => 'https://se.api.mailgun.net/v3/domains/mailgun.everycheck.com/messages/',
                'timeout' => 180.0,
            ]);

            //$guzzleLogFile = $this->getParameter('kernel.project_dir').'/var/logs/guzzle.log';
            //$guzzleLogStream = fopen($guzzleLogFile,'w');
            // stream_set_write_buffer($guzzleLogStream, 0);
            $body = [
                "debug" => false//$guzzleLogStream
            ];
            $body["headers"] = [
                "Authorization" =>"Basic ".base64_encode("api:".$this->getParameter('mailgun_api_key'))
            ];

            $attachments = json_decode($postedEmail['attachments'],true);
            foreach($attachments as $attachment)
            {
                $dlFileUrl= $attachment['url'];
                $this->logger->debug($dlFileUrl);

                $filename = uniqid();

                try
                {
                    $response = $client->request("GET", $dlFileUrl,$body);
                    file_put_contents($filename, $response->getBody());
                    $file = new UploadedFile($filename,$attachment['name'],$attachment['content-type'],$attachment['size'],UPLOAD_ERR_OK);
                }
                catch (\Exception $e) 
                {
                    file_put_contents($filename, $e->getMessage());
                    $file = new UploadedFile($filename,$attachment['name'],'text/plain',strlen($e->getMessage()),EmailAttachment::GUZZLE_EXCEPTION);
                }

                $emailAttachment = new EmailAttachment($email, $file);
                $this->em->persist($emailAttachment);

                unlink($filename);

                $this->logger->debug(serialize($emailAttachment));

            }

            $this->em->flush();
            $message .= " with attachment(s).";
        }

        return new JsonResponse($message, 200);
    }

    /**
     * @Route("/conversation/{id}", name="get_conversation_content", methods={"GET"})
     */
    public function getConversationContentAction($id)
    {
        $conversation = $this->em->getRepository('AppBundle:EmailConversation')->findOneById($id);

        if(empty($conversation)) return $this->render('errors/404.html.twig');

        return $this->render('default/conversation.html.twig', [
            'emails' => $conversation->getEmails(),
        ]);
    }

    /**
     * @Route("/email/{id}/reply", name="reply_to_email", methods={"POST","GET"})
     */
    public function ReplyAction($id, Request $request)
    {
        $email = $this->em->getRepository('AppBundle:Email')->findOneById($id);

        if(empty($email)) return $this->render('errors/404.html.twig');

        $reply = new Email();

        $form = $this->createForm(ReplyFormType::class, $reply);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $msg = Mailgun::create($this->getParameter('mailgun_api_key'));

            try
            {
                $msg->messages()->send(
                    $email->getSenderEmail(), [
                        'from' => $email->getRecipientEmail(),
                        'to' => $email->getSenderEmail(),
                        'subject' => $email->getSubject(),
                        'text' => $reply->getBody(),
                        'h:Reply-To' => $email->getMessageMailgunId()
                    ]
                );
            }
            catch (\Exception $e)
            {
                return $this->render('errors/400.html.twig', [$e->getMessage()]);
            }

            $reply->setSenderEmail($email->getRecipientEmail());
            $reply->setRecipientEmail($email->getSenderEmail());
            $reply->setSubject($email->getSubject());
            $reply->setTimestamp(new \DateTime('now'));
            $reply->setConversation($email->getConversation());
            $reply->setPostRequest("request");
            $reply->setMessageMailgunId("emailId");

            $this->em->persist($reply);

            $this->em->flush();

            return $this->redirectToRoute('get_conversation_content', [
                'id' => $reply->getConversation()->getId()
            ]);
        }

        return $this->render('default/reply.html.twig', [
            'email' => $email,
            'attachments' => $email->getAttachments(),
            'replyFormType' => $form->createView()
        ]);
    }



    /**
     * @Route("/attachment{id}", name="dl_attachment", methods={"GET"})
     */
    public function getAttachmentAction($id)
    {
        $attachment = $this->em->getRepository('AppBundle:EmailAttachment')->find($id);

        if(empty($attachment)) return $this->render('errors/404.html.twig');

        $response = new Response(stream_get_contents($attachment->getAttachment()), 200, [
            'Content-type'=>'text/plain',
            'Content-Length' => fstat($attachment->getAttachment())['size'],
            'Content-Disposition'=> 'attachment; filename="'.$attachment->getFilename().'"'
        ]);

        return $response;
    }

    private function checkIsKeyExists(Array $postedEmail)
    {
        $requiredKeyWithDefaultValue = [
            'from' => "none@none.none",
            'recipient' =>"none@none.none",
            "subject" => "no subject",
            "body-html" => "<html><body>No body</body></html>",
            "timestamp" => new \DateTime()
        ];

        if(!array_key_exists("body-html", $postedEmail))
        {
            $postedEmail['body-html'] = $postedEmail['body-plain'];
        }

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

        return $postedEmail;
    }


}
