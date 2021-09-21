<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{

    /**
     * @Route ("/register", name="user_register")
     */
    public function Register(UserPasswordEncoderInterface $passwordEncoder, Request $request, FormFactoryInterface $factory)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            $em = $this->getDoctrine()->getManager(); // another way to get the manager if not injected

            $em->persist($user);
            $em->flush();

            $this->redirect("post_index");
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}