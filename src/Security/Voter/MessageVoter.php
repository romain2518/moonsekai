<?php

namespace App\Security\Voter;

use App\Entity\Message;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class MessageVoter extends Voter
{
    public const SENDER = 'MESSAGE_SENDER';
    public const RECEIVER = 'MESSAGE_RECEIVER';

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::SENDER, self::RECEIVER])
            && $subject instanceof \App\Entity\Message;
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
            case self::SENDER:
                 /** @var Message $subject */
                 return $subject->getUserSender() === $user; // Returns true if the message sender is the logged in user
                break;
            case self::RECEIVER:
                /** @var Message $subject */
                return $subject->getUserReceiver() === $user; // Returns true if the message receiver is the logged in user
                break;
        }

        return false;
    }
}
