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
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($options) {
            $attribution = $event->getData();
            $form = $event->getForm();

            if (null === $attribution->getMembre()) {

                if(isset($options['attr']['multiMembre']) && true == $options['attr']['multiMembre']) {
//                    $form->add('membre', 'entity', array(
//                        'class'     => 'AppBundle:Membre',
//                        'multiple'  => true,
//                        'data'      => array('1' => '1')
//                    ));
                    $form->add('membre', 'hidden', array(
                        'data'  => $options['attr']['multiMembreIds'],
                        'attr'  => array('data-multi' => 'true')
                    ));
                }
                else {
                    $form->add('membre', 'entity', array(
                        'class' => 'AppBundle:Membre'
                    ));
                }
            }
            else
                $form->add('membre', 'hidden', array(
                    'data'  => $attribution->getMembre()->GetId()
                ));

            if (null != $attribution->getId())
                $form->add('id', 'hidden', array(
                    'data'  => $attribution->GetId()
                ));
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
