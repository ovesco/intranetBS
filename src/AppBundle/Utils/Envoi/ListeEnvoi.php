<?php

namespace AppBundle\Utils\Envoi;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Templating\EngineInterface;

use AppBundle\Entity\Membre;
use AppBundle\Entity\Famille;
use AppBundle\Utils\Export\Pdf;
use AppBundle\Utils\Email\Email;
use \Swift_Attachment;


class ListeEnvoi {

    private $session;
    private $em;


    /**
     * array d'Envoi
     * @var array
     */
    private $envois;

    /**
     * Le constructeur est appelé par le service...donc pas besoin de l'appeler
     *
     * @param Session $session
     * @param EntityManager $em
     *
     */
    public function __construct(Session $session,EntityManager $em) {

        $this->envois   = array(); //contient les envois
        $this->session  = $session;
        $this->em       = $em;

        if($this->session->has('ListeEnvoi')) {
            //On récupère le tableau qui contient les envois.
            $this->envois = $this->session->get('ListeEnvoi');
        }
    }

    /**
     * Cette méthode est utilisée pour créer un envoi.
     * Les envois sont adressés à des Membres ou Famille.
     *
     * @param $ownerId
     * @param $ownerClass
     * @param Pdf $pdf
     * @param null $description
     */
    public function addEnvoi($ownerId,$ownerClass, Pdf $pdf, $description = null)
    {
        $envoi = new Envoi($ownerId,$ownerClass,$pdf,$description,$this->em);
        //on sauve par clé token dans le tableau
        $this->envois[$envoi->getToken()] = $envoi;

        //on sauve la liste
        $this->save();
    }

    /**
     * A appeler après chaque utilisation de la liste d'Envois.
     */
    public function save()
    {
        $this->session->set('ListeEnvoi',$this->envois);
    }

    public function clearEnvois()
    {
        $this->envois = null;
        $this->session->remove('ListeEnvoi');

    }

    /**
     * @param $token
     * @return mixed
     */
    public function getEnvoiByToken($token)
    {
        $envoi =  $this->envois[$token];
        return $this->reconstructEnvoi($envoi);

    }

    /**
     * @param $token
     */
    public function removeEnvoiByToken($token)
    {
        unset($this->envois[$token]);
    }

    /**
     * Cette méthode renvoie les envois avec en plus les Membres/Familles
     * qui sont propriétaire de l'envoi.
     * On accède par clé au tableau renvoyé par cette méthode.
     *
     * 'envoi' l'objet d'envoi.
     * 'owner' le Membre ou la Famille proprétaire de l'envoi, on peut connaitre la class avec un getClass() sur l'objet.
     * 'token' le token de l'envoi.
     *
     * @return array
     */
    public function getEnvois()
    {
        $arrayEnvois = array();
        foreach($this->envois as $envoi)
        {
            array_push($arrayEnvois,$this->reconstructEnvoi($envoi));
        }
        return $arrayEnvois;
    }


    /**
     * Cette méthode renvoie un envoi avec en plus les Membres/Familles
     * qui sont propriétaire de l'envoi.
     * On accède par clé au tableau renvoyé par cette méthode.
     *
     * 'envoi' l'objet d'envoi.
     * 'owner' le Membre ou la Famille proprétaire de l'envoi, on peut connaitre la class avec un getClass() sur l'objet.
     * 'token' le token de l'envoi.
     *
     * @param $envoi
     * @return array
     */
    private function reconstructEnvoi($envoi)
    {

        $owner = null;

        if($envoi->ownerClass == 'Membre')
        {
            $owner = $this->em->getRepository('AppBundle:Membre')->find($envoi->ownerId);
        }
        elseif($envoi->ownerClass == 'Famille')
        {
            $owner = $this->em->getRepository('AppBundle:Famille')->find($envoi->ownerId);
        }

        return array('envoi'=>$envoi,'owner'=>$owner,'token' => $envoi->getToken());
    }


}