<?php

namespace AppBundle\Form\Payement;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class PayementAddMultipleType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('multiple_payement','collection',array(
                'allow_add'=>true,
                'prototype'=>true,
                'by_reference' => false,
                'type'   => new PayementAddType()))
        ;//fin de la fonction


    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            //'data_class' => 'AppBundle\Entity\Payement'
        ));
    }


    public function getName()
    {
        return 'app_bundlePayementAddMultipleType';
    }

}