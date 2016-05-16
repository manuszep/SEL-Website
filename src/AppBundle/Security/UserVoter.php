<?php

namespace AppBundle\Security;

use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    // these strings are just invented: you can use anything
    const MANAGE = 'manage';

    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::MANAGE))) {
            return false;
        }

        // only vote on User objects inside this voter
        if (!$subject instanceof User) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var User $user */
        $user = $subject;

        if (!$token->getUser() instanceof UserInterface || !$user->isEnabled()) {
            return false;
        }

        switch($attribute) {
            case self::MANAGE:
                return $this->canManage($user, $token);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canManage(User $user, TokenInterface $token)
    {
        if (!$user->isEnabled()) {
            return false;
        }

        if ($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_SUPER_ADMIN')) {
            return false;
        }
        
        return $this->decisionManager->decide($token, array('ROLE_EDITOR'));
    }
}
