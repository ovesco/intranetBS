<?php

namespace AppBundle\Form\Attribution;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

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
                    $form->add('membre', HiddenType::class, array(
                        'data' => $attribution->getMembre()->GetId()
                    ));
                } else {
                    $form->add('membre', EntityType::class, array(
                        'class' => 'AppBundle:Membre'
                    ));
                }
            }

            if (null !== $attribution->getId()) {
                $form->add('id', HiddenType::class, array(
                    'data' => $attribution->GetId()
                ));
            }
        });

        $builder
            ->add('dateDebut', DateType::class, array(
                'widget' => 'single_text'
            ))
            ->add('dateFin', DateType::class, array(
                'widget' => 'single_text',
                'required' => false
            ))
            ->add('groupe', EntityType::class, array(
                'class'		=> 'AppBundle:Groupe'
            ))
            ->add('fonction', EntityType::class, array(
                'class'		=> 'AppBundle:Fonction'
            ))
            ->add('remarques', TextareaType::class, array(
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

    public function getBlockPrefix()
    {
        return 'app_bundle_attribution';
    }
}
