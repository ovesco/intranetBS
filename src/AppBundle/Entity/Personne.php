<?php

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\VirtualProperty;




/**
 * @ORM\MappedSuperclass
 *
 * @ExclusionPolicy("all")
 *
 */
abstract class Personne
{
    const HOMME = 'Homme';
    const FEMME = 'Femme';


    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Expose
     */
    protected $id;

    /**
     * 
     * @var string
     * 
     * @ORM\Column(name="prenom", type="string", length=255)
     *
     * @Expose
     *
     */
    protected $prenom;

    /**
     * @var string
     *
     * 
     * @ORM\Column(name="sexe", type="string", columnDefinition="ENUM('Homme', 'Femme')")
     *
     *
     */
    protected $sexe;

    /**
     * 
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Contact", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="id")
     */
    protected $contact;

    /**
     * @var string
     *
     * 
     * @ORM\Column(name="iban", type="string", length=255, nullable=true)
     */
    protected $iban;

    /**
     * @var boolean
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="decede", type="boolean")
     */
    private $decede;


    public function __construct()
    {
        $this->decede = false;
        //une personne a forcement un contact
        $this->contact = new Contact();
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return ucwords($this->prenom);
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     * @return Personne
     */
    public function setPrenom($prenom)
    {
        $this->prenom = ucwords($prenom);

        return $this;
    }

    /**
     * Get sexe
     *
     * @return string
     */
    public function getSexe()
    {
        return $this->sexe;
    }

    /**
     * Set sexe
     *
     * @param string $sexe
     * @return Personne
     */
    public function setSexe($sexe)
    {
        if ($sexe != Personne::HOMME && $sexe != Personne::FEMME)
            throw new Exception("Le sexe doit être " . Personne::HOMME . " ou " . Personne::FEMME . ", obtenu : '" . $sexe . "'");

        $this->sexe = $sexe;

        return $this;
    }

    /**
     * Get iban
     *
     * @return string
     */
    public function getIban()
    {
        return $this->iban;
    }

    /**
     * Set iban
     *
     * @param string $iban
     * @return Personne
     */
    public function setIban($iban)
    {
        $this->iban = $iban;

        return $this;
    }

    /**
     * Get contact
     *
     * @return Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Set contact
     *
     * @param Contact $contact
     * @return Personne
     */
    public function setContact(Contact $contact = null)
    {
        $this->contact = $contact;

        return $this;
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
     * @return boolean
     */
    public function isDecede()
    {
        return $this->decede;
    }

    /**
     * @param boolean $decede
     */
    public function setDecede($decede)
    {
        $this->decede = $decede;
    }
}
