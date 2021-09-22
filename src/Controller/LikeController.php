<?php

namespace App\Controller;

use App\Entity\MicroPost;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/likes")
 */
class LikeController extends AbstractController
{
    /**
     * @Route("/like/{id}", name="like_post")
     */
    public function like(MicroPost $post): JsonResponse
    {
        /**@var User $authenticated_user*/
        $authenticated_user = $this->getUser();

        if(!$authenticated_user instanceof User)
            return new JsonResponse([], Response::HTTP_UNAUTHORIZED);

        $post->like($authenticated_user);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse([
            'count' => $post->getLikedBy()->count()
        ], 200);
    }

    /**
     * @Route("/unlike/{id}", name="unlike_post")
     */
    public function unlike(MicroPost $post): JsonResponse
    {
        /**@var User $authenticated_user*/
        $authenticated_user = $this->getUser();

        if(!$authenticated_user instanceof User)
            return new JsonResponse([], Response::HTTP_UNAUTHORIZED);

        $post->getLikedBy()->removeElement($authenticated_user);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse([
            'count' => $post->getLikedBy()->count()
        ], 200);
    }
}
