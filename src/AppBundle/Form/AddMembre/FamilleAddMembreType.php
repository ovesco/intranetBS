<?php

namespace AppBundle\Form\AddMembre;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Doctrine\ORM\EntityRepository;
use AppBundle\Form\FamilleType;

class FamilleAddMembreType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder
            ->add('famille',new FamilleType())
        ;
    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Membre'
        ));
    }


    public function getName()
    {
        return 'appbundle_membre_add_famille_add';
    }

}
