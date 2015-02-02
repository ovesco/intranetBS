<?php

namespace AppBundle\Form;

use AppBundle\Entity\FonctionRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;



class GroupeModelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('fonctionChef', 'entity', array(
                'class'		=> 'AppBundle:Fonction',
                'property'	=> 'nom',
            ))
            ->add('fonctions', 'entity', array(
                'class'		=> 'AppBundle:Fonction',
                'property'	=> 'nom',
                'multiple'=>true,
                'expanded'=>false,
                'required'=>false,
            ))
            ->add('affichageEffectifs','checkbox',array('label'=>'Affichage des effectifs?','required'=>false))

        ;


    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\GroupeModel'
        ));
    }

    public function getName()
    {
        return 'appbundle_groupeModelType';
    }
}
