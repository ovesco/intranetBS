<?php

namespace AppBundle\Utils\Data;

use AppBundle\Entity\Membre;
use Symfony\Component\Routing\Router;

class JsonParser {

    private $router;

    public function __construct(Router $router) {

        $this->router = $router;
    }

    /**
     * Retourne le tableau qui ira dans un champ de recherche pour une entité parmi Groupe, Membre ou Famille
     * @param string $type
     * @param Object $entity
     * @return array
     * @throws \Exception
     */
    public function toSemanticCategorySearch($type, $entity) {

        if($type == 'membre') {

            return array(

                'title'         => ucwords($entity->getPrenom() . ' ' . $entity->getNom()),
                'url'           => $this->router->generate('interne_voir_membre', array('membre' => $entity->getId())),
                'description'   => "Naissance : " . $entity->getNaissance()->format('d.m.Y') . " - Numéro BS: " . $entity->getNumeroBs(),
            );
        }

        else if($type == 'famille') {

            return array(

                'title'         => $entity->getNom(),
                'url'           => $this->router->generate('interne_voir_famille', array('famille' => $entity->getId())),
                'description'   => ($entity->getAdressePrincipale() == null) ? 'Aucune adresse trouvée' : "de " . $entity->getAdressePrincipale()['adresse']->getLocalite()
            );
        }

        else if($type == 'groupe') {

            return array(

                'title'         => $entity->getNom(),
                'url'           => $this->router->generate('interne_voir_groupe', array('groupe' => $entity->getId())),
                'description'   => count($entity->getMembersRecursive()) . " membres"
            );
        }

        else throw new \Exception("Type non compatible");
    }

    public function simplifyMembre(Membre $membre) {

        return array(

            'id'            => $membre->getId(),
            'nom'           => $membre->getNom(),
            'prenom'        => $membre->getPrenom(),
            'naissance'     => $membre->getNaissance()->format('d.m.Y'),
            'fonction'      => $membre->getActiveAttribution()->getFonction()->getNom(),
            'groupe'        => $membre->getActiveAttribution()->getGroupe()->getNom(),
            'numeroBs'      => $membre->getNumeroBs(),
            'url'           => $this->router->generate('interne_voir_membre', array('membre' => $membre->getId()))
        );
    }
}