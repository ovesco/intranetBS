<?php


namespace AppBundle\Utils\ListUtils\ListModels;

use AppBundle\Entity\Famille;
use AppBundle\Entity\Membre;
use AppBundle\Utils\ListUtils\Action;
use AppBundle\Utils\ListUtils\Column;
use AppBundle\Utils\ListUtils\ListModelInterface;
use AppBundle\Utils\ListUtils\ListRenderer;
use AppBundle\Entity\Mail;
use AppBundle\Entity\ReceiverMembre;
use AppBundle\Entity\ReceiverFamille;
use Symfony\Component\Routing\Router;

class ListModelsMail implements ListModelInterface
{

    static public function getRepresentedClass(){
        return null;
    }

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

        $list->addColumn(new Column('Mail', function (Mail $item) { return $item->getTitle(); }));

        $list->addColumn(new Column('Courrier', function (Mail $item) use ($twig) {

            $adresse = $item->getAddress();
            if($adresse != null)
            {
                $color = '';
                $date = null;
                if($item->isPrinted())
                {
                    $color = 'green';
                    $date = $item->getLastPrintDate()->format($twig->getGlobals()['global_date_format']);
                }
                $date_info = 'En attente';
                if($date != null)
                {
                    $date_info = 'Dernière impresssion: '.$date;
                }

                return '<i class="'.$color.' mail icon popupable" data-html="<strong>'.$date_info.'</strong><br>'.$adresse.'"></i>';
            }
            return '-';

        }));

        $list->addColumn(new Column('Emails', function (Mail $item) use ($twig) {


            if(!$item->getEmails()->isEmpty())
            {
                $color = '';
                $date = null;
                if($item->isSentByMail())
                {
                    $color = 'green';
                    $date = $item->getLastEmailSentDate()->format($twig->getGlobals()['global_date_format']);
                }
                $date_info = 'En attente';
                if($date != null)
                {
                    $date_info = 'Dernière envois: '.$date;
                }
                $emails = '';
                foreach($item->getEmails() as $email)
                {
                    $emails = $emails.$email.'<br>';
                }

                return '<i class="'.$color.' at icon popupable" data-html="<strong>'.$date_info.'</strong><br>'.$emails.'"></i>';
            }
            return '-';

        }));


        $list->addColumn(new Column('Sender', function (Mail $item) {

            $owner = $item->getSender()->getOwner();
            if($owner instanceof Membre)
            {
                return $owner->getNom().' '.$owner->getPrenom();
            }
            return null;
        }));


        $list->addColumn(new Column('Document', function (Mail $item) use ($router) {
            if(is_null($item->getDocument()))
                return 'No documents';
            $name = $item->getDocument()->getName();
            $downloadPath = $router->generate('app_document_download',array('document'=>$item->getDocument()->getId()));

            $source = '<a href="'.$downloadPath.'" class="popupable" data-html="Télécharger fichier source:<br>'.$name.'"><i class="file outline icon"></i></a>';

            $print = '<a href="" class="popupable" data-content="Imprimer avec adresse"><i class="print icon"></i></a>';

            return $source.$print;
        }));

        return $list;
    }

    /**
     * @param \Twig_Environment $twig
     * @param $items
     * @param Router $router
     * @param string $url
     * @return ListRenderer
     */
    static public function getMyMail(\Twig_Environment $twig, Router $router, $items, $url = null)
    {
        $list = new ListRenderer($twig, $items);
        $list->setUrl($url);
        $list->setSearchBar(true);

        $list->addColumn(new Column('Mail', function (Mail $item) { return $item->getTitle(); }));

        $list->addColumn(new Column('Courrier', function (Mail $item) use ($twig) {

            $adresse = $item->getAddress();
            if($adresse != null)
            {
                $color = '';
                $date = null;
                if($item->isPrinted())
                {
                    $color = 'green';
                    $date = $item->getLastPrintDate()->format($twig->getGlobals()['global_date_format']);
                }
                $date_info = 'En attente';
                if($date != null)
                {
                    $date_info = 'Dernière impresssion: '.$date;
                }

                return '<i class="'.$color.' mail icon popupable" data-html="'.$date_info.'<br>'.$adresse.'"></i>';
            }
            return '-';

        }));

        $list->addColumn(new Column('Emails', function (Mail $item) use ($twig) {


            if(!$item->getEmails()->isEmpty())
            {
                $color = '';
                $date = null;
                if($item->isSentByMail())
                {
                    $color = 'green';
                    $date = $item->getLastEmailSentDate()->format($twig->getGlobals()['global_date_format']);
                }
                $date_info = 'En attente';
                if($date != null)
                {
                    $date_info = 'Dernière envois: '.$date;
                }
                $emails = '';
                foreach($item->getEmails() as $email)
                {
                    $emails = $emails.$email.'<br>';
                }

                return '<i class="'.$color.' at icon popupable" data-html="'.$date_info.'<br>'.$emails.'"></i>';
            }
            return '-';

        }));


        $list->addColumn(new Column('Pour', function (Mail $item) {

            $owner = $item->getReceiver()->getOwner();
            if($owner instanceof Membre)
            {
                return $owner->getNom().' '.$owner->getPrenom();
            }
            if($owner instanceof Famille)
            {
                return 'Famille '.$owner->getNom();
            }
            return null;
        }));


        $list->addColumn(new Column('Document', function (Mail $item) use ($router) {
            if(is_null($item->getDocument()))
                return 'No documents';
            $name = $item->getDocument()->getName();
            $downloadPath = $router->generate('app_document_download',array('document'=>$item->getDocument()->getId()));

            $source = '<a href="'.$downloadPath.'" class="popupable" data-html="Télécharger fichier source:<br>'.$name.'"><i class="file outline icon"></i></a>';

            $print = '<a href="" class="popupable" data-content="Imprimer avec adresse"><i class="print icon"></i></a>';

            return $source.$print;
        }));

        return $list;
    }

}


?>