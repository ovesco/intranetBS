<?php

namespace Interne\MatBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Loan
 *
 * @ORM\Table(name="mat_bundle_loan")
 * @ORM\Entity
 */
class Loan
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Interne\MatBundle\Entity\Booking", inversedBy="loans", cascade={"persist"})
     */
    private $booking;

    /**
     * @ORM\ManyToOne(targetEntity="Interne\MatBundle\Entity\Equipement", inversedBy="loans")
     */
    private $equipement;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="damage", type="text")
     */
    private $damage;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text")
     */
    private $comment;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Loan
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set damage
     *
     * @param string $damage
     *
     * @return Loan
     */
    public function setDamage($damage)
    {
        $this->damage = $damage;

        return $this;
    }

    /**
     * Get damage
     *
     * @return string
     */
    public function getDamage()
    {
        return $this->damage;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Loan
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }



    /**
     * Set booking
     *
     * @param \Interne\MatBundle\Entity\Booking $booking
     *
     * @return Loan
     */
    public function setBooking(\Interne\MatBundle\Entity\Booking $booking = null)
    {
        $this->booking = $booking;

        return $this;
    }

    /**
     * Get booking
     *
     * @return \Interne\MatBundle\Entity\Booking
     */
    public function getBooking()
    {
        return $this->booking;
    }



    /**
     * Set equipement
     *
     * @param \Interne\MatBundle\Entity\Equipement $equipement
     *
     * @return Loan
     */
    public function setEquipement(\Interne\MatBundle\Entity\Equipement $equipement = null)
    {
        $this->equipement = $equipement;

        return $this;
    }

    /**
     * Get equipement
     *
     * @return \Interne\MatBundle\Entity\Equipement
     */
    public function getEquipement()
    {
        return $this->equipement;
    }
}
