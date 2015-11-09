<?php

namespace AppBundle\Search;

use AppBundle\Search\ModeSearchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use AppBundle\Field\SemanticType;
use AppBundle\Field\GenreType;
use AppBundle\Field\DatePickerType;
use AppBundle\Entity\Membre;

class MembreSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('mode',new ModeSearchType(),array('mapped'=>false))
            ->add('prenom','text',array('label' => 'Prénom','required'=>false))
            ->add('nom','text',array('label' => 'Nom','required'=>false))
            ->add('fromNaissance','datepicker',array('label' => 'Naissance de','required'=>false))
            ->add('toNaissance','datepicker',array('label' => 'Naissance à','required'=>false))
            ->add('sexe','genre',array('label' => 'Sexe','required'=>false))
            ->add('attribution',new AttributionSearchType())
            ;
    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
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
