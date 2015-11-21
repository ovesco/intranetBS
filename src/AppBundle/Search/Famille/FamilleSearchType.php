<?php

namespace AppBundle\Search\Famille;

use AppBundle\Search\ModeSearchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class FamilleSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('mode',new ModeSearchType())
            ->add('nom','text',array('label' => 'Nom','required'=>false))
            ;
    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Search\Famille\FamilleSearch'
        ));
    }


    public function getName()
    {
        return 'AppBundle_famille_search';
    }

}
