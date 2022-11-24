<?php

namespace App\Security\Voter;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class CommentVoter extends Voter
{
    public const NOT_MUTED = 'COMMENT_NOT_MUTED';
    public const EDIT = 'COMMENT_EDIT';
    public const DELETE = 'COMMENT_DELETE';

    public function __construct(
        private Security $security
    ) {
    }

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return self::NOT_MUTED === $attribute && null === $subject
        || in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof \App\Entity\Comment;
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
            case self::NOT_MUTED:
                /** @var User $user */
                return !$user->isMuted(); // Returns true if the logged in user is not muted
                break;
            case self::EDIT:
                /** @var Comment $subject */
                return $subject->getUser() === $user; // Returns true if the comment author is the logged in user
                break;
            case self::DELETE:
                // Returns true if the comment author is the logged in user OR if logged in user is granted the role moderator
                return $subject->getUser() === $user || $this->security->isGranted('ROLE_MODERATOR');
                break;
        }

        return false;
    }
}
