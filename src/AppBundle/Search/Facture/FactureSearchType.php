<?php

namespace AppBundle\Search\Facture;

use AppBundle\Entity\Facture;
use AppBundle\Search\DateIntervalSearchType;
use AppBundle\Search\NumericIntervalSearchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class FactureSearchType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', NumberType::class, array('label' => 'Num. de référance', 'required' => false))
            ->add('intervalDateCreation', DateIntervalSearchType::class, array('label' => 'Date de création', 'required' => false))
            ->add('intervalDatePayement', DateIntervalSearchType::class, array('label' => 'Date de payement', 'required' => false))
            ->add('intervalMontantEmis', NumericIntervalSearchType::class, array('label' => 'Montant emis', 'required' => false))
            ->add('intervalMontantRecu', NumericIntervalSearchType::class, array('label' => 'Montant reçu', 'required' => false))
            ->add(
                'statut', ChoiceType::class, array(
                'label' => 'Statut',
                'required' => false,
                'choices' => array(Facture::OUVERTE => 'Ouverte', Facture::PAYEE => 'Payée'),
            ))
            ->add('nombreRappels', IntegerType::class, array('label' => 'Nombre de rappels', 'required' => false))
            ->add('debiteur', TextType::class, array('label' => 'Propriétaire', 'required' => false))
            ->add('titreCreance', TextType::class, array('label' => 'Titre d\'une créances', 'required' => false))/*

         eCreation','date',array('label' => 'à', 'required' => false))

            ->add('titreCreance','text',array('label' => 'Titre de la créance', 'required' => false))
            ->add('fromMontantEmisCreance',NumberType::class,array('label' => 'De', 'required' => false))
            ->add('toMontantEmisCreance',NumberType::class,array('label' => 'a', 'required' => false))
            ->add('fromDateCreationCreance','date',array('label' => 'De', 'required' => false))
            ->add('toDateCreationCreance','date',array('label' => 'à', 'required' => false))

            ->add('nombreRappels',NumberType::class,array('label' => 'Nombre de Rappel', 'required' => false))
            ->add('fromMontantEmisRappel',NumberType::class,array('label' => 'De', 'required' => false))
            ->add('toMontantEmisRappel',NumberType::class,array('label' => 'a', 'required' => false))
            ->add('fromDateCreationRappel','date',array('label' => 'De', 'required' => false))
            ->add('toDateCreationRappel','date',array('label' => 'à', 'required' => false))


            ->add('fromMontantRecu',NumberType::class,array('label' => 'De', 'required' => false))
            ->add('toMontantRecu',NumberType::class,array('label' => 'a', 'required' => false))

            ->add('fromDatePayement','date',array('label' => 'De', 'required' => false))
            ->add('toDatePayement','date',array('label' => 'à', 'required' => false))


            ->add('statut','choice',array('label' => 'Statut','required' => false,'choices' => array('ouverte'=>'Ouverte', 'payee'=>'Payée'), 'required' => false))

            */


        ;//fin de la fonction builder


    }

    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Search\Facture\FactureSearch'
        ));
    }


    public function getBlockPrefix()
    {
        return 'app_bundle_FactureSearch_Type';
    }

}
