<?php

namespace App\Controller;

use App\Entity\Outing;
use App\Form\OutingType;
use App\Repository\OutingRepository;
use App\Repository\StatusRepository;
use App\Services\StatusService;
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
        $outings = $outingRepository->findAllPublishedOutings();
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

    #[Route('cancel/{id}', name: 'cancel', requirements: ['id' => '\d+'])]
    public function delete(
        int $id,
        OutingRepository $outingRepository,
        StatusService $statusService,
        EntityManagerInterface $entityManager
    ) : Response {
        $outing = $outingRepository->find($id);

        if ($outing->getOrganiser() !== $this->getUser()) {
            $this->addFlash('error', 'You cannot cancel an outing you didn\'t create.');
            return $this->redirectToRoute('outing_list');
        }

        $statusService->setStatusWithName($outing, 'Annulée');

        $entityManager->persist($outing);
        $entityManager->flush();

        return $this->redirectToRoute('outing_details', ['id' => $id]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(
        EntityManagerInterface $entityManager,
        StatusService $statusService,
        Request $request,
    ): Response {
        $outing = new Outing();

        $outingForm = $this->createForm(OutingType::class, $outing);
        $outingForm->handleRequest($request);

        if ($outingForm->isSubmitted() && $outingForm->isValid()) {
            //User making the Outing is its organiser :
            $outing->setOrganiser($this->getUser());

            //Organiser is a participant by default :
            $outing->addParticipant($this->getUser());

            $statusService->statusOpenClose($outing);

            $entityManager->persist($outing);
            $entityManager->flush();

            $this->addFlash('success', 'Outing' . $outing->getName() . ' has been added.');

            return $this->redirectToRoute('outing_details', ['id' => $outing->getId()]);
        }
        return $this->render('outing/create.html.twig', [
            'outingForm' => $outingForm
        ]);

}

#[Route('/participate/{id}', name: 'participate', requirements: ['id' => '\d+'])]
public function participateInOuting(int $id,
                                    EntityManagerInterface $entityManager,
                                    OutingRepository $outingRepository,
                                    StatusService $statusService,
                                   ): RedirectResponse
{
        $outing = $outingRepository->find($id);
        $currentUser = $this->getUser();

        if ($outing->getParticipants()->contains($currentUser)) {
            $this->addFlash('error', "User already signed up for this outing");
            return $this->redirectToRoute('outing_list');
        }
        if($outing->getStatus()->getLabel() == "Clôturée") {
            $this->addFlash('error', "Il n'y a plus de places pour cette sortie.");
        }

        $outing->addParticipant($currentUser);

        $statusService->statusOpenClose($outing)
    ;
        $entityManager->persist($outing);
        $entityManager->flush();
        return $this->redirectToRoute('outing_details', ['id' => $outing->getId()]);
}

#[Route('/quit/{id}', name: 'quit', requirements: ['id' => '\d+'])]
public function quitAnOuting(int $id,
                             EntityManagerInterface $entityManager,
                             StatusService $statusService,
                             OutingRepository $outingRepository) {
    $outing = $outingRepository->find($id);
    $currentUser = $this->getUser();

    if (!$outing->getParticipants()->contains($currentUser)) {
        $this->addFlash('error', "User isn't signed up for this outing");
        return $this->redirectToRoute('outing_list');
    }
    $outing->removeParticipant($currentUser);

    $statusService->statusOpenClose($outing);

    $entityManager->persist($outing);
    $entityManager->flush();

    return $this->redirectToRoute('outing_details', ['id' => $outing->getId()]);
}


}

