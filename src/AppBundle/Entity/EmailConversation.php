<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * EmailConversation
 *
 * @ORM\Table(name="email_conversation")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EmailConversationRepository")
 */
class EmailConversation
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
     * User owning this token
     * @ORM\OneToMany(targetEntity="Email", mappedBy="conversation")
     * @var EmailConversation
     */
    protected $emails;

    public function __construct()
    {
        $this->emails = new ArrayCollection();
    }


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return EmailConversation $
     */
    public function getEmails()
    {
        return $this->emails;
    }
}
