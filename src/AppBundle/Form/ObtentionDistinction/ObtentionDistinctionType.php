<?php

namespace AppBundle\Form\ObtentionDistinction;

use AppBundle\Field\DatePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ObtentionDistinctionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
            $obtention = $event->getData();
            $form = $event->getForm();

            /* We have to check that it doesn't exist because of form inheritance */
            if (!$form->has('membre')) {
                if (null !== $obtention->getMembre()) {
                    $form->add('membre', HiddenType::class, array(
                        'data' => $obtention->getMembre()->getId()
                    ));
                } else {
                    $form->add('membre', EntityType::class, array(
                        'class' => 'AppBundle:Membre'
                    ));
                }
            }

            if (null !== $obtention->getId()) {
                $form->add('id', HiddenType::class, array(
                    'data' => $obtention->getId()
                ));
            }
        });


        $builder
            ->add('date', DatePickerType::class, array('label' => "Reçu le"))
            ->add('distinction', EntityType::class, array(
                'class' => 'AppBundle:Distinction'
            ));
    }

    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\ObtentionDistinction'
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'app_bundle_obtention_distinction';
    }
}
