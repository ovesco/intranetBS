<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 31.08.16
 * Time: 16:04
 */

namespace AppBundle\Handler;

use Doctrine\ORM\EntityManager;

class UserHandler extends EntityHandler {

    public function __construct(EntityManager $em){

        parent::__construct($em,'AppBundle:User');

    }

}