<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use AppBundle\Entity\Geniteur;
use AppBundle\Entity\Adresse;

class FamilleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',        'text', array('required'	=> false, 'label' => 'Nom de famille'))
            ->add('pere',       new GeniteurType, array('required' => false))
            ->add('mere',       new GeniteurType, array('required' => false))
            ->add('contact',    new ContactType())
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
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
