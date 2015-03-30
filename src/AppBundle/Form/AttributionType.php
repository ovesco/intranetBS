<?php

namespace AppBundle\Form;

use AppBundle\Entity\Attribution;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AttributionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($options) {
            $attribution = $event->getData();
            $form = $event->getForm();

            /* We have to check that is doesn't exist because of form inheritance */
            if(!$form->has('membre')) {
                if (null !== $attribution->getMembre()) {
                    $form->add('membre', 'hidden', array(
                        'data' => $attribution->getMembre()->GetId()
                    ));
                } else {
                    $form->add('membre', 'entity', array(
                        'class' => 'AppBundle:Membre'
                    ));
                }
            }

            if (null !== $attribution->getId()) {
                $form->add('id', 'hidden', array(
                    'data' => $attribution->GetId()
                ));
            }
        });

        $builder
            ->add('dateDebut', 'date', array(
                'attr'		=> array(
                    'class'         => 'datepicker')
            ))
            ->add('dateFin', 'date', array(
                'required'	=> false,
                'attr'		=> array(
                    'class'         => 'datepicker')
            ))
            ->add('groupe', 'entity', array(
                'class'		=> 'AppBundle:Groupe'
            ))
            ->add('fonction', 'entity', array(
                'class'		=> 'AppBundle:Fonction'
            ))
            ->add('remarques', 'textarea', array(
                'required'	=> false,
            ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Attribution'
        ));
    }

    public function getName()
    {
        return 'appbundle_attributiontype';
    }
}
