<?php

namespace AppBundle\Utils\Listing;

use Doctrine\ORM\EntityManager;

class Liste {

    /**
     * Conteneur de membres
     * Ne contient que les IDs des membres, facilite le stockage en session et la vitesse
     * @var array
     */
    public $liste;

    /**
     * Nom de la liste
     * @var string
     */
    public $name;

    private $em;

    public function __construct($name, EntityManager $em) {

        $this->name     = $name;
        $this->liste    = array();
        $this->em       = $em;
    }

    /**
     * La méthode add permet d'ajouter des membre à la liste. La méthode va automatiquement
     * détecter les éventuels doublons et les supprimer
     * @param $membres array les membres à ajouter
     */
    public function add(array $membres) {

        foreach($membres as $membre) {

            if(!in_array($membre->getId(), $this->liste))
                $this->liste[] = $membre->getId();
        }
    }

    /**
     * Meme principe que add, mais prend en paramètre un array d'ids, plus pratique pour les requêtes
     * ajax.
     * @param array $ids les ids des membres à ajouter
     */
    public function addByIds(array $ids) {

        for($i = 0; $i < count($ids); $i++) {

            if(!in_array($ids[$i], $this->liste))
                $this->liste[] = $ids[$i];
        }
    }

    /**
     * Retourne un tableau des membres contenus dans la liste
     * @return array
     */
    public function getAll() {

        $membres = array();

        for($i = 0; $i < count($this->liste); $i++)
            $membres[] = $this->em->getRepository('AppBundle:Membre')->find($this->liste[$i]);

        return $membres;
    }

    /**
     * Vide la liste
     */
    public function emptyList() {

        $this->liste = array();
    }

    /**
     * Retourne la taille de la liste
     * @return int
     */
    public function getLength() {

        return count($this->liste);
    }

    /**
     * Supprimme les éléments de la liste à partir d'une liste de membres
     * @param $membres array les membres à supprimer
     */
    public function remove(array $membres) {

        $newList = array();

        for($i = 0; $i < count($this->liste); $i++) {

            $toRemove = false;

            foreach($membres as $m) {

                if($this->liste[$i] == $m->getId())
                    $toRemove = true;
            }

            if(!$toRemove)
                $newList[] = $this->liste[$i];
        }

        $this->liste = $newList;
    }

    /**
     * Supprimme les éléments de la liste à partir d'une liste d'ids de membres
     * @param array $ids les ids a supprimer
     */
    public function removeByIds(array $ids) {

        $newList = array();

        for($i = 0; $i < count($this->liste); $i++) {

            $toRemove = false;

            for($j = 0; $j < count($ids); $j++) {

                if($this->liste[$i] == $ids[$j])
                    $toRemove = true;
            }

            if(!$toRemove)
                $newList[] = $this->liste[$i];
        }

        $this->liste = $newList;
    }

    /**
     * Retourne le token de la liste.
     * Le token est géneré par md5 du nom de la liste
     */
    public function getToken() {

        return md5($this->name);
    }

    /**
     * Vérifie si un membre est déjà dans la liste
     * @param $id int l'id du membre
     * @return boolean
     */
    public function contains($id) {

        return in_array($id, $this->liste);
    }
}