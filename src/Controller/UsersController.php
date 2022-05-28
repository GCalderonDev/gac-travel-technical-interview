<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\UsersType;
use DateTimeImmutable;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[IsGranted('ROLE_ADMIN')]
class UsersController extends AbstractController
{
    #[Route('/admin/users', name: 'admin_users')]
    public function index(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $users = $entityManager->getRepository(Users::class)->findAll();

        return $this->render('users/index.html.twig', [
            'crud_name' => 'Users',
            'users' => $users,
            'create_route' => $this->generateUrl('admin_users_create')
        ]);
    }

    #[Route('/admin/users/create', name: 'admin_users_create')]
    public function create(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface): Response
    {
        $user = new Users();
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);

        // If we are creating user
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUsername($form->get('username')->getData());
            $user->setPassword($userPasswordHasherInterface->hashPassword($user, $form->get('password')->getData()));
            $user->setActive($form->get('active')->getData());
            $user->setRoles($form->get('roles')->getData());
            $user->setCreatedAt(new DateTimeImmutable());

            // Save entity
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'User created successfully');
            return $this->redirectToRoute('admin_users');
        }

        // Return form
        return $this->render('users/create-edit-form.html.twig', [
            'crud_name' => 'Create user',
            'userForm' => $form->createView()
        ]);
    }

    #[Route('/admin/users/{user}/edit', name: 'admin_users_edit')]
    public function edit(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface, Users $user): Response
    {
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);

        // If we are updating user
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUsername($form->get('username')->getData());
            if (!is_null($form->get('password')->getData())) {
                $user->setPassword($userPasswordHasherInterface->hashPassword($user, $form->get('password')->getData()));
            }
            $user->setActive($form->get('active')->getData());
            $user->setRoles($form->get('roles')->getData());

            // Save entity
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash('success', 'User updated successfully');
            return $this->redirectToRoute('admin_users');
        }

        // Return form
        return $this->render('users/create-edit-form.html.twig', [
            'crud_name' => 'Edit user: '.$user->getUserIdentifier(),
            'user' => $user,
            'userForm' => $form->createView()
        ]);
    }

    #[Route('/admin/users/delete/{user}', name: 'admin_users_delete')]
    public function delete(Users $user): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', 'User deleted successfully');
        return $this->redirectToRoute('admin_users');
    }
}
