<?php

namespace AppBundle\Form\User;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, array('required' => true, 'label' => 'Username'))
            ->add('password', TextType::class, array('required' => true, 'label' => 'mot de passe'))
            ->add('isActive', CheckboxType::class, array('required' => false, 'label' => 'Utilisateur activé'))
            ->add('rolesEntity',EntityType::class, array('class' => 'AppBundle:Role','choice_label' => 'role','multiple'=>true,'required'=>true))
        ;
    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
        ));
    }

    public function getBlockPrefix()
    {
        return 'AppBundle_user';
    }
}
