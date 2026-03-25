<?php

namespace App\Controller;

use App\Entity\Outing;
use App\Form\OutingType;
use App\Repository\OutingRepository;
use App\Repository\StatusRepository;
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

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(
        EntityManagerInterface $entityManager,
        StatusRepository $statusRepository,
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

            //Assign status 'Ouverte' when outing is published
            $published = $statusRepository->getStatusByName('Ouverte');
            $outing->setStatus($published);

            $entityManager->persist($outing);
            $entityManager->flush();

            $this->addFlash('success', 'Outing' . $outing->getName() . ' has been added.');

            return $this->redirectToRoute('outing_details', ['id' => $outing->getId()]);
        }
        return $this->render('outing/create.html.twig', [
            'outingForm' => $outingForm
        ]);
}

    //retourne le formulaire pour update une sortie
//    #[Route('/update/{id}', name: 'update', requirements: ['id' => '\d+'])]
//    public function update(
//        int $id,
//        OutingRepository $outingRepository,
//        Request $request,
//        EntityManagerInterface $entityManager,
//    ){
//        $outing = $outingRepository->find($id);
//        $outingForm = $this->createForm(OutingType::class, $outing);
//        $outingForm->handleRequest($request);
//        if ($outingForm->isSubmitted() && $outingForm->isValid()) {
//            $entityManager->persist($outing);
//            $entityManager->flush();
//            $this->addFlash('success', 'Votre sortie' . $outing->getName() . ' a été mise à jour.');
//            return $this->redirectToRoute('outing_details', ['id' => $outing->getId()]);
//        }
//        return $this->render('outing/update.html.twig', [
//            'outing' => $outing,
//        ]);
//    }





//il faut aussi une route pour acceder à une "privateList"
// qui affiche les sorties dont l'utilisateur ets l'organisateur.

//    #[Route('/save', name: 'save', methods: ['GET', 'POST'])]
//    public function save(
//        EntityManagerInterface $entityManager,
//        Request $request,
//        OutingRepository $outingRepository,
//        int $id,
//    ): Response {
//        $outing = new Outing();
//        $outingForm = $this->createForm(OutingType::class, $outing);
//        $outingForm->handleRequest($request);
//
//        if ($outingForm->isSubmitted() && $outingForm->isValid()) {
//
//
//            $entityManager->persist($outing);
//            $entityManager->flush();
//
//            $this->addFlash('success', 'La sortie ' . $outing->getName() . ' a été sauvgardée.');
//
//            return $this->redirectToRoute('outing_save', ['id' => $outing->getId()]);
//        }
//        return $this->render('outing/privateList.html.twig', [
//            'outingForm' => $outingForm
//        ]);
//    }

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

