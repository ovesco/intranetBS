<?php

namespace AppBundle\Form\Adresse;

use AppBundle\Field\BooleanType;
use AppBundle\Field\RemarqueAccordionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AdresseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('expediable', BooleanType::class, array('label' => 'expediable'))
            ->add('rue', TextType::class, array('required' => false, 'label' => 'Rue'))
            ->add('npa', NumberType::class, array('required' => false, 'label' => 'NPA'))
            ->add('localite', TextType::class, array('required' => false, 'label' => 'Localité'))
            ->add('remarques', RemarqueAccordionType::class, array('required' => false))
        ;
    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Adresse'
        ));
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_adresse';
    }
}
