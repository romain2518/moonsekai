<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    public const EDIT_PROFILE = 'USER_EDIT_PROFILE';
    public const EDIT_USER = 'USER_EDIT';
    public const EDIT_RANK = 'USER_EDIT_RANK';

    public function __construct(private AccessDecisionManagerInterface $accessDecisionManager) {
    }

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT_PROFILE, self::EDIT_USER, self::EDIT_RANK])
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
            case self::EDIT_PROFILE:
                /** @var User $subject */
                // return $user === $subject; // Return true if the requested user is the one logged in
                
                break;
            case self::EDIT_USER:
                /** @var User $subject */
                //? Using access decision manager because isGranted() can not be used on another user

                $token = new UsernamePasswordToken($subject, 'none', $subject->getRoles());
                return !$this->accessDecisionManager->decide($token, ['ROLE_MODERATOR'], $subject); // Return true if the requested user is not granted the ROLE_MODERATOR
                
                break;
            case self::EDIT_RANK:
                # code...
                break;
        }

        return false;
    }
}
