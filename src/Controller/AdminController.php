<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserRegistrationType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'admin_')]
final class AdminController extends AbstractController
{
    #[Route('', name: 'dashboard')]
    public function displayAdminDashboard(): Response
    {

        return $this->render('admin/admin.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/users', name: 'users')]
    public function manageUsers(
        UserRepository $userRepository,
        Request $request,
    ): Response
    {
        $user = $userRepository->findAll();

        return $this->render('admin/users.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/users/register', name: 'register')]
    public function registerUsers(
        EntityManagerInterface $entityManager,
        Request $request,
    ): Response
    {
        $user = new User();
        $userRegistrationForm = $this->createForm(UserRegistrationType::class, $user);
        $userRegistrationForm->handleRequest($request);

        if ($userRegistrationForm->isSubmitted() && $userRegistrationForm->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
        }

        return $this->render('admin/register.html.twig', [
            'user' => $user,
            'userRegistrationForm' => $userRegistrationForm,
        ]);
    }

}
