<?php

namespace Interne\FinancesBundle\Search;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use AppBundle\Search\NumericIntervalSearchType;
use AppBundle\Search\DateIntervalSearchType;
use Interne\FinancesBundle\Entity\Facture;

class FactureSearchType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('id','number',array('label' => 'Num. de référance', 'required' => false))
            ->add('intervalDateCreation',new DateIntervalSearchType() ,array('label' => 'Date de création', 'required' => false))
            ->add('intervalDatePayement',new DateIntervalSearchType() ,array('label' => 'Date de payement', 'required' => false))
            ->add('intervalMontantEmis',new NumericIntervalSearchType(),array('label' => 'Montant emis', 'required' => false))
            ->add('intervalMontantRecu',new NumericIntervalSearchType(),array('label' => 'Montant reçu', 'required' => false))
            ->add('statut','choice',array(
                'label' => 'Statut',
                'required' => false,
                'choices' => array(Facture::OUVERTE=>'Ouverte', Facture::PAYEE=>'Payée'),
                ))
            ->add('nombreRappels','integer',array('label' => 'Nombre de rappels', 'required' => false))
            ->add('debiteur','text',array('label' => 'Propriétaire', 'required' => false))
            ->add('titreCreance','text',array('label' => 'Titre d\'une créances', 'required' => false))

            /*

         eCreation','date',array('label' => 'à', 'required' => false))

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

            */



        ;//fin de la fonction builder



    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Interne\FinancesBundle\Search\FactureSearch'
        ));
    }


    public function getName()
    {
        return 'InterneFinancesBundle_FactureSearch_Type';
    }

}
