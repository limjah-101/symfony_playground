<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * @Route("/blog")
 */
class BlogController extends AbstractController
{

    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(SessionInterface $session, RouterInterface $router)
    {

        $this->session = $session;
        $this->router = $router;
    }
    /**
     * @Route("/", name="blog_index")
     */
    public function index()
    {
        return $this->render('blog/login.html.twig', [
            'posts' => $this->session->get('posts'),
        ]);
    }

    /**
     * @Route("/add", name="blog_add")
     */
    public function add()
    {
        $posts = $this->session->get('posts');
        $posts[uniqid()] = [
            'title' => 'A random title ' . rand(1, 500),
            'text' => 'Some random content ' . rand(1, 500),
        ];
        $this->session->set('posts', $posts);

        return $this->redirect($this->router->generate('blog_index'));
    }

    /**
     * @Route("/show/{id}", name="blog_show")
     */
    public function show($id)
    {
        $posts = $this->session->get('posts');
        if(!$posts || !isset($posts[$id]))
            throw new NotFoundHttpException('Posts not found');

        return $this->render('blog/show.html.twig', [
            'id' => $id,
            'post' => $posts[$id]
        ]);
    }
}
