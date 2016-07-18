<?php

namespace AppBundle\Form\Payement;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;


class PayementAddMultipleType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('multiple_payement', CollectionType::class, array(
                'allow_add'=>true,
                'prototype'=>true,
                'by_reference' => false,
                'entry_type' => new PayementAddType()))
        ;//fin de la fonction


    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            //'data_class' => 'AppBundle\Entity\Payement'
        ));
    }


    public function getBlockPrefix()
    {
        return 'app_bundlePayementAddMultipleType';
    }

}