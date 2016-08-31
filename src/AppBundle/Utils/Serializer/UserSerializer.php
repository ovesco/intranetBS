<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 31.08.16
 * Time: 15:20
 */

namespace AppBundle\Utils\Serializer;

/**
 * Class UserSerializer
 * @package AppBundle\Utils\Serializer
 */
class UserSerializer extends EntitySerializer{

    public function getEntityClass(){
        return 'AppBundle\Entity\User';
    }

}