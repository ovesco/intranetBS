<?php

namespace Interne\FinancesBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class CreanceSearchType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre','text',array('label' => 'Titre', 'required' => false))
            ->add('remarque','textarea',array('label' => 'Remarque', 'required' => false))
            ->add('fromDateCreation','date',array('label' => 'De', 'required' => false))
            ->add('toDateCreation','date',array('label' => 'à', 'required' => false))
            ->add('fromMontantEmis','number',array('label' => 'De', 'required' => false))
            ->add('toMontantEmis','number',array('label' => 'à', 'required' => false))
            ->add('fromMontantRecu','number',array('label' => 'De', 'required' => false))
            ->add('toMontantRecu','number',array('label' => 'à', 'required' => false))
            ->add('idFacture','number',array('label' => 'Facture (n°)', 'required' => false))
            ->add('nomMembre','text',array('label' => 'Nom', 'required' => false))
            ->add('prenomMembre','text',array('label' => 'Prénom', 'required' => false))
            ->add('nomFamille','text',array('label' => 'Nom de famille', 'required' => false))

        ;

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Interne\FinancesBundle\SearchClass\CreanceSearch'
        ));
    }


    public function getName()
    {
        return 'InterneFinancesBundle_CreanceSearch_Type';
    }

}