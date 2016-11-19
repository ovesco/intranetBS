<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 20.10.16
 * Time: 20:33
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Payement;

class PayementRepository extends Repository{

    public function findNotValidated(){

        return $this->findBy(array('validated'=>false));

    }


    /**
     *
     * @override
     * @param $payement
     * @throws Exception
     */
    public function remove($payement){

        if($payement instanceof Payement)
        {

            if($payement->isRemovable())
            {
                parent::remove($payement);
            }
            else
                throw new Exception('The payement is linked to a facture');

        }
        else
            throw new Exception('The entity is not an instance of Payement');

    }

}