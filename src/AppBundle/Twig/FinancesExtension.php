<?php

namespace AppBundle\Twig;

use AppBundle\Entity\Creance;
use AppBundle\Entity\Facture;
use AppBundle\Entity\Payement;

class FinancesExtension extends \Twig_Extension
{

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'finances_extension';
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('money',array($this, 'money_filter')),
            new \Twig_SimpleFilter('ref',array($this, 'facture_ref_filter')),
            new \Twig_SimpleFilter('payement_state_icon', array($this, 'payement_state_icon')),
            new \Twig_SimpleFilter('payement_state_color', array($this, 'payement_state_color')),
            new \Twig_SimpleFilter('payement_state_text', array($this, 'payement_state_text')),
            new \Twig_SimpleFilter('creance_state', array($this, 'creance_state')),
            new \Twig_SimpleFilter('creance_state_detail', array($this, 'creance_state_detail')),
            new \Twig_SimpleFilter('facture_state', array($this, 'facture_state')),
            new \Twig_SimpleFilter('facture_state_detail', array($this, 'facture_state_detail')),
            new \Twig_SimpleFilter('payement_state', array($this, 'payement_state')),
            new \Twig_SimpleFilter('payement_state_detail', array($this, 'payement_state_detail')),
            new \Twig_SimpleFilter('payement_validation', array($this, 'payement_validation')),
        );
    }

    /**
     * Filtre pour supprimer les décimales inutiles sur les sommes d'argent
     *
     * @param $value
     * @return string
     */
    public function money_filter($value)
    {
        $decimals = $value - floor($value);
        $format = number_format($value,2,'.','\'');
        if($decimals == 0){
            $result = explode('.',$format);
            return $result[0].'.-';
        }
        else{
            return $format;
        }

    }

    public function facture_ref_filter(Facture $facture)
    {
       if($facture instanceof Facture)
       {
           return 'N°'.$facture->getId();
       }
        return '-';
    }

    public function creance_state(Creance $creance)
    {
        if($creance->isFactured())
        {
            return $this->facture_state($creance->getFacture());
        }
        return '<i class="orange wait icon popupable" data-content="En attente de facturation"></i>';
    }

    public function creance_state_detail(Creance $creance)
    {
        if($creance->isFactured())
        {
            return $this->facture_state_detail($creance->getFacture());
        }
        return '<div class="ui orange label"><i class="wait icon"></i>En attente de facturation</div>';
    }


    public function facture_state(Facture $facture)
    {
        $data = $this->processFactureRepresentation($facture->getStatut());
        return '<i class="'.$data['color'].' '.$data['icon'].' icon popupable" data-content="'.$data['text'].'"></i>';
    }

    public function facture_state_detail(Facture $facture)
    {
        $data = $this->processFactureRepresentation($facture->getStatut());
        return '<div class="ui '.$data['color'].' label"><i class="'.$data['icon'].' icon"></i>'.$data['text'].'</div>';
    }

    public function payement_validation(Payement $payement)
    {
        if($payement->isValidated())
            return '<i class="green check icon popupable" data-content="Payement validé"></i>';
        else
            return '<i class="red warning sign icon popupable" data-content="Payement non validé"></i>';
    }

    public function payement_state(Payement $payement)
    {
        $data = $this->processStateRepresentation($payement->getState());
        return '<i class="'.$data['color'].' '.$data['icon'].' icon popupable" data-content="'.$data['text'].'"></i>';
    }

    public function payement_state_detail(Payement $payement)
    {
        $data = $this->processStateRepresentation($payement->getState());
        return '<div class="ui '.$data['color'].' label"><i class="'.$data['icon'].' icon"></i>'.$data['text'].'</div>';
    }

    private function processStateRepresentation($state){
        switch($state){
            case Payement::NOT_FOUND:
                return array('color'=>'red','text'=>'Facture introuvable pour ce payement','icon'=>'warning');
                break;
            case Payement::NOT_DEFINED:
                return array('color'=>'orange','text'=>'Etat non définit','icon'=>'warning');
                break;
            case Payement::FOUND_ALREADY_PAID:
                return array('color'=>'orange','text'=>'Facture déjà payée précédement','icon'=>'warning');
                break;
            case Payement::FOUND_LOWER:
                return array('color'=>'red','text'=>'Payement reçu avec montant insuffisant','icon'=>'caret down');
                break;
            case Payement::FOUND_UPPER:
                return array('color'=>'green','text'=>'Payement reçu avec montant supérieure','icon'=>'caret up');
                break;
            case Payement::FOUND_VALID:
                return array('color'=>'green','text'=>'Payement valide','icon'=>'caret up');
                break;
            default:
                return null;
        }

    }


    private function processFactureRepresentation($state){
        switch($state){
            case Facture::PAYED:
                return array('color'=>'green','text'=>'Facture payée','icon'=>'check');
                break;
            case Facture::OPEN:
                return array('color'=>'orange','text'=>'Facture ouverte','icon'=>'circle thin');
                break;
            case Facture::CANCELLED:
                return array('color'=>'red','text'=>'Facture annulée','icon'=>'warning');
                break;
            default:
                return null;
        }

}




}