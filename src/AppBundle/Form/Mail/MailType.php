<?php

namespace AppBundle\Form\Mail;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Mail;
use AppBundle\Form\Document\DocumentType;

class MailType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title','text',array('label' => 'Titre'))
            ->add('method','choice',array(
                'label'=>'Methode d\'envois',
                'choices'=>array(
                    Mail::METHOD_EMAIL_AND_POST=>'Email et courrier',
                    Mail::METHOD_EMAIL=>'Email uniquement',
                    Mail::METHOD_POST=>'Courrier uniquement')
            ))
            ->add('document',new DocumentType())
        ;

    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Mail'
        ));
    }


    public function getName()
    {
        return 'app_bundle_mail_type';
    }

}