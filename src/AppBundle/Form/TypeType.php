<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('fonctionChef', 'semantic', array(

                'class'		=> 'AppBundle:Fonction',
                'property'	=> 'nom'
            ))
            ->add('affichageEffectifs','checkbox',array('label'=>'Affichage des effectifs?'))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Type'
        ));
    }

    public function getName()
    {
        return 'appbundle_typetype';
    }
}
