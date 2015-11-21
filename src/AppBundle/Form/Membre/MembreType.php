<?php

namespace AppBundle\Form\Membre;

use AppBundle\Form\Attribution\AttributionType;
use AppBundle\Form\Famille\FamilleType;
use AppBundle\Form\ObtentionDistinction\ObtentionDistinctionType;
use AppBundle\Form\Personne\PersonneType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MembreType extends PersonneType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder,$options);

        $builder
            ->add('famille', new FamilleType())
            ->add('attributions','collection', array('type' => new AttributionType()))
            ->add('distinctions','collection', array('type' => new ObtentionDistinctionType()))
            ->add('naissance','datepicker', array('label' => 'Date de naissance'))
            ->add('numeroAvs','number',
                array(
                    'label' => 'Numéro AVS',
                    'required' => false,
                    'attr' => array(
                        'data-formatter' => 'true',
                        'data-pattern'   => '{{9999999999999}}'
                    )
                )
            )
            ->add('numeroBs','number',
                array(
                    'label' => 'Numéro BS',
                    'required' => false,
                )
            )

            ->add(
                'remarques',
                'textarea',
                array(
                    'required' => false
                )
            )
            ->add('envoiFacture','choice', array('choices' => array('Membre' => 'Membre', 'Famille' => 'Famille')))
            ->add('inscription','datepicker',array('label' => 'Inscription'))
            ->add('statut','text', array('label' => 'Statut'))
            ->add(
                'id',
                'hidden'
            );
    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Membre'
        ));
    }


    public function getName()
    {
        return 'app_bundle_membre';
    }

}
