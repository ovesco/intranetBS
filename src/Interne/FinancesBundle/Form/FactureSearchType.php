<?php

namespace Interne\FinancesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class FactureSearchType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            /*
             * Elements appartenant aux factures
             */
            ->add('id','number',array('label' => 'Num. de référance', 'required' => false))


            ->add('fromMontantEmis','number',array('label' => 'De', 'required' => false))
            ->add('toMontantEmis','number',array('label' => 'a', 'required' => false))
            ->add('fromMontantEmisCreances','number',array('label' => 'De', 'required' => false))
            ->add('toMontantEmisCreances','number',array('label' => 'a', 'required' => false))
            ->add('fromMontantEmisRappels','number',array('label' => 'De', 'required' => false))
            ->add('toMontantEmisRappels','number',array('label' => 'a', 'required' => false))

            ->add('fromDateCreation','date',array('label' => 'De', 'required' => false))
            ->add('toDateCreation','date',array('label' => 'à', 'required' => false))

            ->add('titreCreance','text',array('label' => 'Titre de la créance', 'required' => false))
            ->add('fromMontantEmisCreance','number',array('label' => 'De', 'required' => false))
            ->add('toMontantEmisCreance','number',array('label' => 'a', 'required' => false))
            ->add('fromDateCreationCreance','date',array('label' => 'De', 'required' => false))
            ->add('toDateCreationCreance','date',array('label' => 'à', 'required' => false))

            ->add('nombreRappels','number',array('label' => 'Nombre de Rappel', 'required' => false))
            ->add('fromMontantEmisRappel','number',array('label' => 'De', 'required' => false))
            ->add('toMontantEmisRappel','number',array('label' => 'a', 'required' => false))
            ->add('fromDateCreationRappel','date',array('label' => 'De', 'required' => false))
            ->add('toDateCreationRappel','date',array('label' => 'à', 'required' => false))


            ->add('fromMontantRecu','number',array('label' => 'De', 'required' => false))
            ->add('toMontantRecu','number',array('label' => 'a', 'required' => false))

            ->add('fromDatePayement','date',array('label' => 'De', 'required' => false))
            ->add('toDatePayement','date',array('label' => 'à', 'required' => false))


            ->add('statut','choice',array('label' => 'Statut','required' => false,'choices' => array('ouverte'=>'Ouverte', 'payee'=>'Payée'), 'required' => false))




        ;//fin de la fonction builder



    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Interne\FinancesBundle\SearchClass\FactureSearch'
        ));
    }


    public function getName()
    {
        return 'InterneFinancesBundle_FactureSearch_Type';
    }

}
