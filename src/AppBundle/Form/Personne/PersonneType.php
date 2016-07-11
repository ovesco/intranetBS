<?php

namespace AppBundle\Form\Personne;

use AppBundle\Field\GenreType;
use AppBundle\Form\Contact\ContactType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class PersonneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('prenom', TextType::class, array('required' => false, 'label' => 'Prénom'))
            ->add('sexe', GenreType::class)
            ->add('contact', ContactType::class)
            ->add(
                'iban',
                TextType::class,
                array(
                    'label' => 'IBAN',
                    'required' => false,
                    'attr'  => array(
                        'data-formatter' => 'true',
                        'data-pattern'   => '{{aa}} {{99}} {{99999}} {{************}}'
                    )
                )
            )
        ;
    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Personne'
        ));
    }

    public function getName()
    {
        return 'app_bundle_personne';
    }
}
