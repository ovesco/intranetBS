<?php

namespace Interne\OrganisationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class NewsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('subject','text',array('required' => true, 'label' => 'Sujet'))
            ->add('content', TextareaType::class, array('required' => true, 'label' => 'Contenu'))
        ;


    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Interne\OrganisationBundle\Entity\News'
        ));
    }

    public function getBlockPrefix()
    {
        return 'InterneOrganisation_NewsType';
    }
}