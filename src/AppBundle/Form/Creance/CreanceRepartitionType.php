<?php

namespace AppBundle\Form\Creance;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;


class CreanceRepartitionType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre','hidden',array('label' => false))
            ->add('montantEmis','hidden',array('label' => false))
            ->add('montantRecu','number', array('label' => false))

        ;//fin de fonction
    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Creance'
        ));
    }


    public function getName()
    {
        return 'app_bundle_creance_repartition';
    }

}