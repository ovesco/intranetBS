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

    public $groupesOfParametres; //fichier de config parsé
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
            $this->groupesOfParametres = $yaml->parse(file_get_contents($this->path.'/Parametre.yml'));
        } catch (ParseException $e) {
            printf("Unable to parse the YAML string: %s", $e->getMessage());
        }

        /*
         * On controle ici que la hierarchie des fichiers existe.
         * Si il manque un fichier, on l'ajoute.
         */
        foreach($this->groupesOfParametres as $groupeName => $groupe)
        {
            foreach($groupe['parametres'] as $parameterName => $parametre)
            {

                $dirPath = $this->path.'/values';
                if (!file_exists($dirPath)) {
                    mkdir($dirPath, 0777, true);
                }


                $valuePath = $dirPath.'/'.$groupeName.'_'.$parameterName.'.txt';

                if (!file_exists($valuePath)) {
                    $file = fopen($valuePath, "w");
                    if(isset($parametre['default'])){
                        fwrite($file,$parametre['default']);
                    }
                    fclose($file);
                }

            }
        }

    }

    /**
     * @param $groupe
     * @param $name
     * @return null|string
     */
    public function getType($groupe,$name)
    {
        return $this->groupesOfParametres[$groupe]['parametres'][$name]['type'];
    }

    /**
     * retourne un tableau avec tout les parametres
     *
     * @return mixed
     */
    public function getParametres()
    {
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
        $yaml = $dumper->dump($this->groupesOfParametres,3);
        file_put_contents($this->path.'/Parametre.yml', $yaml);
    }


}