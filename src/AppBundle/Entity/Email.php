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
     * @ORM\Column(name="sender_email", type="string", nullable=false)
     * @Assert\Type("string")
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email.",
     *     checkMX = true
     * )
     */
    private $senderEmail;

    /**
     * object of the email
     * @var string
     *
     * @ORM\Column(name="object", type="string", nullable=false)
     * @Assert\Type("string")
     */
    private $object;

    /**
     * body of the expedient
     * @var string
     *
     * @ORM\Column(name="body", type="string", length=4000, nullable=false)
     * @Assert\Type("string")
     */
    private $body;


    /**
     * date of reception
     * @var \DateTime
     *
     * @ORM\Column(name="reception_date", type="datetime", nullable=false)
     * @Assert\DateTime
     */
    private $receptionDate;


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
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param string $object
     */
    public function setObject($object)
    {
        $this->object = $object;
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
    public function getReceptionDate()
    {
        return $this->receptionDate;
    }

    /**
     * @param \DateTime $receptionDate
     */
    public function setReceptionDate($receptionDate)
    {
        $this->receptionDate = $receptionDate;
    }
}

