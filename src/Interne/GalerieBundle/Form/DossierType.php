<?php

namespace Interne\GalerieBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DossierType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('groupe', 'semantic', array(

                'class'		=> 'AppBundle:Groupe',
                'property'	=> 'nom'
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Interne\GalerieBundle\Entity\Dossier'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'interne_galeriebundle_dossier';
    }
}
