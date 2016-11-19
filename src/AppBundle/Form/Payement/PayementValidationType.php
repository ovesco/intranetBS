<?php

namespace AppBundle\Form\Payement;


use AppBundle\Entity\Payement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;


class PayementValidationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        /** @var Payement $payement */
        $payement = $builder->getData();


        switch($payement->getState()) {
            case Payement::NOT_FOUND:
                break;
            case Payement::NOT_DEFINED:
                break;
            case Payement::FOUND_ALREADY_PAID:
                break;
            case Payement::FOUND_LOWER:
                $builder->add('new_creance', CheckboxType::class,
                    array(
                        'label' => 'Crée un créance de compensation?',
                        'required' => false,
                        'mapped'=>false
                    ));
                break;
            case Payement::FOUND_UPPER:
                break;//should not be validate
            case Payement::FOUND_VALID:
                break;//should not be validate
        }
        $builder->add('comment', TextareaType::class, array('label' => 'Remarque', 'required' => false));//fin de la fonction




        /*
         * evenement lors de l'instentitation du formulaire. cela permet d'adapter le formulaire
         * en fonction des données.
         *
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var Payement $payement *
            $payement = $event->getData();
            $form = $event->getForm();

            switch ($payement->getState()) {
                case Payement::NOT_FOUND:
                case Payement::NOT_DEFINED:
                case Payement::FOUND_ALREADY_PAID:
                    //do not add "facture" field
                    break;
                case Payement::FOUND_LOWER:
                case Payement::FOUND_UPPER:
                case Payement::FOUND_VALID:
                    $form->add('facture', new FactureRepartitionType());
                    $form->add('montantRecu', HiddenType::class);

                    $montantDisponible = $payement->getMontantRecu();

                    $i = $payement->getFacture()->getCreances()->getIterator();
                    while ($i->valid()) {
                        $montantEmis = $i->current()->getMontantEmis();
                        if ($montantDisponible >= $montantEmis) {
                            $i->current()->setMontantRecu($montantEmis);
                            $montantDisponible -= $montantEmis;
                        } else {
                            $i->current()->setMontantRecu($montantDisponible);
                            $montantDisponible -= $montantDisponible;
                        }
                        $i->next();
                    }

                    $i = $payement->getFacture()->getRappels()->getIterator();
                    while ($i->valid()) {
                        $montantEmis = $i->current()->getMontantEmis();
                        if ($montantDisponible >= $montantEmis) {
                            $i->current()->setMontantRecu($montantEmis);
                            $montantDisponible -= $montantEmis;
                        } else {
                            $i->current()->setMontantRecu($montantDisponible);
                            $montantDisponible -= $montantDisponible;
                        }
                        $i->next();
                    }

                    break;
            }


        });
        */


    }

    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Payement'
        ));
    }


    public function getBlockPrefix()
    {
        return 'app_bundlePayementValidationType';
    }

}