<?php

namespace AppBundle\Utils\Parametre;

use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Parser;


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

    public $parametres;
    private $kernel;
    private $path;

    public function __construct($kernel)
    {

        $this->kernel = $kernel;
        $this->path = $this->kernel->getRootDir() . '/../src/AppBundle/Utils/Parametre';

        /*
         * On récupère le tableau de parametre.
         */
        $yaml = new Parser();
        try {
            $this->parametres = $yaml->parse(file_get_contents($this->path.'/Parametre.yml'));
        } catch (ParseException $e) {
            printf("Unable to parse the YAML string: %s", $e->getMessage());
        }


        /*
         * On controle ici que la hierarchie des fichiers existe.
         * Si il manque un fichier, on l'ajoute.
         */
        foreach($this->parametres as $groupe)
        {

            foreach($groupe['parametres'] as $parametre)
            {


                $valuePath = $this->path.'/values/'.$groupe['groupe'].'_'.$parametre['name'].'.txt';
                if (!file_exists($valuePath)) {
                    $file = fopen($valuePath, "w");
                    fclose($file);
                }

            }
        }

    }

    /**
     * retourne un tableau avec tout les parametres
     *
     * @return mixed
     */
    public function getParametres()
    {
        $parameters = $this->parametres;
        foreach($parameters as $k_groupe => $groupe)
        {
            foreach($groupe['parametres'] as $k_parametre => $parametre)
            {
                $value = $this->getValue($groupe['groupe'],$parametre['name']);

                $parameters[$k_groupe]['parametres'][$k_parametre]['value'] = $value;
            }
        }
        return $parameters;
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
        //on reconstruit le chemin pour le fichier
        $valuePath = $this->path.'/values/'.$groupe.'_'.$name.'.txt';
        //on recrite le contenu
        file_put_contents($valuePath,$value);
    }


    /**
     * fonction pas vraiment utile pour remetre en forme le fichier de parametre.yml
     */
    public function save()
    {
        $dumper = new Dumper();
        $yaml = $dumper->dump($this->parametres,3);
        file_put_contents($this->path.'/Parametre.yml', $yaml);
    }

}