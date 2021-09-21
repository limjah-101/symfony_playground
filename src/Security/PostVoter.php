<?php

namespace App\Security;

use App\Entity\MicroPost;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PostVoter extends Voter
{

    const EDIT = "EDIT";
    const DELETE = "DELETE";

    protected function supports($attribute, $subject): bool
    {
        if(!in_array($attribute, [self::EDIT, self::DELETE]))
            return false;

        if(!$subject instanceof MicroPost)
            return false;

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $authenticated_user = $token->getUser();

        if(!$authenticated_user instanceof User)
            return false;
        /**
         * @var MicroPost $post
         */
        $post = $subject;

        return $post->getUser()->getId() === $authenticated_user->getId();
    }
}