<?php

namespace AppBundle\Form\Model;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;


class ModelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, array('label' => 'Nom'))
            ->add('fonctionChef', EntityType::class, array(
                'class'		=> 'AppBundle:Fonction',
                'property'	=> 'nom',
                'label'=>'Fonction chef'
            ))
            ->add('fonctions', EntityType::class, array(
                'class'		=> 'AppBundle:Fonction',
                'property'	=> 'nom',
                'multiple'=>true,
                'expanded'=>false,
                'required'=>false,
                'label' =>'Fonctions'
            ))
            ->add('affichageEffectifs','checkbox',array('label'=>'Affichage des effectifs?','required'=>false))
            ->add('categories', EntityType::class, array(
                'class'		=> 'AppBundle:Categorie',
                'property'	=> 'nom',
                'multiple'=>true,
                'expanded'=>false,
                'required'=>false,
            ))
        ;


    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Model'
        ));
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_model';
    }
}
