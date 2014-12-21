<?php

namespace AppBundle\Utils\Listing;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Utils\Listing\Liste;

class Lister {

    private $session;
    private $em;

    /**
     * array de Listes
     * @var array
     */
    private $listes;

    public function __construct(Session $session,EntityManager $em) {

        $this->listes   = array();
        $this->session  = $session;
        $this->em       = $em;

        if($session->has('listing')) {

            $listesFromSession = $session->get('listing');

            for($i = 0; $i < count($listesFromSession); $i++) {


                $created = $this->addListe($listesFromSession[$i]['name']);
                $created->addByIds($listesFromSession[$i]['liste']);
            }
        }
    }

    /**
     * Sauve le listing en l'état dans la session
     */
    public function save() {

        $toSave = array();

        foreach($this->listes as $k => $liste)
            $toSave[] = array('liste' => $liste->liste, 'name' => $liste->name);

        $this->session->set('listing', $toSave);
    }

    /**
     * Crée une liste vide à partir d'un nom
     * @param $name string nom de la liste
     */
    public function addListe($name) {

        foreach($this->listes as $liste) {

            if($liste->name == $name)
                $name = $name . '-' . time();
        }

        $liste = new Liste($name, $this->em);

        $this->listes[$liste->getToken()] = $liste;
        return $this->listes[$liste->getToken()];
    }

    /**
     * Supprimme une liste existante par son token
     * @param $token string le token de la liste
     */
    public function removeListeByToken($token) {

        foreach($this->listes as $k => $liste) {

            if($liste->getToken() == $token)
                unset($this->listes[$k]);
        }
    }

    /**
     * Supprimme une liste existante
     * @param $name string le nom de la liste
     */
    public function removeListe($name) {

        foreach($this->listes as $k => $liste) {

            if($liste->name == $name)
                unset($this->listes[$k]);
        }
    }

    /**
     * Retourne une liste par son nom
     * @param $name string le nom de la liste
     * @return Liste la liste
     */
    public function get($name) {

        foreach($this->listes as $liste) {

            if($liste->name == $name)
                return $liste;
        }
    }

    /**
     * Retourne une liste par son token
     * @param $token string le token
     * @return Liste la liste
     */
    public function getByToken($token) {

        foreach($this->listes as $liste) {

            if($liste->getToken() == $token)
                return $liste;
        }
    }

    /**
     * retourne toutes les listes
     * @return array
     */
    public function getListes() {

        return $this->listes;
    }

    /**
     * supprimme toutes les listes
     */
    public function removeAll() {

        $this->listes = array();
    }
}