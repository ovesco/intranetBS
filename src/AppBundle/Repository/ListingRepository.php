<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 20.10.16
 * Time: 20:33
 */

namespace AppBundle\Repository;

use AppBundle\Entity\User;

class ListingRepository extends Repository{

    public function listingOfUser(User $user, $entityClass = null)
    {
        $contrainte = array('user'=>$user);

        if($entityClass != null)
        {
            $contrainte['entity'] = $entityClass;
        }

        return $this->findBy($contrainte);
    }

}