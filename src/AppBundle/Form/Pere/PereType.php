<?php

namespace AppBundle\Form\Pere;

use AppBundle\Form\Geniteur\GeniteurType;
use Symfony\Component\Form\FormBuilderInterface;


class PereType extends GeniteurType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder,$options);
    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Pere'
        ));
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_pere';
    }
}
