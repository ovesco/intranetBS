<?php

namespace AppBundle\Form\Famille;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use AppBundle\Form\Pere\PereType;
use AppBundle\Form\Mere\MereType;
use AppBundle\Form\Contact\ContactType;

class FamilleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',        'text', array('required'	=> false, 'label' => 'Nom de famille'))
            ->add('pere',       new PereType, array('required' => false))
            ->add('mere',       new MereType, array('required' => false))
            ->add('contact',    new ContactType())
        ;
    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Famille',
        ));
    }

    public function getName()
    {
        return 'AppBundle_famille';
    }
}
