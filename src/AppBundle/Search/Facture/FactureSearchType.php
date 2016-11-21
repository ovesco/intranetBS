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
use AppBundle\Search\ModeSearchType;

class FactureSearchType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mode', ModeSearchType::class)
            ->add('id', NumberType::class, array('label' => 'Num. de référance', 'required' => false))
            ->add('intervalDateCreation', DateIntervalSearchType::class, array('label' => 'Date de création', 'required' => false))
            ->add('intervalDatePayement', DateIntervalSearchType::class, array('label' => 'Date de payement', 'required' => false))
            ->add('intervalMontantEmis', NumericIntervalSearchType::class, array('label' => 'Montant emis', 'required' => false))
            ->add('intervalMontantRecu', NumericIntervalSearchType::class, array('label' => 'Montant reçu', 'required' => false))
            ->add(
                'statut', ChoiceType::class, array(
                'label' => 'Statut',
                'required' => false,
                'choices' => array(Facture::OPEN => 'Ouverte', Facture::PAYED => 'Payée',Facture::CANCELLED => 'Annulée'),
            ))
            ->add('nombreRappels', IntegerType::class, array('label' => 'Nombre de rappels', 'required' => false))
            ->add('debiteur', TextType::class, array('label' => 'Propriétaire', 'required' => false))
            ->add('titreCreance', TextType::class, array('label' => 'Titre d\'une créances', 'required' => false))
        ;


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
