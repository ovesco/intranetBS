<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 20.10.16
 * Time: 20:33
 */

namespace AppBundle\Repository;

class PayementRepository extends Repository{

    public function findNotValidated(){

        return $this->findBy(array('validated'=>false));

    }

}