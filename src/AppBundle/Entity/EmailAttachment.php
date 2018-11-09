<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Email
 *
 * @ORM\Table(name="email_attachment")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EmailAttachmentRepository")
 */
class EmailAttachment
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
     * attachment
     *
     * @ORM\Column(name="attachment", type="blob")
     */
    private $attachment;

    /**
     * attachment
     *
     * @ORM\Column(name="filename", type="string")
     */
    private $filename;

    /**
     * User owning this token
     * @ORM\ManyToOne(targetEntity="Email")
     * @var Email
     */
    protected $email;

    public function __construct($email, $filename, $attachment)
    {
        $this->email = $email;
        $this->filename = $filename;
        $this->attachment = $attachment;
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
     * @return mixed
     */
    public function getAttachment()
    {
        return $this->attachment;
    }

    /**
     * @param mixed $attachment
     */
    public function setAttachment($attachment)
    {
        $this->attachment = $attachment;
    }

    /**
     * @return Email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param Email $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param mixed $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }


}

