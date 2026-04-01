<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserRegistrationType;
use App\Repository\OutingRepository;
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
    ): Response
    {

        $users = $userRepository->findAll();

        return $this->render('admin/users.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/users/active/{id}', name: 'users_active')]
    public function changeUserActive(
        EntityManagerInterface $entityManager,
        User $id,
        UserRepository $userRepository,
    ): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $user = $userRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException('Oups ! Cet utilisateur n\'existe pas !');
        }
        if ($user->isActive()) {
            $user->setActive(false);
        } else {
            $user->setActive(true);
        }
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->redirectToRoute('admin_users');
    }

    #[Route('/users/delete/{id}', name: 'users_delete')]
    public function deleteUser(
        EntityManagerInterface $entityManager,
        User $id,
        UserRepository $userRepository,
    ): Response
    {
        $user = $userRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException('Oups ! Cet utilisateur n\'existe pas !');
        }

        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', message: 'L\'utilisateur vient d\'être supprimé.');
        return $this->redirectToRoute('admin_users');
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

    #[Route("/archives", name: 'archives')]
    public function archivedOutings(
        OutingRepository $outingRepository): Response
    {

        $outings = $outingRepository->findArchivedOutings();

        return $this->render('admin/outingsArchives.html.twig', ['outings' => $outings]);
}



}
