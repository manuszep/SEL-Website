<?php

namespace AppBundle\Security;

use AppBundle\Entity\Service;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class ServiceVoter extends Voter
{
    // these strings are just invented: you can use anything
    const EDIT = 'edit';

    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::EDIT))) {
            return false;
        }

        // only vote on Service objects inside this voter
        if (!$subject instanceof Service) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        if ($this->decisionManager->decide($token, array('ROLE_EDITOR'))) {
            return true;
        }


        // you know $subject is a Service object, thanks to supports
        /** @var Service $service */
        $service = $subject;

        switch($attribute) {
            case self::EDIT:
                echo "case edit";
                return $this->canEdit($service, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canEdit(Service $service, User $user)
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        return $user === $service->getUser();
    }
}
