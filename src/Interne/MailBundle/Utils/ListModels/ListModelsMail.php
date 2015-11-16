<?php


namespace Interne\MailBundle\Utils\ListModels;

use AppBundle\Utils\ListUtils\Action;
use AppBundle\Utils\ListUtils\Column;
use AppBundle\Utils\ListUtils\ListModelInterface;
use AppBundle\Utils\ListUtils\ListRenderer;
use Interne\MailBundle\Entity\Mail;
use Symfony\Component\Routing\Router;

class ListModelsMail implements ListModelInterface
{

    static public function getRepresentedClass(){
        return 'Interne\MailBundle\Entity\Mail';
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

        $list->addColumn(new Column('Sent', function (Mail $item) { return ($item->isSent() ? 'oui':'non'); }));

        $list->addColumn(new Column('Emails', function (Mail $item) {

            $list_emails = '';
            foreach($item->getEmails() as $email)
            {
                $list_emails = $list_emails.' '.$email;
            }
            return $list_emails;

        }));

        $list->addColumn(new Column('Adresse', function (Mail $item) { return $item->getAddress(); }));

        $list->addColumn(new Column('Document', function (Mail $item) use ($router) {
            if(is_null($item->getDocument()))
                return 'No documents';
            $name = $item->getDocument()->getName();
            $downloadPath = $router->generate('interne_mail_document_download',array('document'=>$item->getDocument()->getId()));
            return '<a href="'.$downloadPath.'">'.$name.'</>';
        }));

        return $list;
    }

}


?>