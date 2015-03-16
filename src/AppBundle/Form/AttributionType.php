<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AttributionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
            $attribution = $event->getData();
            $form = $event->getForm();

            if (null === $attribution->getMembre())
                $form->add('membre', 'entity', array(
                    'class'		=> 'AppBundle:Membre'
                ));
            else
                $form->add('membre', 'hidden', array(
                    'data'  => $attribution->getMembre()->GetId()
                ));
        });

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
            $attribution = $event->getData();
            $form = $event->getForm();

            if (null != $attribution->getId())
                $form->add('id', 'hidden', array(
                    'data'  => $attribution->GetId()
                ));
        });

        $builder
            ->add('dateDebut', 'date', array(
                'attr'		=> array(
                    'placeholder'   => 'YYYY-MM-JJ',
                    'class'         => 'datepicker',
                    'value'         => date('Y-m-d', time()))
            ))
            ->add('dateFin', 'date', array(
                'required'	=> false,
                'attr'		=> array(
                    'placeholder'   => 'YYYY-MM-JJ',
                    'class'         => 'datepicker'),
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
