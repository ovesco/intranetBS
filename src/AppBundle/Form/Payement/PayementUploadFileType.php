<?php

namespace AppBundle\Form\Payement;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;


class PayementUploadFileType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', FileType::class, array(
                'attr' => array('accept' => '.v11'),
                'constraints' => new File(array('maxSize' => '200K'))));//fin de la fonction


    }

    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {

        $resolver->setDefaults(array(//'data_class' => 'AppBundle\Entity\Payement'
        ));

    }


    public function getBlockPrefix()
    {
        return 'app_bundlePayementUploadFileType';
    }

}