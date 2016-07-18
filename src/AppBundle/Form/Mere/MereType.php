<?php

namespace AppBundle\Form\Mere;

use AppBundle\Form\Geniteur\GeniteurType;
use Symfony\Component\Form\FormBuilderInterface;


class MereType extends GeniteurType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder,$options);
    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Mere'
        ));
    }

    public function getBlockPrefix()
    {
        return 'appbundle_mere_type';
    }
}
