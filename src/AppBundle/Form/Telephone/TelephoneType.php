<?php

namespace AppBundle\Form\Telephone;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class TelephoneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('telephone', TextType::class, array('required' => false, 'label' => 'Numéro'))
            ->add('remarques', TextareaType::class, array('required' => false, 'label' => 'Remarque'));
    }

    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Telephone'
        ));
    }

    public function getName()
    {
        return 'app_bundle_telephone';
    }
}