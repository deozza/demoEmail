<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints As Assert;

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
     * email of the sender
     * @var integer
     *
     * @ORM\Column(name="nb_attachment", type="integer")
     */
    private $nbAttachment;

    /**
     * json of the request
     * @var string
     *
     * @ORM\Column(name="json_request", type="text")
     */
    private $jsonRequest;


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
     * @return int
     */
    public function getNbAttachment()
    {
        return $this->nbAttachment;
    }

    /**
     * @param int $nbAttachment
     */
    public function setNbAttachment($nbAttachment)
    {
        $this->nbAttachment = $nbAttachment;
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
    public function getJsonRequest()
    {
        return $this->jsonRequest;
    }

    /**
     * @param string $jsonRequest
     */
    public function setJsonRequest($jsonRequest)
    {
        $this->jsonRequest = $jsonRequest;
    }


}

