<?php

namespace App\Security\Voter;

use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
    public const EDIT_USER_RANK = 'USER_EDIT_RANK';

    public function __construct(
        private AccessDecisionManagerInterface $accessDecisionManager,
        private Security $security
        ) {
    }

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return (in_array($attribute, [self::EDIT_PROFILE, self::EDIT_USER])
            && $subject instanceof \App\Entity\User)
            || ($attribute === self::EDIT_USER_RANK && is_string($subject));
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

                $maxRole = 'ROLE_MODERATOR';
                if ($this->security->isGranted('ROLE_ADMIN')) $maxRole = 'ROLE_ADMIN';
                if ($this->security->isGranted('ROLE_SUPERADMIN')) $maxRole = 'ROLE_SUPERADMIN';

                $token = new UsernamePasswordToken($subject, 'none', $subject->getRoles());
                
                // Return true if the requested user has a lower role than the logged in user
                return !$this->accessDecisionManager->decide($token, [$maxRole], $subject);
                
                break; 
            case self::EDIT_USER_RANK:
                /** @var string $subject role that must be lower than the one logged in user has */
                
                if ($subject === 'ROLE_USER') {
                    return $this->security->isGranted('ROLE_MODERATOR');
                } elseif ($subject === 'ROLE_MODERATOR') {
                    return $this->security->isGranted('ROLE_ADMIN');
                } else { // $subject === 'ROLE_ADMIN'
                    return $this->security->isGranted('ROLE_SUPERADMIN');
                }
                
                break;
        }

        return false;
    }
}
