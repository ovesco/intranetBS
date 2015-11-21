<?php

namespace AppBundle\Form\Geniteur;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use AppBundle\Form\Contact\ContactType;

class GeniteurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('prenom', 'text', array('required' => false, 'label' => 'Prénom'))
            ->add('profession', 'text', array('required' => false, 'label' => 'Profession'))
            ->add('contact', new ContactType())
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
