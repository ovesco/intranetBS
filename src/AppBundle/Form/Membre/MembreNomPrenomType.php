<?php

namespace AppBundle\Form\Membre;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;


class MembreNomPrenomType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('prenom', TextType::class,
                array(
                    'label' => 'Prénom',
                    'required' => true))
            ->add('nom', TextType::class,
                array(
                    'label' => 'Nom',
                    'mapped' => false,//Normalement, le nom est une propriété de la famille...
                    'required' => true));
    }


    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Membre'
        ));
    }


    public function getBlockPrefix()
    {
        return 'app_bundle_membre_nom_prenom';
    }

}
