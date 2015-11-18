<?php

namespace AppBundle\Form\AddMembre;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class FamilleChoiceMembreType extends AbstractType
{

    private $matchedFamilles;

    public function __construct($matchedFamilles)
    {
        $this->matchedFamilles = $matchedFamilles;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$familleIds = array(3,4);

        $builder
            ->add('prenom','text',array('label' => 'Prénom'))

            ->add('famille', 'entity', array(
                'class'		=> 'AppBundle:Famille',
                'multiple'=>false,
                'required'=>false,
                'choices'=>$this->matchedFamilles,
                'empty_data'=>'Autre famille',
            ))



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
        return 'appbundle_membre_add_step_two';
    }

}
