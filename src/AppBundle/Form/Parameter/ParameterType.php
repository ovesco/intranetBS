<?php

namespace AppBundle\Form\Paramter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use AppBundle\Entity\Parameter;
use Symfony\Component\Validator\Constraints\File;

class ParameterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //on récupère l'entité utilisée pour créer le formulaire
        /** @var Parameter $parameter */
        $parameter = $builder->getData();

        switch($parameter->getType())
        {
            case Parameter::TYPE_STRING:
                $builder->add('data','text',array('label'=>$parameter->getLabel()));
                break;

            case Parameter::TYPE_TEXT:
                $builder->add('data','textarea',array('label'=>$parameter->getLabel()));
                break;

            case Parameter::TYPE_EMAIL:
                $builder->add('data','email',array('label'=>$parameter->getLabel()));
                break;

            case Parameter::TYPE_CHOICE:

                $choices = array();
                foreach($parameter->getOptions('choices') as $subarray)
                {
                    foreach($subarray as $key=>$value)
                    {
                        $choices[$key]=$value;
                    }
                }

                $builder->add('data','choice',array(
                    'label'=>$parameter->getLabel(),
                    'choices'=>$choices));
                break;

            case Parameter::TYPE_PNG:
                $builder->add('data','file',array(
                    'label'=>$parameter->getLabel(),
                    'data_class'=>null,
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

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Parameter'
        ));
    }

    public function getName()
    {
        return 'app_bundle_parameter';
    }
}
