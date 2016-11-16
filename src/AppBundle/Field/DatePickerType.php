<?php

namespace AppBundle\Field;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * A utiliser lorsqu'on veut une date
 *
 * @package AppBundle\Form
 */
class DatePickerType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new DateTimeToStringTransformer(null, null, 'd.m.Y'));
    }


    public function getParent()
    {
        return 'text';
    }

    /**
     * Permet de faire que le champ sera représenté par un datepicker_widget
     *
     * @return string
     *
     */
    public function getBlockPrefix()
    {
        return 'datepicker';
    }
}