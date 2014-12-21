<?php

namespace AppBundle\Form;

use AppBundle\Entity\Famille;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MembreInfosScoutesType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'numeroBs',
                'text',
                array('label' => 'Numéro BS')
            )
            ->add(
                'inscription',
                'datepicker',
                array('label' => 'Inscription')
            )
            ->add(
                'statut',
                'text',
                array('label' => 'Statut')
            )
        ;
    }

    public function getName() {

        return 'appBundle_membre_infos_scoutes_type';
    }
}