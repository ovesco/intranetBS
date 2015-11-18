<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use AppBundle\Form\ContactType;
use AppBundle\Form\AdresseType;


/**
 * todo checker si cette class est utilisée qqpart...je crois que c'est pas vraiment très utils vu que geniteur est abstract. (uffer 16.11.2015)
 *
 * Class GeniteurType
 * @package AppBundle\Form
 */
class GeniteurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('prenom', 'text', array('required' => false, 'label' => 'Prénom'))
            ->add('profession', 'text', array('required' => false, 'label' => 'Profession'))
            ->add('contact', new ContactType())
        ;
    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Geniteur'
        ));
    }

    public function getName()
    {
        return 'appbundle_geniteurtype';
    }
}
