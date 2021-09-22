<?php

namespace App\Controller;

use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security( "is_granted('ROLE_USER')" )
 * @Route( "/following" )
 */
class FollowingController extends AbstractController
{
    /**
     * @Route ( "/follow/{id}", name="follow" )
     */
    public function follow(User $userToFollow): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        /**@var User $current_user*/
        $current_user = $this->getUser();

        // unable following yourself
        if($userToFollow->getId() != $current_user->getId()) {

            $current_user->getFollowing()->add($userToFollow);
            $this->getDoctrine()->getManager()->flush();// No need to call persist | Doctrine auto-prepare the insert query to execute
        }

        return $this->redirectToRoute(
            'users_posts',
            ['username' => $userToFollow->getUsername()]
        );
    }

    /**
     * @Route ( "/unfollow/{id}", name="unfollow" )
     */
    public function unfollow(User $userToUnfollow): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        /**@var User $current_user*/
        $current_user = $this->getUser();
        $current_user->getFollowing()->removeElement($userToUnfollow);

        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute(
            'users_posts',
            ['username' => $userToUnfollow->getUsername()]
        );
    }
}
