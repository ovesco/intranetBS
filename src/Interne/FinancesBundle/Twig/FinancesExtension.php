<?php

namespace Interne\FinancesBundle\Twig;

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