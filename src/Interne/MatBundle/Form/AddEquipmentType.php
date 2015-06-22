<?php

namespace Interne\MatBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AddEquipmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('comment')
//            ->add('tags',       new TagsType, array('required' => false))
//            ->add('type',       new TypeType, array('required' => false))
        ;
    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MatBundle\Entity\Equipment'
        ));
    }

    public function getName()
    {
        return 'mat_bundle_equipmenttype';
    }
}
