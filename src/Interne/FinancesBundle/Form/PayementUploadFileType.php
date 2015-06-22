<?php

namespace Interne\FinancesBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\File;


class PayementUploadFileType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file','file',array(
                'attr'=>array('accept'=>'.v11'),
                'constraints' => new File(array('maxSize' => '200K'))))


        ;//fin de la fonction




    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {

        $resolver->setDefaults(array(
            //'data_class' => 'Interne\FinancesBundle\Entity\Payement'
        ));

    }


    public function getName()
    {
        return 'InterneFinancesBundlePayementUploadFileType';
    }

}