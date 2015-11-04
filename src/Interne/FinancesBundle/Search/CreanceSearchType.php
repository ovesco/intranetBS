<?php

namespace Interne\FinancesBundle\Search;


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
            /*
            ->add('idFacture','number',array('label' => 'Facture (n°)', 'required' => false))
            ->add('nomMembre','text',array('label' => 'Nom', 'required' => false))
            ->add('prenomMembre','text',array('label' => 'Prénom', 'required' => false))
            ->add('nomFamille','text',array('label' => 'Nom de famille', 'required' => false))
            ->add('factured','choice',array('label'=>'Facturée?','choices'=>array('yes'=>'oui','no'=>'non'),'required'  => false))
            ->add('payed','choice',array('label'=>'Payée?','choices'=>array('yes'=>'oui','no'=>'non'),'required'  => false))
*/
        ;

    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Interne\FinancesBundle\Search\CreanceSearch'
        ));
    }


    public function getName()
    {
        return 'InterneFinancesBundle_creance_search_type';
    }

}