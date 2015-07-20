<?php

namespace AppBundle\Form;

use AppBundle\Field\FamilleValueType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use AppBundle\Entity\Geniteur;
use AppBundle\Entity\Adresse;

class AddFamilleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',        new FamilleValueType(),
                array(
                    'required'=> false,
                    'label' => 'Nom de famille',
                ))
            ->add('pere',       new GeniteurType, array('required' => false))
            ->add('mere',       new GeniteurType, array('required' => false))
            ->add('contact',    new ContactType())
        ;
    }

    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
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
