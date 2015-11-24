<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\ElasticaBundle\Configuration\Search;
use Symfony\Component\Config\Definition\Exception\Exception;
use Doctrine\ORM\EntityManager;

/**
 * Payement
 *
 * @ORM\Table(name="app_payement")
 * @ORM\Entity
 * @Search(repositoryClass="AppBundle\Search\Payement\PayementRepository")
 */
class Payement
{

    const NOT_DEFINED = 'not_defined'; //if the payement is still not compared with Facture
    const NOT_FOUND = 'not_found'; //no facture with this payment->idFacture
    const FOUND_ALREADY_PAID = 'found_already_payed';//if facture is found but already paid
    const FOUND_VALID = 'found_valid';//payement correspond to the facture
    const FOUND_LOWER = 'found_lower';
    const FOUND_UPPER = 'found_upper';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="idFacture", type="integer")
     */
    private $idFacture; //n'est pas forcement représentatif d'un ID existant. (state: NOT_FOUND)

    /**
     * @var Facture
     *
     * @ORM\OneToOne(targetEntity="Facture", inversedBy="payement", cascade={"persist"})
     */
    private $facture;

    /**
     * @var float
     *
     * @ORM\Column(name="montantRecu", type="float")
     */
    private $montantRecu;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", columnDefinition="ENUM('not_found','found_already_payed','found_valid','found_lower','found_upper','not_defined')")
     */
    private $state;

    /**
     * @var boolean
     *
     * @ORM\Column(name="validated", type="boolean")
     */
    private $validated;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
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
     * Set idFacture
     *
     * @param integer $idFacture
     * @return Payement
     */
    public function setIdFacture($idFacture)
    {
        $this->idFacture = $idFacture;

        return $this;
    }

    /**
     * Get idFacture
     *
     * @return integer 
     */
    public function getIdFacture()
    {
        return $this->idFacture;
    }

    /**
     * Set montantRecu
     *
     * @param float $montantRecu
     * @return Payement
     */
    public function setMontantRecu($montantRecu)
    {
        $this->montantRecu = $montantRecu;

        return $this;
    }

    /**
     * Get montantRecu
     *
     * @return float 
     */
    public function getMontantRecu()
    {
        return $this->montantRecu;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Payement
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set state
     *
     * @param string $state
     * @return Payement
     */
    public function setState($state)
    {
        if( $state != Payement::NOT_DEFINED &&
            $state != Payement::NOT_FOUND &&
            $state != Payement::FOUND_ALREADY_PAID &&
            $state != Payement::FOUND_VALID &&
            $state != Payement::FOUND_LOWER &&
            $state != Payement::FOUND_UPPER)
            throw new Exception("Le statut incorect , obtenu : '" . $state . "'");

        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set facture
     *
     * @param \AppBundle\Entity\Facture $facture
     *
     * @return Payement
     */
    public function setFacture(\AppBundle\Entity\Facture $facture = null)
    {
        $this->facture = $facture;

        return $this;
    }

    /**
     * Get facture
     *
     * @return \AppBundle\Entity\Facture
     */
    public function getFacture()
    {
        return $this->facture;
    }

    /**
     * Set validated
     *
     * @param boolean $validated
     *
     * @return Payement
     */
    public function setValidated($validated)
    {
        $this->validated = $validated;

        return $this;
    }

    /**
     * Is validated
     *
     *
     * @return Payement
     */
    public function isValidated()
    {
        return $this->validated;
    }


    /**
     * Get validated
     *
     * @return boolean
     */
    public function getValidated()
    {
        return $this->validated;
    }


    /**
     * Cette fonction va checker le statut de lu payement en fonction des factures existantes.
     *
     * @param EntityManager $em
     */
    public function checkState(EntityManager $em){

        /** @var Facture $facture */
        $facture = $em->getRepository('InterneFinancesBundle:Facture')->find($this->getIdFacture());

        if($facture != Null)
        {
            if($facture->getStatut() == Facture::OUVERTE)
            {
                $montantTotalEmis = $facture->getMontantEmis();
                $montantRecu = $this->getMontantRecu();

                if($montantTotalEmis == $montantRecu)
                {
                    $this->setState(Payement::FOUND_VALID);
                }
                elseif($montantTotalEmis > $montantRecu)
                {
                    $this->setState(Payement::FOUND_LOWER);
                }
                elseif($montantTotalEmis < $montantRecu)
                {
                    $this->setState(Payement::FOUND_UPPER);
                }
                /*
                 * On lie le payement à la facture
                 */
                $this->setFacture($facture);
                $facture->setPayement($this);
                /*
                 * On definit la facture comme payée dans tout les cas...ce qui correspond à la réalité.
                 * par contre le payement reste à valider pour répartir la somme dans les créances
                 */
                $facture->setStatut(Facture::PAYEE);
                $em->persist($facture);
            }
            else
            {
                /*
                 * la facture a déjà été payée
                 */
                $this->setState(Payement::FOUND_ALREADY_PAID);
            }


        }
        else
        {
            $this->setState(Payement::NOT_FOUND);
        }


    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Payement
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


}
