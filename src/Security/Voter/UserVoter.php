<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    public const EDIT_LOGINS = 'USER_EDIT_LOGINS';
    public const EDIT_PROFILE = 'USER_EDIT_PROFILE';
    public const EDIT_USER = 'USER_EDIT_USER';
    public const VIEW = 'VIEW';

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT_LOGINS, self::EDIT_PROFILE, self::EDIT_USER, self::VIEW])
            && $subject instanceof \App\Entity\User;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT_LOGINS:
                /** @var User $subject */
                return $user === $subject; // Return true if the requested user is the one logged in
                
                break;
            case self::VIEW:
                // logic to determine if the user can VIEW
                // return true or false
                break;
        }

        return false;
    }
}
