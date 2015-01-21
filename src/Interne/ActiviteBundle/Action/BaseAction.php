<?php

namespace AppBundle\Action;

use AppBundle\Entity\Membre;

/**
 * Class BaseAction
 * Offre un squelette de base pour les actions qui n'ont pas besoin d'implémenter des fonctionnalités complexes
 * et fournit quelques méthodes de base obligatoires à chaque action
 * @package AppBundle\Action
 */
abstract class BaseAction implements ActionInterface {

    protected $concernedMembre;

    protected $throwerMembre;

    protected $datePeremption;

    public function setConcernedMembre(Membre $membre) {
        $this->concernedMembre = $membre;
    }

    public function getConcernedMembre() {
        return $this->concernedMembre;
    }

    public function setDatePeremption(\DateTime $datePeremption) {
        $this->datePeremption = $datePeremption;
    }

    public function getDatePeremption() {
        return $this->datePeremption;
    }

    public function setThrowerMembre(Membre $membre) {
        $this->throwerMembre = $membre;
    }

    public function getThrowerMembre() {
        return $this->throwerMembre;
    }
}