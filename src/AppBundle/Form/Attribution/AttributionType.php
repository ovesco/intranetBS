<?php

namespace AppBundle\Form\Attribution;

use AppBundle\Entity\Attribution;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AttributionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
            $attribution = $event->getData();
            $form = $event->getForm();

            /* We have to check that it doesn't exist because of form inheritance */
            if (!$form->has('membre')) {
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
            ->add('dateDebut', 'datepicker')
            ->add('dateFin', 'datepicker')

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


    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Attribution'
        ));
    }

    public function getName()
    {
        return 'app_bundle_attribution';
    }
}
