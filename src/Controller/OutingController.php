<?php

namespace App\Controller;

use App\Entity\Outing;
use App\Form\OutingType;
use App\Repository\OutingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/outing', name: 'outing_')]
final class OutingController extends AbstractController
{
    #[Route('', name: 'list')]
    public function list(OutingRepository $outingRepository): Response
    {
        $outings = $outingRepository->findAll();
//        $outings = $outingRepository->findOutingsPastMonth();

        return $this->render('outing/list.html.twig', [
            'outings' => $outings
        ]);
    }

    #[Route('/{id}', name: 'details', requirements: ['id' => '\d+'])]
    public function details(
    int $id,
    OutingRepository $outingRepository
    ): Response {
        $outing = $outingRepository->find($id);
        return $this->render('outing/details.html.twig', [
            'outing' => $outing
        ]);
    }

    #[Route('delete/{id}', name: 'delete', requirements: ['id' => '\d+'])]
    public function delete(
        int $id,
        OutingRepository $outingRepository,
        EntityManagerInterface $entityManager
    ) : Response {
        $outing = $outingRepository->find($id);

        $entityManager->remove($outing);
        $entityManager->flush();

        return $this->redirectToRoute('outing_list');
    }

    #[Route('/create', name: 'create')]
    #[Route('/update/{id}', name: 'update', requirements: ['id' => '\d+'])]
    public function create(
        EntityManagerInterface $entityManager,
        OutingRepository $outingRepository,
        Request $request,
        int $id = null
    ): Response {
        $outing = new Outing();


        $outingForm = $this->createForm(OutingType::class, $outing);
        $outingForm->handleRequest($request);

        if ($outingForm->isSubmitted() && $outingForm->isValid()) {
            $entityManager->persist($outing);
            $entityManager->flush();

            return $this->redirectToRoute('outing_list', ['id' => $outing->getId()]);
        }
        return $this->render('outing/create.html.twig', [
            'outingForm' => $outingForm
        ]);

}

#[Route('/participate/{id}', name: 'participate', requirements: ['id' => '\d+'])]
public function participateInOuting(int $id,
                                    EntityManagerInterface $entityManager,
                                    OutingRepository $outingRepository
                                   ): RedirectResponse
{
        $outing = $outingRepository->find($id);
        $currentUser = $this->getUser();

        if ($outing->getParticipants()->contains($currentUser)) {
            $this->addFlash('error', "User already signed up for this outing");
            return $this->redirectToRoute('outing_list');
        }

        $outing->addParticipant($currentUser);
        $entityManager->persist($outing);
        $entityManager->flush();
        return $this->redirectToRoute('outing_details', ['id' => $outing->getId()]);
}

#[Route('/quit/{id}', name: 'quit', requirements: ['id' => '\d+'])]
public function quitAnOuting(int $id,
                             EntityManagerInterface $entityManager,
                             OutingRepository $outingRepository) {
    $outing = $outingRepository->find($id);
    $currentUser = $this->getUser();

    if (!$outing->getParticipants()->contains($currentUser)) {
        $this->addFlash('error', "User isn't signed up for this outing");
        return $this->redirectToRoute('outing_list');
    }
    $outing->removeParticipant($currentUser);

    $entityManager->persist($outing);
    $entityManager->flush();

    return $this->redirectToRoute('outing_details', ['id' => $outing->getId()]);
}


}

