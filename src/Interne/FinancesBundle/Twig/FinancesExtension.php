<?php

namespace Interne\FinancesBundle\Twig;

use Interne\FinancesBundle\Entity\Creance;
use Interne\FinancesBundle\Entity\Facture;
use Interne\FinancesBundle\Entity\Payement;

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
            new \Twig_SimpleFilter('payement_state_icon', array($this, 'payement_state_icon')),
            new \Twig_SimpleFilter('payement_state_color', array($this, 'payement_state_color')),
            new \Twig_SimpleFilter('payement_state_text', array($this, 'payement_state_text')),
            new \Twig_SimpleFilter('statut_label', array($this, 'statut_label')),
            new \Twig_SimpleFilter('creance_facture_status', array($this, 'creance_facture_status')),
            new \Twig_SimpleFilter('creance_is_payed', array($this, 'creance_is_payed')),
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


    public function creance_is_payed(Creance $creance)
    {
        if($creance->isFactured())
        {
            if($creance->isPayed())
            {
                return '<i class="bordered inverted green checkmark icon popupable" data-content="Payée"></i>';
            }
        }
        return '<i class="bordered inverted red remove icon popupable" data-content="Impayée"></i>';
    }


    public function creance_facture_status(Creance $creance){
        if($creance->isFactured())
        {
            return 'N° '.$creance->getFacture()->getId();

        }
        else
        {
            return '<i class="bordered inverted orange wait icon popupable" data-content="En attente de facturation"></i>';
        }

    }


    public function statut_label($statut)
    {
        if($statut == Facture::PAYEE){
            return '<div class="ui label blue">Ouverte</div>';
        }
        elseif($statut == Facture::OUVERTE){
            return '<div class="ui label green">Payée</div>';
        }
        elseif($statut == Creance::EN_ATTENTE){
            return '<div class="ui label orange">En attente</div>';
        }
        return null;
    }



    public function payement_state_icon($state)
    {
        $data = $this->processStateRepresentation($state);
        return $data['icon'];
    }

    public function payement_state_text($state)
    {
        $data = $this->processStateRepresentation($state);
        return $data['text'];
    }

    public function payement_state_color($state)
    {
        $data = $this->processStateRepresentation($state);
        return $data['color'];
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




}