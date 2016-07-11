<?php

namespace AppBundle\Form\Membre;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class MembreFamilleChoiceType extends AbstractType
{

    /** @var array */
    private $familles;

    public function __construct($familles)
    {
        $this->familles = $familles;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('famille', EntityType::class, array(
                'class' => 'AppBundle:Famille',
                'multiple' => false,
                'required' => false,
                'choices' => $this->familles,
                'placeholder' => 'Aucune de ces familles',
            ));
    }

    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Membre'
        ));
    }


    public function getName()
    {
        return 'appbundle_membre_famille_choice';
    }

}
