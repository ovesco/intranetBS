<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 31.08.16
 * Time: 15:20
 */

namespace AppBundle\Utils\Serializer;

/**
 * Class MembreSerializer
 * @package AppBundle\Utils\Serializer
 */
class MembreSerializer extends EntitySerializer{

    public function getEntityClass(){
        return 'AppBundle\Entity\Membre';
    }

}