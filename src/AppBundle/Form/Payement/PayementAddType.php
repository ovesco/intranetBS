<?php

namespace AppBundle\Form\Payement;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;


class PayementAddType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('idFacture', IntegerType::class, array('label' => 'N°Facture', 'required' => false))
            ->add('montantRecu', NumberType::class, array('label' => 'Montant reçu', 'required' => false));//fin de la fonction


    }

    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Payement'
        ));
    }


    public function getName()
    {
        return 'app_bundlePayementAddType';
    }

}