<?php

namespace AppBundle\Search\Famille;

use AppBundle\Search\ModeSearchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;


class FamilleSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('mode', ModeSearchType::class)
            ->add('nom', TextType::class, array('label' => 'Nom', 'required' => false))
            ;
    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Search\Famille\FamilleSearch'
        ));
    }


    public function getBlockPrefix()
    {
        return 'AppBundle_famille_search';
    }

}
