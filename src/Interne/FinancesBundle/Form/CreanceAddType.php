<?php

namespace Interne\FinancesBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;


class CreanceAddType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'titre',
                'text',
                array('label' => 'Titre')
            )

            ->add(
                'remarque',
                'textarea',
                array('label' => 'Remarque', 'required' => false)
            )
            ->add(
                'montantEmis',
                'number',
                array('label' => 'Montant')
            )
            ->add(
                'idOwner',
                'hidden',
                array(  'required' => false,
                        'mapped' => false)
            )
            ->add(
                'classOwner',
                'hidden',
                array(  'required' => false,
                        'mapped' => false)
            )
            ->add(
                'idsMembre',
                'hidden',
                array(  'required' => false,
                        'mapped' => false)
            )

            ;//fin de fonction


    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Interne\FinancesBundle\Entity\Creance'
        ));
    }


    public function getName()
    {
        return 'InterneFinancesBundle_creanceAddType';
    }

}