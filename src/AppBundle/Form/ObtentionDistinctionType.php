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
        });


        $builder
            ->add('date', 'date', array(
                'attr'	=> array('class' => 'datepicker')
            ))
            ->add('distinction', 'entity', array(
                'class'		=> 'AppBundle:Distinction'
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
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
        return 'appbundle_obtentiondistinction';
    }
}
