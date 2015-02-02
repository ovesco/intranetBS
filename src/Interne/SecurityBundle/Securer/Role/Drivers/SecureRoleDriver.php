<?php

namespace Interne\SecurityBundle\Securer\Role\Drivers;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Interne\SecurityBundle\Securer\Role\CoreRoleSecurer;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Interne\SecurityBundle\Securer\Annotations\SecureRole;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


class SecureRoleDriver
{
    private $reader;
    private $securer;

    public function __construct(Reader $reader, CoreRoleSecurer $securer)
    {
        $this->reader  = $reader;
        $this->securer = $securer;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        if (!is_array($controller = $event->getController()))
            throw new \Exception("L'annotation @SecureRole ne peut être utilisée que dans des controllers.");


        $object      = new \ReflectionObject($controller[0]);
        $method      = $object->getMethod($controller[1]);
        $annotations = new ArrayCollection();
        $granted     = true;

        foreach ($this->reader->getMethodAnnotations($method) as $configuration) {

            if ($configuration instanceof SecureRole) {

                $rolesString = str_replace(' ', '', $configuration->value);
                $roles       = explode(',', $rolesString);

                $granted     = $this->securer->hasRoles($roles);
            }
        }

        if(!$granted) throw new AccessDeniedException("Accès refusé");
    }
}