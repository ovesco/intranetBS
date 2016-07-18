<?php

namespace AppBundle\Form\Paramter;

use AppBundle\Entity\Parameter;
use AppBundle\Form\Email\EmailType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;

class ParameterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //on récupère l'entité utilisée pour créer le formulaire
        /** @var Parameter $parameter */
        $parameter = $builder->getData();

        switch ($parameter->getType()) {
            case Parameter::TYPE_STRING:
                $builder->add('data', TextType::class, array('label' => $parameter->getLabel()));
                break;

            case Parameter::TYPE_TEXT:
                $builder->add('data', TextareaType::class, array('label' => $parameter->getLabel()));
                break;

            case Parameter::TYPE_EMAIL:
                $builder->add('data', EmailType::class, array('label' => $parameter->getLabel()));
                break;

            case Parameter::TYPE_CHOICE:

                $choices = array();
                foreach ($parameter->getOptions('choices') as $subarray) {
                    foreach ($subarray as $key => $value) {
                        $choices[$key] = $value;
                    }
                }

                $builder->add('data', ChoiceType::class, array(
                    'label' => $parameter->getLabel(),
                    'choices' => $choices));
                break;

            case Parameter::TYPE_PNG:
                $builder->add('data', FileType::class, array(
                    'label' => $parameter->getLabel(),
                    'data_class' => null,
                    'constraints' => [
                        new File([
                            'maxSize' => '50k',
                            'mimeTypes' => [
                                'image/png'
                            ],
                            'mimeTypesMessage' => 'Please upload a valid PDF',
                        ])
                    ]));
                break;
        }


    }

    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Parameter'
        ));
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_parameter';
    }
}
