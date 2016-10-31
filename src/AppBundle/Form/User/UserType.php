<?php

namespace AppBundle\Form\User;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use AppBundle\Security\RoleHierarchy;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var RoleHierarchy $rh */
        $rh = $options['app.role.hierarchy'];

        $roleChoices = array();
        foreach($rh->getAllExistingRoles() as $role)
        {
            $roleChoices[$role] = $role;
        }

        $builder
            ->add('username', TextType::class, array('required' => true, 'label' => 'Username'))
            ->add('password', TextType::class, array('required' => true, 'label' => 'mot de passe'))
            ->add('isActive', CheckboxType::class, array('required' => false, 'label' => 'Utilisateur activé'))
            ->add('selectedRoles',ChoiceType::class,array('choices' => $roleChoices,'multiple'=>true))
        ;
    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',

        ));
        //inject service in form
        $resolver->setRequired('app.role.hierarchy');
    }

    public function getBlockPrefix()
    {
        return 'AppBundle_user';
    }
}
