<?php

namespace AppBundle\Field;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;

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

    public function getName()
    {
        return 'datepicker';
    }
}