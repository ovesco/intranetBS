<?php

namespace AppBundle\Utils\Parametre;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Parser;

use Doctrine\ORM\EntityManager;


/**
 * Service: Parametre
 *
 * Ce service permet la gestion des parametres du site comme par exemple:
 * - un texte dans un mail automatique
 * - un texte sur une factures générée automatiquement
 * - les infos sur le groupe scout qui l'utilise
 * - - adresse
 * - - logo
 * - etc
 *
 * On utilise se service principalement pour acceder à un parametre à la fois
 * avec la fonction:
 *
 * $param = $this->get('parametres')->getValue($groupe,$name);
 *
 * Notes: les parametres sont classé par groupe et nom dans le fichier Parametre.yml
 *
 * Class Parametres
 * @package AppBundle\Utils\Parametre
 */
class Parametres
{
    /** @var  EntityManager */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }


    /**
     * Cette méthode est utilisée pour récupérer un parametre via le
     * service.
     *
     * @param string $parameterName
     * @return mixed
     */
    public function get($parameterName)
    {
        $parameter = $this->em->getRepository('AppBundle:Parameter')->findOneBy(array('name'=>$parameterName));
        if($parameter != null)
        {
            return $parameter->getData();
        }
        else
        {
            throw new Exception("Parameter '".$parameterName."' is not currently stored in database. Check the parameters.yml and maybe launch the ParameterCommand.php");
        }
    }




    /*
     *
     *
     *
     *
     *
     * tout ce qui est en dessous de ceci est plus d'acutalité
     *
     *
     *
     *
     *
     */


    public function getType($groupe,$name)
    {
        throw new Exception("Cette fonction du service parametre est désactivée...ne plus l'utiliser. question:->uffer");
        //return $this->groupesOfParametres[$groupe]['parametres'][$name]['type'];
    }

    /**
     * retourne un tableau avec tout les parametres
     *
     * @return mixed
     */
    public function getParametres()
    {
        throw new Exception("Cette fonction du service parametre est désactivée...ne plus l'utiliser. question:->uffer");
        /*
        $parameters = $this->groupesOfParametres;
        foreach($parameters as $k_groupe => $groupe)
        {
            foreach($parameters[$k_groupe]['parametres'] as $k_parametre => $parametre)
            {
                $value = $this->getValue($k_groupe,$k_parametre);

                $parameters[$k_groupe]['parametres'][$k_parametre]['value'] = $value;
            }
        }

        return $parameters;
        */
    }

    /**
     * Permet d'acceder à la valeur d'un parametre en fonction de son groupe et nom.
     *
     * @param $groupe
     * @param $name
     * @return null|string
     */
    public function getValue($groupe,$name)
    {

        throw new Exception("Cette fonction du service parametre est désactivée...ne plus l'utiliser. question:->uffer");

        /*

        $valuePath = $this->path.'/values/'.$groupe.'_'.$name.'.txt';

        if (file_exists($valuePath)) {

            $file = fopen($valuePath,'r');
            $size = filesize($valuePath);
            if($size > 0)
            {
                $value = fread($file,$size);
                fclose($file);
                return $value;
            }
            else
            {
                fclose($file);
                return null;
            }

        }

        return null;
        */



    }

    /**
     * Engegistre la valeur d'un parametre dans son fichier
     *
     * @param $groupe
     * @param $name
     * @param $value
     */
    public function setValue($groupe,$name,$value)
    {
        throw new Exception("Cette fonction du service parametre est désactivée...ne plus l'utiliser. question:->uffer");

        /*
        //on reconstruit le chemin pour le fichier
        $valuePath = $this->path.'/values/'.$groupe.'_'.$name.'.txt';
        //on recrite le contenu
        file_put_contents($valuePath,$value);
        */
    }


    /**
     * fonction pas vraiment utile pour remetre en forme le fichier de parametre.yml
     */
    public function save()
    {
        throw new Exception("Cette fonction du service parametre est désactivée...ne plus l'utiliser. question:->uffer");

        /*
        $dumper = new Dumper();
        $yaml = $dumper->dump($this->groupesOfParametres,3);
        file_put_contents($this->path.'/Parametre.yml', $yaml);
        */
    }


}