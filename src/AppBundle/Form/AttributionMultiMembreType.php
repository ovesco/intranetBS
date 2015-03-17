<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AttributionMultiMembreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->remove('membre');

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($options) {
            $form = $event->getForm();

            if (isset($options['attr']['membres']) && $options['attr']['membres'] !==  null) {
                $form->add('membre', 'hidden', array(
                    'data' => $options['attr']['membres']
                ));
            } else {
                $form->add('membre', 'entity', array(
                    'class'     => 'AppBundle:Membre',
                    'multiple'  => true
                ));
            }
        });


    }

    public function getName()
    {
        return 'appbundle_attributionmultimembretype';
    }

    public function getParent()
    {
        return new AttributionType();
    }
}