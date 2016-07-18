<?php

namespace AppBundle\Form\Famille;

use AppBundle\Form\Contact\ContactType;
use AppBundle\Form\Mere\MereType;
use AppBundle\Form\Pere\PereType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class FamilleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, array('required' => false, 'label' => 'Nom de famille'))
            ->add('pere', PereType::class, array('required' => false))
            ->add('mere', MereType::class, array('required' => false))
            ->add('contact', ContactType::class)
        ;
    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Famille',
        ));
    }

    public function getBlockPrefix()
    {
        return 'AppBundle_famille';
    }
}
