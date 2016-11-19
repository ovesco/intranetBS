<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 20.10.16
 * Time: 20:33
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Facture;
use AppBundle\Entity\Creance;
use Symfony\Component\Security\Acl\Exception\Exception;

class FactureRepository extends Repository{

    /**
     * @override
     *
     *
     * todo CMR de Nur est ce que c'est bien de mettre cette logique la? ca me parait censé mais j'aimerais avoir ton avis
     */
    public function remove($facture){

        if($facture instanceof Facture)
        {

            if($facture->isRemovable())
            {
                /** @var Creance $creance */
                foreach($facture->getCreances() as $creance)
                {
                    $creance->setFacture(null);
                }

                parent::remove($facture);
            }
            else
                throw new Exception('The facture has already received a payment');

        }
        else
            throw new Exception('The entity is not an instance of Facture');

    }

}