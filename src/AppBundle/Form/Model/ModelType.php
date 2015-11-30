<?php

namespace AppBundle\Form\Model;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;



class ModelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom','text',array('label'=>'Nom'))
            ->add('fonctionChef', 'entity', array(
                'class'		=> 'AppBundle:Fonction',
                'property'	=> 'nom',
                'label'=>'Fonction chef'
            ))
            ->add('fonctions', 'entity', array(
                'class'		=> 'AppBundle:Fonction',
                'property'	=> 'nom',
                'multiple'=>true,
                'expanded'=>false,
                'required'=>false,
                'label' =>'Fonctions'
            ))
            ->add('affichageEffectifs','checkbox',array('label'=>'Affichage des effectifs?','required'=>false))

            ->add('categories', 'entity', array(
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

    public function getName()
    {
        return 'app_bundle_model';
    }
}
