<?php


namespace AppBundle\Utils\ListUtils\ListModels;

use AppBundle\Entity\Parameter;
use AppBundle\Utils\ListUtils\AbstractList;
use AppBundle\Utils\ListUtils\ActionLine;
use AppBundle\Utils\ListUtils\Column;
use AppBundle\Utils\Event\EventPostAction;
use Symfony\Component\Routing\Router;

class ListModelsParameter extends AbstractList
{
    public function getDefault($items, $url = null)
    {
        $this->setItems($items);
        $this->setUrl($url);


        $this->addColumn(new Column('Parametre', function (Parameter $item) { return $item->getDescription(); }));

        $this->addColumn(new Column('Donnée', function (Parameter $item) {

            switch($item->getType())
            {
                case Parameter::TYPE_EMAIL:
                case Parameter::TYPE_STRING:
                    return $item->getData();
                case Parameter::TYPE_TEXT:
                    return substr($item->getData(),0,30).'...';



                case Parameter::TYPE_PNG:
                    return '<img class="ui image" src="'.$item->getData().'">';

                case Parameter::TYPE_CHOICE:
                default:
                    return 'Représentaiton impossible';

            }
        }));

        $this->addColumn(new Column('Type', function (Parameter $item) { return $item->getType(); }));

        if($this->isGranted('ROLE_PARAMETER'))
        {
            $parameters = function (Parameter $item) {
                return array(
                    "parameter" => $item->getId()
                );
            };
            $this->addActionLine(new ActionLine('Editer', 'edit', 'app_parameter_edit', $parameters, EventPostAction::ShowModal,null,true,false));
        }


        return $this;
    }


}


?>