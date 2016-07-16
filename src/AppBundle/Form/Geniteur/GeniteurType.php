<?php

namespace AppBundle\Form\Geniteur;

use AppBundle\Form\Contact\ContactType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class GeniteurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('prenom', TextType::class, array('required' => false, 'label' => 'Prénom'))
            ->add('profession', TextType::class, array('required' => false, 'label' => 'Profession'))
            ->add('contact', ContactType::class)
        ;
    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Geniteur'
        ));
    }

    public function getName()
    {
        return 'app_bundle_geniteur';
    }
}
