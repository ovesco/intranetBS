<?php

namespace AppBundle\Form\Payement;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Search\NumericIntervalSearchType;
use AppBundle\Search\DateIntervalSearchType;


class PayementSearchType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('idFacture', NumberType::class,
                array('label' => 'N°Facture', 'required' => false))
            ->add('intervalMontantRecu', NumericIntervalSearchType::class, array('label' => 'Montant reçu', 'required' => false))
            ->add('intervalDate', DateIntervalSearchType::class, array('label' => 'Date de création', 'required' => false))
            ->add('state', ChoiceType::class,
                array(
                    'label' => 'Lien avec facture ',
                    'required' => false,
                    'choices' =>
                        array(
                            'waiting' => 'En attente de validation',
                            'not_found' => 'Facture inexistante',
                            'found_valid' => 'Facture payée',
                            'found_lower_valid' => 'Facture payée avec montant inférieur',
                            'found_lower_new_facture' => 'Facture payée avec montant inférieur (et complément exigé)',
                            'found_upper' => 'Facture payée avec montant supérieur'
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