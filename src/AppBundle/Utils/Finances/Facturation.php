<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 20.11.16
 * Time: 18:27
 */

namespace AppBundle\Utils\Finances;

use AppBundle\Entity\Debiteur;
use AppBundle\Entity\Creance;
use AppBundle\Entity\Facture;
use AppBundle\Entity\Famille;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Repository\FactureRepository;
use AppBundle\Entity\Membre;
use Symfony\Component\Security\Acl\Exception\Exception;

/**
 *
 * C'est dans cette class que ce situe la logique liée à la facturation.
 *
 * Tout les méthodes permetant de crée des facture a partire de créance s'y trouvent.
 *
 * Class Facturation
 * @package AppBundle\Utils\Finances
 *
 *
 *
 */
class Facturation {

    /** @var FactureRepository */
    private $factureRepository;

    public function __construct(FactureRepository $factureRepository)
    {
        $this->factureRepository = $factureRepository;
    }

    /**
     *
     * Facture tout les créances d'un débiteur qui ne sont
     * pas encore facturées
     *
     * @param Debiteur $debiteur
     */
    public function facturationDebiteur(Debiteur $debiteur)
    {
        $creancesToFacture = new ArrayCollection();

        /** @var Creance $creance */
        foreach($debiteur->getCreances() as $creance)
        {
            if(!$creance->isFactured())
            {
                $creancesToFacture->add($creance);
            }
        }

        /*
         * Dans le cas ou il y a des créance sans facture
         */
        if(!$creancesToFacture->isEmpty())
        {
            $this->facturationCreanceToDebiteur($debiteur,$creancesToFacture);
        }
    }

    /**
     * Facture les créances d'un débiteur
     *
     * Les créances doivent être toutes au débiteur
     *
     * @param Debiteur $debiteur
     * @param ArrayCollection $creances
     */
    private function facturationCreanceToDebiteur(Debiteur $debiteur, ArrayCollection $creances)
    {
        /*
         * check que c'est bien des créance
         */
        if($this->isCollectionOfCreance($creances))
        {
            if($this->areCreancesFromSameDebiteur($debiteur, $creances))
            {
                $facture = new Facture();

                if($debiteur->getOwner() instanceof Membre)
                {

                    /*
                     * On vérifie que la facturation se fait pour
                     * le membre ou sa famille, ensuite on met le
                     * bon débiteur.
                     *
                     */
                    /** @var Membre $membre */
                    $membre = $debiteur->getOwner();

                    switch($membre->getEnvoiFacture())
                    {
                        case Membre::Facture_to_membre:
                            $facture->setDebiteur($debiteur);
                            break;
                        case Membre::Facture_to_famille:
                            $facture->setDebiteur($membre->getFamille()->getDebiteur());
                            break;
                    }

                }
                if($debiteur->getOwner() instanceof Famille)
                {
                    /*
                     * c'est toujours le debiteur de famille qui recoit
                     * la facture.
                     */
                    $facture->setDebiteur($debiteur);
                }

                $facture->setCreances($creances)
                    ->setDateCreation(new \DateTime('now'))
                    ->setStatut(Facture::OPEN);

                $this->factureRepository->save($facture);
            }
        }
    }

    /**
     * cette fonction permet de facturer une liste de créance à leur débiteur
     *
     * a noter: les créance peuvent venir de plusieur débiteur
     *
     * @param ArrayCollection $creances
     */
    public function facturationCreances(ArrayCollection $creances)
    {
        /*
         * 1er etap: regrouper les créances par débiteur
         */
        $debiteurs = new ArrayCollection();

        /** @var Creance $creance */
        foreach($creances as $creance)
        {
            $idDebiteur = $creance->getDebiteur()->getId();

            if(!$debiteurs->containsKey($idDebiteur))
            {
                $debiteurs->set($idDebiteur,new ArrayCollection(array($creance)));
            }
            else
            {
                /** @var ArrayCollection $collectionCreances */
                $collectionCreances = $debiteurs->get($idDebiteur);
                $collectionCreances->add($creance);
                $debiteurs->set($idDebiteur,$collectionCreances);
            }
        }

        /*
         * 2eme etape: facturer les créance par débiteur
         */
        /**
         * @var  $id
         * @var ArrayCollection $collectionCreances
         */
        foreach($debiteurs as $id=>$collectionCreances)
        {
            /** @var Creance $creance */
            $creance = $collectionCreances->first();
            $this->facturationCreanceToDebiteur($creance->getDebiteur(),$collectionCreances);
        }

    }

    /**
     * Permet de vérifier que c'est bien une collection de creances
     *
     * @param ArrayCollection $creances
     * @return boolean
     */
    private function isCollectionOfCreance(ArrayCollection $creances)
    {
        foreach ($creances as $creance) {
            if (!($creance instanceof Creance))
                throw new Exception('object must be instance of Creance');
        }
        return true;
    }

    /**
     * @param Debiteur $debiteur
     * @param ArrayCollection $creances
     * @return bool
     */
    private function areCreancesFromSameDebiteur(Debiteur $debiteur, ArrayCollection $creances)
    {
        /** @var Creance $creance */
        foreach ($creances as $creance) {
            if (!$debiteur->getCreances()->contains($creance)) {
                return false;
            }
        }
        return true;
    }


}