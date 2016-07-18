<?php

namespace AppBundle\Form\Facture;

use AppBundle\Form\Creance\CreanceRepartitionType;
use AppBundle\Form\Rappel\RappelRepartitionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

class FactureRepartitionType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('creances', CollectionType::class,
                array(
                    'label' => 'Créances',
                    'entry_type' => new CreanceRepartitionType()
                ))
            ->add('rappels', CollectionType::class,
                array(
                    'label' => 'Rappels',
                    'entry_type' => new RappelRepartitionType()));

        //fin de la fonction builder
    }

    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Facture'
        ));
    }


    public function getBlockPrefix()
    {
        return 'app_bundle_facture_repartition';
    }

}
