<?php

namespace AppBundle\Form\Listing;

use AppBundle\Entity\Listing;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ListingType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('entity',ChoiceType::class,array(
                    'choices' => array(
                        'Membre' => Listing::ENTITY_MEMBRE,
                        'Facture' => Listing::ENTITY_FACTURE),
                    'choices_as_values' => true
                )
            )
            ;
    }


    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Listing',
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'app_entity_listing';
    }
}
