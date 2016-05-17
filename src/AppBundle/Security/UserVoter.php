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
    const ENABLE = 'enable';
    const CREATE = 'create user';
    const SHOW = 'show';

    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::MANAGE, self::ENABLE, self::CREATE, self::SHOW))) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        switch($attribute) {
            case self::MANAGE:
                if (!$subject instanceof User) {
                    return false;
                }

                $user = $subject;

                return $this->canManage($user, $token);
            case self::ENABLE:
                if (!$subject instanceof User) {
                    return false;
                }

                $user = $subject;

                return $this->canEnable($user, $token);
            case self::SHOW:
                if (!$subject instanceof User) {
                    return false;
                }

                $user = $subject;

                return $this->canShow($user, $token);
            case self::CREATE:
                return $this->canCreate($token);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canShow(User $user, TokenInterface $token) {
        if ((!$user->isEnabled() || $user->isLocked()) && !$this->decisionManager->decide($token, array('ROLE_COCO'))) {
            return false;
        }

        return true;
    }

    private function canManage(User $user, TokenInterface $token)
    {
        if (!$token->getUser() instanceof UserInterface) {
            return false;
        }

        if (!$user->isEnabled()) {
            return false;
        }

        if ($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_SUPER_ADMIN')) {
            return false;
        }

        return $this->decisionManager->decide($token, array('ROLE_EDITOR'));
    }

    private function canEnable(User $user, TokenInterface $token) {
        if (!$token->getUser() instanceof UserInterface) {
            return false;
        }

        if ($user->isEnabled()) {
            return false;
        }

        return $this->decisionManager->decide($token, array('ROLE_EDITOR'));
    }

    private function canCreate(TokenInterface $token) {
        if (!$token->getUser() instanceof UserInterface) {
            return false;
        }

        return $this->decisionManager->decide($token, array('ROLE_COCO'));
    }
}
