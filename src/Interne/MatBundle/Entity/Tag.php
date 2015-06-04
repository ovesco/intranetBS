<?php

namespace Interne\MatBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tag
 *
 * @ORM\Table(name="mat_bundle_tag")
 * @ORM\Entity
 */
class Tag
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="Interne\MatBundle\Entity\Equipement", inversedBy="tags", cascade={"persist"})
     * @ORM\JoinTable(name="mat_bundle_tag_equipement",
     *      joinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="equipement_id", referencedColumnName="id")}
     *      )
     */
    private $equipements;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->equipements = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Tag
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Add equipement
     *
     * @param \Interne\MatBundle\Entity\Equipment $equipement
     *
     * @return Tag
     */
    public function addEquipement(\Interne\MatBundle\Entity\Equipment $equipement)
    {
        $this->equipements[] = $equipement;

        return $this;
    }

    /**
     * Remove equipement
     *
     * @param \Interne\MatBundle\Entity\Equipment $equipement
     */
    public function removeEquipement(\Interne\MatBundle\Entity\Equipment $equipement)
    {
        $this->equipements->removeElement($equipement);
    }

    /**
     * Get equipements
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEquipements()
    {
        return $this->equipements;
    }
}
