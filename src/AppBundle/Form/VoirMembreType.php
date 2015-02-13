<?php

namespace AppBundle\Form;

use AppBundle\Entity\Famille;
use AppBundle\Field\MembreFamilleType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
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
            ->add('naissance','datepicker')
            ->add('sexe','genre')
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
            ->add('statut','text',array('label' => 'Statut'))

            ->add('famille',new FamilleType())

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
