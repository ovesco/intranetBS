<?php

namespace AppBundle\Form\Email;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class EmailAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', TextType::class, array('required' => false, 'label' => 'Email'))
            ->add('remarques', TextType::class, array('required' => false, 'label' => 'Remarque'))
            ->add('contact_id', HiddenType::class, array('mapped' => false));
    }

    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Email'
        ));
    }

    public function getName()
    {
        return 'app_bundle_email_add';
    }
}