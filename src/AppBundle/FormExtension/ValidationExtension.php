<?php

namespace AppBundle\FormExtension;

use Symfony\Component\Form\AbstractTypeExtension;

class ValidationExtension extends AbstractTypeExtension
{
    /**
    * Retourne le nom du type de champ qui est étendu
    *
    * @return string Le nom du type qui est étendu
    */
    public function getExtendedType()
    {
        return 'appBundle_famille';
    }
}