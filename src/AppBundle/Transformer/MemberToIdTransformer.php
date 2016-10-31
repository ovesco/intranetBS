<?php

namespace AppBundle\Transformer;

use AppBundle\Entity\Membre;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class MemberToIdTransformer implements DataTransformerInterface
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Transforms an object (member) to a string (number).
     *
     * @param  Membre|null $member
     * @return string
     */
    public function transform($member)
    {
        if (null === $member) {
            return '';
        }

        return $member->getId();
    }

    /**
     * Transforms a string (number) to an object (member).
     *
     * @param  string $memberNumber
     * @return Membre|null
     * @throws TransformationFailedException if object (member) is not found.
     */
    public function reverseTransform($memberNumber)
    {
        // no member number? It's optional, so that's ok
        if (!$memberNumber) {
            return null;
        }

        $member = $this->manager
            ->getRepository('AppBundle:Membre')
            // query for the member with this id
            ->find($memberNumber);

        if (null === $member) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'A member with id "%s" does not exist!',
                $memberNumber
            ));
        }

        return $member;
    }
}

