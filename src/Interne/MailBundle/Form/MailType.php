<?php

namespace Interne\MailBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;


class MailType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title','text',array('label' => 'Titre'))
            ->add('document',new DocumentType())
        ;

    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Interne\MailBundle\Entity\Mail'
        ));
    }


    public function getName()
    {
        return 'interne_mail_bundle_mail_type';
    }

}