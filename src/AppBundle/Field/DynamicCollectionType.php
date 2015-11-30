<?php

namespace AppBundle\Field;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class DynamicCollectionType extends AbstractType
{

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'allow_add'=>true,
            'prototype'=>true,
            'by_reference' => false,
            )
        );
    }

    public function getParent()
    {
        return 'collection';
    }

    public function getName()
    {
        return 'dynamic_collection';
    }
}