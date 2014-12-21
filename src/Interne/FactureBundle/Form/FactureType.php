<?php

namespace Interne\FactureBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;



class FactureType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder


            ->add(
                'montantRecu',
                'number',
                array(
                    'label' => 'Montant reçu',
                    'required' => false,
                    'data'=> 0
                )
            )

            ->add(
                'statut',
                'choice',
                array(
                    'label' => 'Statut',
                    'choices' => array('ouverte'=>'Ouverte', 'payee'=>'Payée')
                )
            )
            ->add(
                'dateCreation',
                'date',
                array(
                    'label' => 'Date de création',
                    'data' => new \DateTime()
                    )
            )
            ->add(
               'rappels',
                'collection',
                array(
                    'type'          => new RappelType(),
                    'allow_add'     => true,
                    'allow_delete'  => true,
                    'prototype'     => true,
                    'by_reference'  => false,

                )

            );



    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Interne\FactureBundle\Entity\Facture'
        ));
    }


    public function getName()
    {
        return 'InterneFactureBundle_factureType';
    }

}
