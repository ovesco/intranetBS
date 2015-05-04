<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use AppBundle\Field\SemanticType;
use AppBundle\Field\GenreType;
use AppBundle\Field\DatePickerType;


class VoirMembreType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('prenom','text',array('label' => 'Prénom'))
            ->add('naissance','datepicker', array('label' => 'Date de naissance'))
            ->add('sexe','genre', array('label' => 'Sexe'))
            ->add('numeroAvs','text', array('label' => 'Numéro AVS',) )
            ->add(
                'iban',
                'text',
                array(
                    'label' => 'IBAN',
                    'required' => false,
                    'attr'  => array(
                        'data-formatter' => 'true',
                        'data-pattern'   => '{{aa}} {{99}} {{99999}} {{************}}'
                    )
                )
            )

            ->add(
                'remarques',
                'textarea',
                array(
                    'required' => false
                )
            )

            ->add(
                'contact',
                new ContactType()
            )

            ->add('envoiFacture','choice', array('choices' => array('Membre' => 'Membre', 'Famille' => 'Famille')))

            ->add('numeroBs', 'text',array('label' => 'Numéro BS'))
            ->add('inscription','datepicker',array('label' => 'Inscription'))
            ->add('statut','text', array('label' => 'Statut'))
            ->add('attributions', 'collection', array('type' => new AttributionType()))
            ->add('distinctions', 'collection', array('type' => new ObtentionDistinctionType()))

            ->add(
                'id',
                'hidden'
            );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Membre'
        ));
    }


    public function getName()
    {
        return 'AppBundle_membre';
    }

}
