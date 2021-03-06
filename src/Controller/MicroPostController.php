<?php

namespace App\Controller;

use App\Entity\MicroPost;
use App\Entity\User;
use App\Form\MicroPostType;
use App\Repository\MicroPostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * @Route ("posts")
 */
class MicroPostController extends AbstractController
{
    /**
     * @var MicroPostRepository
     */
    private $microPostRepository;
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var UserRepository
     */
    private $userRepository;


    public function __construct(
        UserRepository $userRepository,
        MicroPostRepository $microPostRepository,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        RouterInterface $router,
        FlashBagInterface $flashBag
    )
    {
        $this->microPostRepository = $microPostRepository;
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->flashBag = $flashBag;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/", name="post_index")
     */
    public function index(): Response
    {
        $authenticated_user= $this->getUser();
        $users_to_follow = [];

        if($authenticated_user instanceof User) {
            $posts = $this->microPostRepository->getFollowingPosts($authenticated_user->getFollowing());
            $users_to_follow = count($posts) === 0 ? $this->userRepository->getUsersWith5PostsMoreExceptAuthenticatedUser($authenticated_user) : [];
        }
        else
            $posts = $this->microPostRepository->findBy([], ['time' => 'DESC']);

        return $this->render('micro_post/index.html.twig', [
            'posts' => $posts,
            'users_to_follow' => $users_to_follow
        ]);
    }

    /**
     * @Route("/add", name="post_add")
     * @Security ("is_granted('ROLE_USER')")
     */
    public function add(Request $request)
    {
        $post = new MicroPost();
        $post->setTime(new \DateTime());
        $post->setUser($this->getUser());

        $form = $this->formFactory->create(MicroPostType::class, $post);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($post);
            $this->entityManager->flush();

            return new RedirectResponse($this->router->generate("post_index"));
        }

        return $this->render('micro_post/add.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route ("/{id}", name="post_show")
     */
    public function show(MicroPost $post): Response
    {
        return $this->render('micro_post/show.html.twig', ['post' => $post]);
    }

    /**
     * @Route ("/edit/{id}", name="post_edit")
     * @Security("is_granted('EDIT', post)", message="You are not allowed to perform this action")
     */
    public function edit(MicroPost $post, Request $request)
    {
        $form = $this->formFactory->create(MicroPostType::class, $post);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($post);
            $this->entityManager->flush();

            return new RedirectResponse($this->router->generate("post_index"));
        }

        return $this->render('micro_post/add.html.twig', [
            'form' => $form->createView(),
            'post' => $post
        ]);
    }

    /**
     * @Route ("/delete/{id}", name="post_delete")
     * @Security ("is_granted('DELETE', post)", message="You are not allowed to perform this action")
     */
    public function delete(MicroPost $post): RedirectResponse
    {
        //$this->denyAccessUnlessGranted('DELETE', $post); @DESC Similar to Security Annotation
        $this->entityManager->remove($post);
        $this->entityManager->flush();

        $this->flashBag->add('notice', 'Post was deleted successfully');

        return new RedirectResponse($this->router->generate("post_index"));

    }

    /**
     * @Route ("/user/{username}", name="users_posts")
     * @Security ("is_granted('ROLE_USER')")
     */
    public function getUserPosts(User $user): Response
    {
        return $this->render('micro_post/user_posts.html.twig', [
            'posts' => $this->microPostRepository->findBy(['user' => $user], ['time' => 'DESC']),
            'user' => $user
        ]);
    }
}
