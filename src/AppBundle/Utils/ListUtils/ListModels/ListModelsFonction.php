<?php

namespace AppBundle\Utils\ListUtils\ListModels;

use AppBundle\Utils\Event\EventPostAction;
use AppBundle\Utils\ListUtils\AbstractList;
use AppBundle\Utils\ListUtils\ActionLine;
use AppBundle\Utils\ListUtils\ActionList;
use AppBundle\Utils\ListUtils\Column;
use AppBundle\Utils\ListUtils\ListRenderer;
use Symfony\Component\Routing\Router;
use AppBundle\Entity\Fonction;


class ListModelsFonction extends  AbstractList
{

    public function getDefault($items, $url = null)
    {
        $this->setItems($items);
        $this->setUrl($url);
        $this->setName('fonction_default');

        $this->addColumn(new Column('Nom', function (Fonction $fonction) {
            return $fonction->getNom();
        }));

        $this->addColumn(new Column('Abréviation', function (Fonction $fonction) {
            return $fonction->getAbreviation();
        }));

        $this->addColumn(new Column('Roles', function (Fonction $fonction) {
            $roles = '';
            foreach($fonction->getRoles() as $role)
            {
                $roles = $roles.$role.'<br>';
            }
            return $roles;
        }));


        $parameters = function (Fonction $fonction) {
            return array(
                "fonction" => $fonction->getId()
            );
        };

        /* Editer la fonction courant */
        $edit = new ActionLine('Modifier', 'edit', 'app_fonction_edit', $parameters, EventPostAction::ShowModal);
        $edit->setInMass(false);
        $this->addActionLine($edit);

        $delete = new ActionLine('Supprimer', 'remove', 'app_fonction_remove', $parameters, EventPostAction::RefreshPage);
        $delete->setInMass(false);
        $delete->setCondition(function(Fonction $fonction){return $fonction->isRemovable();});
        $this->addActionLine($delete);


        $this->addActionList(new ActionList('Ajouter', 'add', 'app_fonction_add', function(){return array();}, EventPostAction::ShowModal,null,'green'));

        $this->addExportFormats(self::FORMAT_EXPORT_XLSX,'Exporter en xlsx');
        $this->addExportFormats(self::FORMAT_EXPORT_CSV,'Exporter en csv');


        return $this;
    }

}


?>