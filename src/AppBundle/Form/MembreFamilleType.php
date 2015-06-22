<?php

namespace AppBundle\Form;

use AppBundle\Field\FamilleValueType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


/**
 * Utilisé uniquement pour la sélection de famille dans le formulaire d'ajout de membres
 * @package AppBundle\Form
 */
class MembreFamilleType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add(
                'famille',
                new FamilleValueType(),
                array(
                    'class' => 'AppBundle:Famille',
                    'property' => 'nom'
                )
            )
        ;
    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {

        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Membre',
        ));

    }

    public function getName()
    {
        return 'membre_famille';
    }
}