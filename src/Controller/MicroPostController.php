<?php

namespace App\Controller;

use App\Entity\MicroPost;
use App\Form\MicroPostType;
use App\Repository\MicroPostRepository;
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


    public function __construct(
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
    }

    /**
     * @Route("/", name="post_index")
     */
    public function index(): Response
    {
        return $this->render('micro_post/index.html.twig', [
            'posts' => $this->microPostRepository->findBy([], ['time' => 'DESC'])
        ]);
    }

    /**
     * @Route("/add", name="post_add")
     */
    public function add(Request $request)
    {
        $post = new MicroPost();
        $post->setTime(new \DateTime());

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
    public function show(MicroPost $post)
    {
        return $this->render('micro_post/show.html.twig', ['post' => $post]);
    }

    /**
     * @Route ("/edit/{id}", name="post_edit")
     */
    public function edit(MicroPost $post, Request $request)
    {

        $this->denyAccessUnlessGranted('EDIT', $post);

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
     * @Security ("is_granted('DELETE', post)", message="You are not allowed to do this action")
     */
    public function delete(MicroPost $post)
    {
        //$this->denyAccessUnlessGranted('DELETE', $post); @DESC Similar to Security Annotation

        $this->entityManager->remove($post);
        $this->entityManager->flush();

        $this->flashBag->add('notice', 'Post was deleted successfully');

        return new RedirectResponse($this->router->generate("post_index"));

    }
}
