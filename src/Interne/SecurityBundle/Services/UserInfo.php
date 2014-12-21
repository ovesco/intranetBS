<?php

namespace Interne\SecurityBundle\Services;

use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\ORM\EntityManager;



/**
 * Ce service à pour but de récupérer l'instance Membre liée à l'utilisateur
 * authentifié. On peut ainsi récupérer l'ensemble de ses informations
 */

class UserInfo
{
	/**
     * @var SecurityContext
     */
	protected $context;
	
	/**
     * @var EntityManager 
     */
	protected $em;
	
	
	
	/**
	 * @param SecurityContext $context
	 * @param EntityManager   $em
	 */
	public function __construct($context, $em)
	{
		$this->context = $context;
		$this->em	   = $em;
	}
	/**
	 * retourne une instance de l'entity membre liée à l'utilisateur
	 * connecté
	 */
	public function getUser() {
		
		//Première chose à faire, récupérer le repository
		//$membreRepo = $this->em->getRepository('InterneFichierBundle:Membre');
		
		return $this->context->getToken()->getUser()->getMembre();
	}
	
	/**
	 * permet de récupérer l'ensemble des rôles liés à l'utilisateur en fonction de
	 * ses attributions, et éventuellement de ses distinctions. 
	 */
	public function getRoles() {
		
	}
}