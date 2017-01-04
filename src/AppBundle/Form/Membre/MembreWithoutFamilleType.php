<?php

namespace AppBundle\Form\Membre;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;


class MembreWithoutFamilleType extends MembreType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder,$options);
        $builder->remove('famille');
        $builder->remove('prenom');
        $builder->remove('distinctions');
        
        $builder->add(
            'prenom',
            TextType::class,
            array(
                'required' => false,
                'label' => 'Prénom',
                'disabled' => true)
        );


    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Membre'
        ));
    }

    public function getBlockPrefix()
    {
        return 'appbundle_membre_add_without_famille';
    }
}
