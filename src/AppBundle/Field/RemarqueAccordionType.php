<?php

namespace AppBundle\Field;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class RemarqueAccordionType extends AbstractType
{

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'label' => 'Remarques'
            )
        );
    }

    public function getParent()
    {
        return 'textarea';
    }

    public function getName()
    {
        return 'remarque_accordion';
    }
}