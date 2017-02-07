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
    const LISTUSERS = 'list users';
    const MANAGE = 'manage users';
    const ENABLE = 'enable user';
    const CREATE = 'create user';
    const EDIT = 'edit user';
    const SHOW = 'show user';
    const LOCK = 'lock user';
    const UNLOCK = 'unlock user';
    const ENABLE_COCO = 'enable coco user';
    const DISABLE_COCO = 'disable coco user';

    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    protected function supports($attribute, $subject)
    {
        if (in_array($attribute, array(self::CREATE, self::LISTUSERS))) {
            return true;
        }

        if (in_array($attribute, array(
            self::MANAGE, self::ENABLE, self::CREATE, self::EDIT, self::SHOW, self::LOCK, self::UNLOCK, self::ENABLE_COCO, self::DISABLE_COCO
        ))) {
            if (!$subject instanceof User) {
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

        if ($attribute == self::CREATE) {
            return $this->canCreate($token);
        }

        if ($attribute == self::LISTUSERS) {
            return true;
        }

        $user = $subject;

        switch($attribute) {
            case self::MANAGE:
                return $this->canManage($user, $token);
            case self::ENABLE:
                return $this->canEnable($user, $token);
            case self::EDIT:
                return $this->canEdit($user, $token);
            case self::SHOW:
                return $this->canShow($user, $token);
            case self::LOCK:
                return $this->canLock($user, $token);
            case self::UNLOCK:
                return $this->canUnlock($user, $token);
            case self::ENABLE_COCO:
                return $this->canEnableCoco($user, $token);
            case self::DISABLE_COCO:
                return $this->canDisableCoco($user, $token);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canEdit(User $user, TokenInterface $token) {
        if ($this->decisionManager->decide($token, array('ROLE_EDITOR'))) {
            return true;
        }

        if ($user == $token->getUser()) {
            return true;
        }

        return false;
    }

    private function canShow(User $user, TokenInterface $token) {
        if ((!$user->isEnabled() || !$user->isAccountNonLocked()) && !$this->decisionManager->decide($token, array('ROLE_COCO'))) {
            return false;
        }

        return true;
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

    private function canEnable(User $user, TokenInterface $token) {
        if ($user->isEnabled()) {
            return false;
        }

        return $this->decisionManager->decide($token, array('ROLE_EDITOR'));
    }

    private function canCreate(TokenInterface $token) {
        return $this->decisionManager->decide($token, array('ROLE_COCO'));
    }

    private function canLock(User $user, TokenInterface $token) {
        if (!$user->isAccountNonLocked()) {
            return false;
        }

        if ($token->getUser() == $user) {
            return false;
        }

        return $this->decisionManager->decide($token, array('ROLE_COCO'));
    }

    private function canUnlock(User $user, TokenInterface $token) {
        if ($user->isAccountNonLocked()) {
            return false;
        }

        if ($token->getUser() == $user) {
            return false;
        }

        return $this->decisionManager->decide($token, array('ROLE_COCO'));
    }

    private function canEnableCoco(User $user, TokenInterface $token) {
        if (!$user->isAccountNonLocked()) {
            return false;
        }

        if (!$user->isEnabled()) {
            return false;
        }

        return $this->decisionManager->decide($token, array('ROLE_COCO'));
    }

    private function canDisableCoco(User $user, TokenInterface $token) {
        return $this->decisionManager->decide($token, array('ROLE_COCO'));
    }
}
