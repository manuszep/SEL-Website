<?php

namespace SelServiceBundle\Security;

use SelServiceBundle\Entity\Service;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class ServiceVoter extends Voter
{
    // these strings are just invented: you can use anything
    const EDIT = 'edit';
    const CREATE = 'create-service';
    const DELETE = 'delete';
    const COMMENT = 'comment-service';

    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    protected function supports($attribute, $subject)
    {
        if (in_array($attribute, array(self::CREATE, self::COMMENT))) {
            return true;
        }

        if (in_array($attribute, array(self::EDIT, self::DELETE))) {
            if (! $subject instanceof Service) {
                return false;
            }

            return true;
        }

        return false;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        if ($attribute == self::CREATE || $attribute == self::COMMENT) {
            return true;
            // Being logged-in is enough to create some services or comment
        }

        if ($this->decisionManager->decide($token, array('ROLE_EDITOR'))) {
            return true;
        }


        // you know $subject is a Service object, thanks to supports
        /** @var Service $service */
        $service = $subject;

        switch($attribute) {
            case self::EDIT:
                return $this->canEdit($service, $user);
            case self::DELETE:
                return $this->canDelete($service, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canEdit(Service $service, User $user)
    {
        return $user === $service->getUser();
    }

    private function canDelete(Service $service, User $user)
    {
        return $user === $service->getUser();
    }
}
