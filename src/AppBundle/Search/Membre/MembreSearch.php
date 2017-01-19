<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 01.11.15
 * Time: 18:54
 */

namespace AppBundle\Search\Membre;

use AppBundle\Search\Attribution\AttributionSearch;

class MembreSearch{

    /** @var  string */
    public $nom;

    /** @var  string */
    public $prenom;

    /** @var  \Datetime */
    public $fromNaissance;

    /** @var  \Datetime */
    public $toNaissance;

    /** @var  string */
    public $sexe;

    /** @var  AttributionSearch */
    public $attribution;

    public  function __construct()
    {
        $this->attribution = new AttributionSearch();
    }
}