<?php

namespace AppBundle\Utils\ListUtils\ListModels;


use AppBundle\Entity\PayementFile;
use AppBundle\Utils\Event\EventPostAction;
use AppBundle\Utils\ListUtils\AbstractList;
use AppBundle\Utils\ListUtils\ActionLine;
use AppBundle\Utils\ListUtils\Column;
use AppBundle\Utils\ListUtils\ListRenderer;

class ListModelsPayementFile extends  AbstractList
{

    /**
     * @param $items
     * @param string $url
     * @return ListRenderer
     */
    public function getDefault($items, $url = null)
    {
        $this->setItems($items);
        $this->setUrl($url);

        $router = $this->getRouter();

        $this->addColumn(new Column('Fichier', function (PayementFile $file) use ($router) {
            return $file->getFilename();
        }));

        $this->addColumn(new Column('Information', function (PayementFile $file) use ($router) {
            return $file->getInfos();
        }));


        $this->addColumn(new Column('Date', function (PayementFile $file) use ($router) {
            return $file->getDate();
        },'date(global_datetime_format)'));

/*
        $userParameters = function (User $user) {
            return array(
                "user" => $user->getId()
            );
        };

        $this->addActionLine(new ActionLine('Modifier', 'edit', 'app_user_edit', $userParameters, EventPostAction::Link));

        $this->addActionLine(new ActionLine('Voir', 'zoom', 'app_user_show', $userParameters, EventPostAction::Link));


        //return '<a href="' . $router->generate('app_membre_show', array('membre' => $membre->getId())) . '">' . $membre->getPrenom() . '</a>';

*/
        return $this;
    }

}


?>