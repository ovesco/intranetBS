<?php

namespace AppBundle\Form\Payement;


use AppBundle\Entity\Creance;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use AppBundle\Entity\Payement;


class PayementValidationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('comment','textarea',array('label' => 'Remarque', 'required' => false,'attr'=>array('placeholder'=>'Une remarque qui pourrait aider dans le future...')))

        ;//fin de la fonction


        /*
         * evenement lors de l'instentitation du formulaire. cela permet d'adapter le formulaire
         * en fonction des données.
         */
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event){
            /** @var Payement $payement */
            $payement = $event->getData();
            $form = $event->getForm();

            switch($payement->getState()){
                case Payement::NOT_FOUND:
                case Payement::NOT_DEFINED:
                case Payement::FOUND_ALREADY_PAID:
                        //do not add "facture" field
                    break;
                case Payement::FOUND_LOWER:
                case Payement::FOUND_UPPER:
                case Payement::FOUND_VALID:
                    $form->add('facture', new FactureRepartitionType());
                    $form->add('montantRecu','hidden');

                    $montantDisponible = $payement->getMontantRecu();

                    $i = $payement->getFacture()->getCreances()->getIterator();
                    while($i->valid())
                    {
                        $montantEmis = $i->current()->getMontantEmis();
                        if($montantDisponible >= $montantEmis)
                        {
                            $i->current()->setMontantRecu($montantEmis);
                            $montantDisponible -= $montantEmis;
                        }
                        else{
                            $i->current()->setMontantRecu($montantDisponible);
                            $montantDisponible -= $montantDisponible;
                        }
                        $i->next();
                    }

                    $i = $payement->getFacture()->getRappels()->getIterator();
                    while($i->valid()) {
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



    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Payement'
        ));
    }


    public function getName()
    {
        return 'app_bundlePayementValidationType';
    }

}