<?php

/**
 * EntityValidator
 * le validator va permettre de conserver la trace d'une modification
 * lorsqu'un utilisateur n'ayant pas de droit finaux modifie la base
 * de donnée
 */

namespace Interne\SecurityBundle\Validator;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Interne\SecurityBundle\Entity\Validator;

 class EntityValidator {
    
    /**      
    * @var \Symfony\Component\DependencyInjection\Container      
    */     
    private $container;     
    /**      
    * @param \Symfony\Component\DependencyInjection\ContainerInterface $container      
    */
    
    public function __construct(ContainerInterface $container)     
    {         
        $this->container = $container;
    }
    
    public function prePersist(LifecycleEventArgs $args)
    {
        /**
         * On regarde si la personne a le role ROLE_DATABASE_PERSIST
         * si il ne l'a pas, les modifications qu'il apporte  au système doivent
         * être validées
         */
        if(!$this->container->get('security.context')->isGranted('ROLE_DATABASE_PERSIST')) {
            
            $entity = $args->getEntity();
            $em     = $args->getEntityManager();
            $class  = get_class($entity);
            
            /**
             * on définit la liste des classes auxquelles le validator ne s'applique pas
             * comme les classes de la galerie (dossier, album, droit)
             */
            //var_dump($class);
            $validate = true;
            $liste = array(
                
                'Externe\GalerieBundle\Entity\Dossier',
                'Externe\GalerieBundle\Entity\Droit',
                'Externe\GalerieBundle\Entity\Album',
            );
            
            foreach($liste as $cls) {
                
                if($entity instanceof $cls) {
                    
                    $validate = false;
                }
            }
            
            //var_dump($validate);

            //$em->clear();
            
        }
    }
    
 }