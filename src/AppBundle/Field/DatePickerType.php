<?php

namespace AppBundle\Field;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class DateType
 *
 * A utiliser lorsqu'on veut une date
 *
 * @package AppBundle\Form
 */
class DatePickerType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(

            'data_class' => 'DateTime'
        ));
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