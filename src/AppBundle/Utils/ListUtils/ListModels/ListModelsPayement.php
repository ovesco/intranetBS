<?php


namespace AppBundle\Utils\ListUtils\ListModels;

use AppBundle\Entity\Payement;
use AppBundle\Entity\PayementFile;
use AppBundle\Utils\ListUtils\ActionLine;
use AppBundle\Utils\ListUtils\Column;
use AppBundle\Utils\ListUtils\ListModelInterface;
use AppBundle\Utils\Event\EventPostAction;
use AppBundle\Utils\ListUtils\ListRenderer;
use AppBundle\Entity\Creance;
use Symfony\Component\Routing\Router;
use AppBundle\Utils\ListUtils\ActionList;
use AppBundle\Entity\Debiteur;

class ListModelsPayement implements ListModelInterface
{


    /**
     * @param \Twig_Environment $twig
     * @param $items
     * @param Router $router
     * @param string $url
     * @return ListRenderer
     */
    static public function getDefault(\Twig_Environment $twig, Router $router, $items, $url = null)
    {

        $list = new ListRenderer($twig, $items);
        $list->setUrl($url);
        $list->setSearchBar(true);

        $list->addColumn(new Column('', function (Payement $item) { return $item; },'payement_validation|raw'));

        $list->addColumn(new Column('Num. réf.', function (Payement $item) { return $item->getIdFacture(); }));

        $list->addColumn(new Column('Montant', function (Payement $item) { return $item->getMontantRecu(); },'money'));

        $list->addColumn(new Column('Date', function (Payement $item) {
            return $item->getDate();
        },'date(global_date_format)'));

        $list->addColumn(new Column('Etat', function (Payement $item) { return $item; },'payement_state|raw'));

        return $list;
    }


    static public function getNotValidated(\Twig_Environment $twig, Router $router, $items, $url = null)
    {
        $list = self::getDefault($twig,$router,$items,$url);

        $payementParameters = function (Payement $payement) {
            return array(
                "payement" => $payement->getId()
            );
        };

        $list->addActionLine(new ActionLine('Afficher', 'zoom', 'app_payement_show', $payementParameters, EventPostAction::ShowModal,null,true,false));


        $list->addActionLine(new ActionLine('Valider', 'settings', 'app_payement_validationform', $payementParameters, EventPostAction::ShowModal,null,true,false));




        return $list;
    }



    /**
     * @param \Twig_Environment $twig
     * @param $items
     * @return ListRenderer
     */
    static public function getSearchResults(\Twig_Environment $twig, Router $router, $items, $url = null)
    {

        $list = self::getDefault($twig,$router,$items,$url);


        $payementParameters = function (Payement $payement) {
            return array(
                "payement" => $payement->getId()
            );
        };

        $list->addActionLine(new ActionLine('Afficher', 'zoom', 'app_payement_show', $payementParameters, EventPostAction::ShowModal,null,true,false));



        return $list;



    }

}


?>