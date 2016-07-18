<?php

namespace AppBundle\Form\Mail;


use AppBundle\Entity\Mail;
use AppBundle\Form\Document\DocumentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class MailType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array('label' => 'Titre'))
            ->add('method', ChoiceType::class, array(
                'label' => 'Methode d\'envois',
                'choices' => array(
                    Mail::METHOD_EMAIL_AND_POST => 'Email et courrier',
                    Mail::METHOD_EMAIL => 'Email uniquement',
                    Mail::METHOD_POST => 'Courrier uniquement')
            ))
            ->add('document', new DocumentType());

    }

    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Mail'
        ));
    }


    public function getBlockPrefix()
    {
        return 'app_bundle_mail_type';
    }

}