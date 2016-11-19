<?php

namespace AppBundle\Search\Payement;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Search\NumericIntervalSearchType;
use AppBundle\Search\DateIntervalSearchType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use AppBundle\Entity\Payement;


class PayementSearchType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('idFacture', NumberType::class,
                array('label' => 'N°Facture', 'required' => false))
            ->add('intervalMontantRecu', NumericIntervalSearchType::class, array('label' => 'Montant reçu', 'required' => false))
            ->add('intervalDate', DateIntervalSearchType::class, array('label' => 'Date de création', 'required' => false))
            ->add('validated',CheckboxType::class,array('required'=>false,'label'=>'Payement validé?'))
            ->add('state', ChoiceType::class,
                array(
                    'label' => 'Lien avec facture ',
                    'required' => false,
                    'choices' =>
                        array(
                            Payement::NOT_FOUND => 'Facture inexistante',
                            Payement::FOUND_VALID => 'Facture payée',
                            Payement::FOUND_LOWER => 'Facture payée avec montant inférieur',
                            Payement::FOUND_UPPER => 'Facture payée avec montant supérieur',
                            Payement::FOUND_ALREADY_PAID => 'Facture déjà payée',
                )));//fin de la fonction


    }

    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Search\Payement\PayementSearch'
        ));
    }


    public function getBlockPrefix()
    {
        return 'app_bundle_payement_search_type';
    }

}