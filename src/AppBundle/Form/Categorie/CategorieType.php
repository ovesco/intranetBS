<?php

namespace AppBundle\Form\Categorie;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;



class CategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom','text',array('label'=>'Nom'))
            ->add('description','textarea',array('label'=>'Description','required'=>false))
        ;
    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Categorie'
        ));
    }

    public function getName()
    {
        return 'app_bundle_categorie';
    }
}
