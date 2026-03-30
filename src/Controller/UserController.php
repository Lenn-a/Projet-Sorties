<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Services\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user', name: 'user_')]
final class UserController extends AbstractController
{
    #[Route('/profile/{id}', name: 'profile', methods: ['GET'])]
    public function getOrModifyUserProfile(
        User $id,
        UserRepository $userRepository,
    ): Response
    {
        $user = $userRepository->find($id);


        return $this->render('user/profile.html.twig', [
            'user' => $id,
        ]);
    }

    #[Route('/update/{id}', name: 'update', methods: ['POST', 'GET'])]
    public function updateUserProfile(
        User $id,
        EntityManagerInterface $entityManager,
        Request $request,
        FileUploader $fileUploader,
        UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = $this->getUser();

        $userForm = $this->createForm(UserType::class, $user);
        $userForm->handleRequest($request);

        if($userForm->isSubmitted() && $userForm->isValid()) {
            // get data of MDP field, if NOT NULL then reset user password with this new data
            // if MDP field null, then no change to current password
            $plainPassword = $userForm->get('plainPassword')->getData();
            if($plainPassword !== null) {
                $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            }
            $file = $userForm -> get('photo')-> getData();
            if ($file != null) {
                $user->setPhoto(
                    $fileUploader->upload($file, 'images/PFP/', $user->getUsername())
                );
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Modifications enregistrées.');
            return $this->redirectToRoute('user_profile', ['id' => $user->getId()]);
        }

        return $this->render('user/update.html.twig', [
            'user' => $id,
            'userForm' => $userForm,
        ]);
    }
}
