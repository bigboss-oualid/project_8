<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\User\AccountType;
use App\Form\User\EditPasswordType;
use App\Form\User\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/users", name="user_list")
     */
    public function listAction(): Response
    {
        return $this->render('user/list.html.twig', ['users' => $this->getDoctrine()->getRepository(User::class)
            ->findAll(), ]);
    }

    /**
     * @Route("/users/create", name="user_create")
     *
     * @return RedirectResponse|Response
     */
    public function createAction(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $encoder
    ): Response {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($encoder->encodePassword($user, $user->getPassword()));

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', "L'utilisateur a bien été ajouté.");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/users/{id}/edit", name="user_edit")
     *
     * @return RedirectResponse|Response
     */
    public function editAction(
        User $user,
        Request $request,
        UserPasswordEncoderInterface $encoder,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(AccountType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($encoder->encodePassword($user, $user->getPassword()));

            $entityManager->flush();

            $this->addFlash('success', "L'utilisateur a bien été modifié");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }

    /**
     * @Route("/users/{id}/edit-password", name="user_password_edit")
     *
     * @return RedirectResponse|Response
     */
    public function editPasswordAction(
        User $user,
        Request $request,
        UserPasswordEncoderInterface $encoder,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(EditPasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($encoder->encodePassword($user, $user->getPassword()));

            $entityManager->flush();

            $this->addFlash('success', "Le mot de passe a bien été modifié");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/password.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }
}
