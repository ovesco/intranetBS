<?php

namespace Interne\SecurityBundle\Voters\Abstracts;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Class StructureVoter
 * Base des voters qui vérifient des ressources dans la structure, c'est-à-dire Membre, Famille et Groupe
 * @package Interne\SecurityBundle\Voters\Abstracts
 */
abstract class StructureVoter implements VoterInterface {

    public function supportsAttribute($attribute)
    {
        return in_array($attribute, array('view','edit','add','remove'));
    }


}