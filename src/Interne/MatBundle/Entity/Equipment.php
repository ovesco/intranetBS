<?php

namespace Interne\MatBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Equipement
 *
 * @ORM\Table(name="mat_bundle_equipement")
 * @ORM\Entity
 */
class Equipment
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
     * @ORM\OneToMany(targetEntity="Interne\MatBundle\Entity\Loan", mappedBy="equipement")
     */
    private $loans;

    /**
     * @ORM\ManyToMany(targetEntity="Interne\MatBundle\Entity\Tag", mappedBy="equipements", cascade={"persist"})
     *
     */
    private $tags;

    /**
     * @ORM\ManyToOne(targetEntity="Interne\MatBundle\Entity\Type", cascade={"persist"})
     */
    private $type;

    /**
     * @var boolean
     *
     * @ORM\Column(name="expendable", type="boolean")
     */
    private $expendable;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text")
     */
    private $comment;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->loans = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Get expendable
     *
     * @return boolean
     */
    public function getExpendable()
    {
        return $this->expendable;
    }

    /**
     * Set expendable
     *
     * @param boolean $expendable
     *
     * @return Equipment
     */
    public function setExpendable($expendable)
    {
        $this->expendable = $expendable;

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
     * Set comment
     *
     * @param string $comment
     *
     * @return Equipment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Equipment
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Add loan
     *
     * @param \Interne\MatBundle\Entity\Loan $loan
     *
     * @return Equipment
     */
    public function addLoan(\Interne\MatBundle\Entity\Loan $loan)
    {
        $this->loans[] = $loan;

        return $this;
    }

    /**
     * Remove loan
     *
     * @param \Interne\MatBundle\Entity\Loan $loan
     */
    public function removeLoan(\Interne\MatBundle\Entity\Loan $loan)
    {
        $this->loans->removeElement($loan);
    }

    /**
     * Get loans
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLoans()
    {
        return $this->loans;
    }

    /**
     * Add tag
     *
     * @param \Interne\MatBundle\Entity\Tag $tag
     *
     * @return Equipment
     */
    public function addTag(\Interne\MatBundle\Entity\Tag $tag)
    {
        $this->tags[] = $tag;

        return $this;
    }

    /**
     * Remove tag
     *
     * @param \Interne\MatBundle\Entity\Tag $tag
     */
    public function removeTag(\Interne\MatBundle\Entity\Tag $tag)
    {
        $this->tags->removeElement($tag);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Get type
     *
     * @return \Interne\MatBundle\Entity\Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param \Interne\MatBundle\Entity\Type $type
     *
     * @return Equipment
     */
    public function setType(\Interne\MatBundle\Entity\Type $type = null)
    {
        $this->type = $type;

        return $this;
    }
}
