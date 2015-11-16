<?php

namespace AppBundle\Entity;

/**
 * IMPORTANT: Le format de sortie de "getAdresse" et "getListeEmails" sont important d'être conservé car
 * il impact enormément de code.
 *
 * Cette class n'est pas une entité doctrine.
 *
 * Elle sert uniquement à regrouper le code "similaire" à Membre et Famille pour
 * obtenir les adresses et les email Expediable.
 *
 * Elle est utiliser uniquement dans méthode imposée par ExpediableInterface.
 *
 *
 * Class Expediable
 * @package AppBundle\Entity
 */
class Expediable
{
    const OWNER_ENTITY = "ownerEntity";
    const ADDRESS = "adresse";
    const EMAIL = "email";

    private $callerEntity;
    private $callerClass;

    private $membre;
    private $famille;
    private $mere;
    private $pere;

    private $adresse;
    private $listeEmails;


    public function __construct($callerEntity)
    {

        $this->callerEntity = $callerEntity;
        $this->callerClass = $callerEntity->className();

        switch($this->callerClass)
        {
            case Membre::className():
                $this->membre = $this->callerEntity;
                $this->famille = $this->callerEntity->getFamille();
                $this->pere = ($this->famille->getPere() == null) ? null : $this->famille->getPere();
                $this->mere = ($this->famille->getMere() == null) ? null : $this->famille->getMere();
                break;
            case Famille::className():
                $this->membre = null;
                $this->famille = $this->callerEntity;
                $this->pere = ($this->famille->getPere() == null) ? null : $this->famille->getPere();
                $this->mere = ($this->famille->getMere() == null) ? null : $this->famille->getMere();
                break;
        }

        $this->adresse = $this->findAdresse();
        $this->listeEmails = $this->findListeEmails();
    }

    private function findAdresse()
    {
        $entities = array($this->membre,$this->famille,$this->mere,$this->pere);

        foreach($entities as $entity)
        {
            if($entity != null)
            {
                $adresse = $entity->getContact()->getAdresse();
                if (!is_null($adresse)) {
                    if ($adresse->isExpediable()) {
                        return array(Expediable::OWNER_ENTITY=>$entity,Expediable::ADDRESS=>$adresse);
                    }
                }
            }
        }
        return null;
    }

    private function findListeEmails()
    {
        $entities = array($this->membre,$this->famille,$this->mere,$this->pere);
        $listeEmails = array();
        foreach($entities as $entity)
        {
            if($entity != null)
            {
                $emails = $entity->getContact()->getEmails();
                if (!is_null($emails)) {
                    foreach($emails as $email)
                    {
                        if ($email->isExpediable()) {
                            $listeEmails[] = array(Expediable::OWNER_ENTITY=>$entity,Expediable::EMAIL=>$email);
                        }
                    }
                }
            }
        }
        return $listeEmails;
    }

    public function getAdresse()
    {
        return $this->adresse;
    }

    public function getListeEmails()
    {
        return $this->listeEmails;
    }


}
