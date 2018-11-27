<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Email
 *
 * @ORM\Table(name="email")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EmailRepository")
 */
class Email
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * email id specified in the mailgun post request
     * @var string
     *
     * @ORM\Column(name="email_mailgun_id", type="string")
     */
    private $messageMailgunId;

    /**
     * email of the sender
     * @var string
     *
     * @ORM\Column(name="sender_email", type="string")
     */
    private $senderEmail;

    /**
     * email of the recipient
     * @var string
     *
     * @ORM\Column(name="recipient_email", type="string")
     */
    private $recipientEmail;

    /**
     * subject of the email
     * @var string
     *
     * @ORM\Column(name="subject", type="string")
     */
    private $subject;

    /**
     * body of the expedient
     * @var string
     *
     * @ORM\Column(name="body", type="string", length=4000)
     */
    private $body;


    /**
     * timestamp of reception
     * @var \DateTime
     *
     * @ORM\Column(name="timestamp", type="datetime")
     */
    private $timestamp;

    /**
     * json of the request
     * @var string
     *
     * @ORM\Column(name="post_request", type="text")
     */
    private $postRequest;

    /**
     * User owning this token
     * @ORM\OneToMany(targetEntity="EmailAttachment", mappedBy="email")
     * @var Email
     */
    protected $attachments;

    /**
     * User owning this token
     * @ORM\ManyToOne(targetEntity="EmailConversation", inversedBy="emails")
     * @ORM\JoinColumn(name="conversation_id", referencedColumnName="id")
     * @var EmailConversation
     */
    protected $conversation;

    public function __construct()
    {
        $this->attachments = new ArrayCollection();
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSenderEmail()
    {
        return $this->senderEmail;
    }

    /**
     * @param string $senderEmail
     */
    public function setSenderEmail($senderEmail)
    {
        $this->senderEmail = $senderEmail;
    }

    /**
     * @return string
     */
    public function getRecipientEmail()
    {
        return $this->recipientEmail;
    }

    /**
     * @param string $recipientEmail
     */
    public function setRecipientEmail($recipientEmail)
    {
        $this->recipientEmail = $recipientEmail;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param \DateTime $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return string
     */
    public function getpostRequest()
    {
        return $this->postRequest;
    }

    /**
     * @param string $postRequest
     */
    public function setPostRequest($postRequest)
    {
        $this->postRequest = $postRequest;
    }

    /**
     * @return Email
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * @return string
     */
    public function getMessageMailgunId()
    {
        return $this->messageMailgunId;
    }

    /**
     * @param string $messageMailgunId
     */
    public function setMessageMailgunId($messageMailgunId)
    {
        $this->messageMailgunId = $messageMailgunId;
    }

    /**
     * @return EmailConversation
     */
    public function getConversation()
    {
        return $this->conversation;
    }

    /**
     * @param EmailConversation $conversation
     */
    public function setConversation($conversation)
    {
        $this->conversation = $conversation;
    }
}

