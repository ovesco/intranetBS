<?php

namespace AppBundle\Form\Facture;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use AppBundle\Form\Creance\CreanceRepartitionType;
use AppBundle\Form\Rappel\RappelRepartitionType;

class FactureRepartitionType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('creances', 'collection', array('label'=>'Créances','type' => new CreanceRepartitionType()))
            ->add('rappels', 'collection', array('label'=>'Rappels','type' => new RappelRepartitionType()));


        ;//fin de la fonction builder

    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Facture'
        ));
    }


    public function getName()
    {
        return 'app_bundle_facture_repartition';
    }

}
