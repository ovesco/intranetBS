<?php


namespace AppBundle\Utils\ListUtils\ListModels;

use AppBundle\Entity\Payement;
use AppBundle\Utils\ListUtils\AbstractList;
use AppBundle\Utils\ListUtils\ActionLine;
use AppBundle\Utils\ListUtils\Column;
use AppBundle\Utils\Event\EventPostAction;
use AppBundle\Utils\ListUtils\ListRenderer;
use Symfony\Component\Routing\Router;

class ListModelsPayement extends  AbstractList
{


    /**
     * @param string $url
     * @return ListRenderer
     */
    public function getDefault($items, $url = null)
    {

        $this->setItems($items);
        $this->setUrl($url);

        $this->addColumn(new Column('', function (Payement $item) { return $item; },'payement_validation|raw'));

        $this->addColumn(new Column('Num. réf.', function (Payement $item) { return $item->getIdFacture(); }));

        $this->addColumn(new Column('Montant', function (Payement $item) { return $item->getMontantRecu(); },'money'));

        $this->addColumn(new Column('Date', function (Payement $item) {
            return $item->getDate();
        },'date(global_date_format)'));

        $this->addColumn(new Column('Etat', function (Payement $item) { return $item; },'payement_state|raw'));

        $payementParameters = function (Payement $payement) {
            return array(
                "payement" => $payement->getId()
            );
        };

        $this->addActionLine(new ActionLine('Afficher', 'zoom', 'app_payement_show', $payementParameters, EventPostAction::ShowModal,null,true,false));


        return $this;
    }


    public function getNotValidated( $items, $url = null)
    {
        $this->getDefault($items,$url);

        $payementParameters = function (Payement $payement) {
            return array(
                "payement" => $payement->getId()
            );
        };

        $this->addActionLine(new ActionLine('Valider', 'check', 'app_payement_validationform', $payementParameters, EventPostAction::ShowModal,null,true,false));


        $removeCondition = function (Payement $payement) {
            return $payement->isRemovable();
        };


        $this->addActionLine(new ActionLine('Supprimer', 'remove', 'app_payement_remove', $payementParameters, EventPostAction::RefreshList,$removeCondition,true,false));




        return $this;
    }



    /**
     * @param $items
     * @return ListRenderer
     */
    public function getSearchResults( $items, $url = null)
    {

        $this->getDefault($items,$url);


        $payementParameters = function (Payement $payement) {
            return array(
                "payement" => $payement->getId()
            );
        };

        $this->addActionLine(new ActionLine('Afficher', 'zoom', 'app_payement_show', $payementParameters, EventPostAction::ShowModal,null,true,false));



        return $this;



    }

}


?>