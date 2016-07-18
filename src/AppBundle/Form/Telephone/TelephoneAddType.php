<?php

namespace AppBundle\Form\Telephone;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class TelephoneAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('telephone', TextType::class, array('required' => true, 'label' => 'Numéro'))
            ->add('remarques', TextType::class, array('required' => false, 'label' => 'Remarque'))
            ->add('contact_id', HiddenType::class, array('mapped' => false));
    }

    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Telephone'
        ));
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_telephone_add';
    }
}