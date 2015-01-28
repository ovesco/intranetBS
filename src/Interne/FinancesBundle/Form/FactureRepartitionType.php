<?php

namespace Interne\FinancesBundle\Form;

use Interne\FactureBundle\Controller\CreanceController;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class FactureRepartitionType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('creances', 'collection', array('label'=>false,'type' => new CreanceRepartitionType()));

        $builder->add('rappels', 'collection', array('label'=>false,'type' => new RappelRepartitionType()));


        ;//fin de la fonction builder



    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Interne\FinancesBundle\Entity\Facture'
        ));
    }


    public function getName()
    {
        return 'InterneFinancesBundleFactureRepartitionType';
    }

}