<?php

namespace AppBundle\Form\Groupe;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class GroupeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('parent', EntityType::class, array(
                'class' => 'AppBundle:Groupe',
                'property' => 'nom',
                'label' => 'Groupe parent',
                'required' => false,
                'empty_value' => 'Groupe racine'
            ))
            ->add('nom')
            ->add('active', HiddenType::class, array('data' => true))
            ->add('model', EntityType::class, array(
                'class' => 'AppBundle:Model',
                'property' => 'nom'
            ));
    }

    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Groupe'
        ));
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_groupe';
    }
}
