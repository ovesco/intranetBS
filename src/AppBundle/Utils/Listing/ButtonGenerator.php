<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 23.01.17
 * Time: 18:17
 */

namespace AppBundle\Utils\Listing;

use AppBundle\Entity\Listing;
use AppBundle\Entity\Membre;

class ButtonGenerator {

    public function __contruct(){

    }

    public function generate($entity)
    {
        if($entity instanceof Membre)
        {
            return 'coucou';
        }

        return null;
    }

}