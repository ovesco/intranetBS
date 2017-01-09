<?php

namespace AppBundle\Utils\Excel\Renderer;

use AppBundle\Entity\Expediable;
use AppBundle\Entity\Groupe;
use AppBundle\Entity\Membre;

class GroupeRenderer extends AbstractRenderer {

    private $recursive = false;

    public function setRecursive($recursive = false) {

        $this->recursive    = $recursive;
        return $this;
    }

    public function render() {

        $wb     = $this->workbook();
        $row    = 0;

        /** @var Groupe $groupe */
        foreach($this->data as $groupe) {

            $membres = $this->recursive ? $groupe->getMembersRecursive() : $groupe->getMembers();

            foreach($membres as $membre) {

                $col    = 'A';

                foreach($this->membreAsRow($membre) as $info) {

                    $wb->getActiveSheet()
                        ->setCellValue($col . $row, $info);

                    $col++;
                }

                $row++;
            }
        }

        return $wb;
    }

    private function membreAsRow(Membre $membre) {

        $expediable = new Expediable($membre);

        return [
            $membre->getNumeroBs(),
            $membre->getPrenom(),
            $membre->getNom(),
            self::tryIt(function() use ($membre) { return $membre->getActiveAttribution()->getGroupe()->getNom(); }),
            self::tryIt(function() use ($membre) { return $membre->getActiveAttribution()->getFonction()->getNom(); }),

            //TODO : trouver un moyen propre de se passer d'Expediables
            //self::tryIt(function() use ($expediable) { return $expediable->getAdresse()->getRue(); }),
            //self::tryIt(function() use ($expediable) { return $expediable->getAdresse()->getNpa(); }),
            //self::tryIt(function() use ($expediable) { return $expediable->getAdresse()->getLocalite(); }),
            //$expediable->getListeEmails(),
            //implode(', ', $membre->getContact()->getTelephones())
        ];
    }

    protected function getNamespace()
    {
        return 'AppBundle:Groupe';
    }
}