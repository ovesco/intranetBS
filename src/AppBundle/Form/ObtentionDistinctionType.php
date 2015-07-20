<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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

            /* We have to check that is doesn't exist because of form inheritance */
            if (!$form->has('membre')) {
                if (null !== $obtention->getMembre()) {
                    $form->add('membre', 'hidden', array(
                        'data' => $obtention->getMembre()->getId()
                    ));
                } else {
                    $form->add('membre', 'entity', array(
                        'class' => 'AppBundle:Membre'
                    ));
                }
            }

            if (null !== $obtention->getId()) {
                $form->add('id', 'hidden', array(
                    'data' => $obtention->getId()
                ));
            }
        });


        $builder
            ->add('date', 'datepicker', array('label' => "Reçu le"))
            ->add('distinction', 'entity', array(
                'class'		=> 'AppBundle:Distinction'
            ))
        ;
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
    public function getName()
    {
        return 'AppBundle_obtention_distinction';
    }
}
