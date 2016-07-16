<?php

namespace AppBundle\Field;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


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
        return TextareaType::class;
    }

    public function getName()
    {
        return 'remarque_accordion';
    }
}