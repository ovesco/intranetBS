<?php

namespace AppBundle\Utils\Parametre;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Definition\Exception\Exception;
use AppBundle\Entity\Parameter;

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
 * $param = $this->get('parametres')->get($name);
 *
 * Class Parametres
 * @package AppBundle\Utils\Parametre
 */
class Parametres
{
    /** @var  EntityManager */
    private $em;

    /** @var array */
    private $config;

    public function __construct(EntityManager $em,$config)
    {
        $this->em = $em;
        $this->config = $config;
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
        if(array_key_exists($parameterName,$this->config))
        {
            //le parametre existe.
            $parameter = $this->em->getRepository('AppBundle:Parameter')->findOneBy(array('name'=>$parameterName));

            if($parameter == null)
            {
                //si un parametre n'est pas trouvé, on verifie la consistance en base de donnée
                //probablement après un restart de la base de donnée
                $this->checkConfig();
                $parameter = $this->em->getRepository('AppBundle:Parameter')->findOneBy(array('name'=>$parameterName));
            }
            return $parameter->getData();
        }
        else
        {
            throw new Exception("Parameter '".$parameterName."' is not set in AppBundle->config->intranet_parameter.yml");
        }

    }

    private function checkConfig()
    {
        foreach($this->config as $parameterName => $parameterDef)
        {
            $parameter = $this->em->getRepository('AppBundle:Parameter')->findOneBy(array('name'=>$parameterName));
            if($parameter == null)
            {
                $parameter = new Parameter();

                $parameter->setName($parameterName);

                if(isset($parameterDef['type']))
                {
                    $parameter->setType($parameterDef['type']);
                }
                else
                {
                    throw new Exception("intranet_parameters with invalid type or undefined");
                }
                if(isset($parameterDef['options']))
                {
                    $parameter->setOptions($parameterDef['options']);
                }
                $this->em->persist($parameter);
            }
        }
        $this->em->flush();
    }









}