<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 23.01.17
 * Time: 18:17
 */

namespace AppBundle\Utils\Listing;

use AppBundle\Entity\Listing;
use AppBundle\Entity\Membre;
use AppBundle\Entity\User;
use AppBundle\Repository\ListingRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class ButtonGenerator {

    /** @var User */
    private $user;

    /** @var  ListingRepository */
    private $repository;

    public function __construct(ListingRepository $repository, TokenStorage $token)
    {
        $this->repository = $repository;
        $this->user = $token->getToken()->getUser();
    }

    public function generate($entity)
    {
        if($entity instanceof Membre)
        {
            $listings = $this->repository->listingOfUser($this->user,Membre::class);

            return $this->render($entity,$listings);
        }

        return null;
    }

    private function render($entity,$listings)
    {
        $html = '';
        /** @var Listing $list */
        foreach($listings as $list)
        {
            $html = $html.'<div class="item" data-url="'..'"">'.$list->getName().'</div>';
        }
        return '<div class="ui menu listing_menu">'.$html.'</div>';

    }


}