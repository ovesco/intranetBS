<?php

namespace Interne\FinancesBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class PayementUploadFileType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('file','file',array('attr'=>array('accept'=>'.v11')))

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