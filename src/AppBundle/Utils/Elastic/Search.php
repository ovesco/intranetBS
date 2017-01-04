<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 03.11.15
 * Time: 16:50
 */

namespace AppBundle\Utils\Elastic;

use AppBundle\Search\Membre\MembreSearch;
use FOS\ElasticaBundle\Repository;
use FOS\ElasticaBundle\Manager\RepositoryManagerInterface;
use AppBundle\Search\Membre\MembreRepository;
use AppBundle\Search\Famille\FamilleSearch;

class Search{

    /** @var RepositoryManagerInterface */
    private $elasticManager;

    public function __construct(RepositoryManagerInterface $elasticManager)
    {
        $this->elasticManager = $elasticManager;
    }

    public function Membre(MembreSearch $membreSearch)
    {
        /** @var MembreRepository $repository */
        $repository = $this->elasticManager->getRepository('AppBundle:Membre');
        return  $repository->search($membreSearch);
    }

    public function Famille(FamilleSearch $membreSearch)
    {
        /** @var MembreRepository $repository */
        $repository = $this->elasticManager->getRepository('AppBundle:Famille');
        return  $repository->search($membreSearch);
    }

}