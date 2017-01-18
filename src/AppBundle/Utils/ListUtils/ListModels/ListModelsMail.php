<?php


namespace AppBundle\Utils\ListUtils\ListModels;

use AppBundle\Entity\Famille;
use AppBundle\Entity\Membre;
use AppBundle\Utils\ListUtils\AbstractList;
use AppBundle\Utils\ListUtils\ActionLine;
use AppBundle\Utils\ListUtils\Column;
use AppBundle\Utils\ListUtils\ListModel;
use AppBundle\Utils\ListUtils\ListModelInterface;
use AppBundle\Utils\ListUtils\ListRenderer;
use AppBundle\Entity\Mail;
use AppBundle\Entity\ReceiverMembre;
use AppBundle\Entity\ReceiverFamille;
use Symfony\Component\Routing\Router;
use AppBundle\Twig\AppExtension;

class ListModelsMail extends AbstractList
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

        $this->addColumn(new Column('Mail', function (Mail $item) { return $item->getTitle(); }));

        $twig = $this->twig;
        $this->addColumn(new Column('Courrier', function (Mail $item) use ($twig) {

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

        $this->addColumn(new Column('Emails', function (Mail $item) use ($twig) {


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


        $this->addColumn(new Column('Sender', function (Mail $item) {

            $owner = $item->getSender()->getOwner();
            if($owner instanceof Membre)
            {
                return $owner->getNom().' '.$owner->getPrenom();
            }
            return null;
        }));

        $router = $this->router;
        $this->addColumn(new Column('Document', function (Mail $item) use ($router) {
            if(is_null($item->getDocument()))
                return 'No documents';
            $name = $item->getDocument()->getName();
            $downloadPath = $router->generate('app_document_download',array('document'=>$item->getDocument()->getId()));

            $source = '<a href="'.$downloadPath.'" class="popupable" data-html="Télécharger fichier source:<br>'.$name.'"><i class="file outline icon"></i></a>';

            $print = '<a href="" class="popupable" data-content="Imprimer avec adresse"><i class="print icon"></i></a>';

            return $source.$print;
        }));

        return $this;
    }

    /**
     * @param $items
     * @param string $url
     * @return ListRenderer
     */
    public function getMyMail($items, $url = null)
    {
        $twig = $this->twig;
        $router = $this->router;
        $this->setItems($items);
        $this->setUrl($url);

        $this->addColumn(new Column('Mail', function (Mail $item) { return $item->getTitle(); }));

        $this->addColumn(new Column('Courrier', function (Mail $item) use ($twig) {

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

                /** @var AppExtension $ext */
                $ext = $twig->getExtension('app_extension');
                return $ext->popup('<i class="'.$color.' mail icon"></i>',$date_info.'<br>'.$adresse);
            }
            return '-';

        }));

        $this->addColumn(new Column('Emails', function (Mail $item) use ($twig) {


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


        $this->addColumn(new Column('Pour', function (Mail $item) {

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


        $this->addColumn(new Column('Document', function (Mail $item) use ($router) {
            if(is_null($item->getDocument()))
                return 'No documents';
            $name = $item->getDocument()->getName();
            $downloadPath = $router->generate('app_document_download',array('document'=>$item->getDocument()->getId()));

            $source = '<a href="'.$downloadPath.'" class="popupable" data-html="Télécharger fichier source:<br>'.$name.'"><i class="file outline icon"></i></a>';

            $print = '<a href="" class="popupable" data-content="Imprimer avec adresse"><i class="print icon"></i></a>';

            return $source.$print;
        }));

        return $this;
    }

}


?>