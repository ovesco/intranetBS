<?php

namespace AppBundle\PDFGenerator\Models;

class ListeDeTroupeModel extends resources\TemplatedModel {

    public function getName() {

        return 'liste_de_troupe';
    }

    public function setTitre() {

        $this->SetTitle('Liste de troupe');
    }
}