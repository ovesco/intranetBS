<?php

namespace AppBundle\Search;

use AppBundle\Field\DatePickerType;
use AppBundle\Field\GenreType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class MembreSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('mode', ModeSearchType::class)
            ->add('prenom', TextType::class, array('label' => 'Prénom', 'required' => false))
            ->add('nom', TextType::class, array('label' => 'Nom', 'required' => false))
            ->add('fromNaissance', DatePickerType::class, array('label' => 'Naissance de', 'required' => false))
            ->add('toNaissance', DatePickerType::class, array('label' => 'Naissance à', 'required' => false))
            ->add('sexe', GenreType::class, array('label' => 'Sexe', 'required' => false))
            ->add('attribution', AttributionSearchType::class);
    }

    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Search\MembreSearch'
        ));
    }


    public function getName()
    {
        return 'AppBundle_membre_search';
    }

}
