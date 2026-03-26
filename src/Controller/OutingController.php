<?php

namespace App\Controller;

use App\Entity\Outing;
use App\Form\Model\OutingSearch;
use App\Form\OutingSearchType;
use App\Form\OutingType;
use App\Repository\OutingRepository;
use App\Repository\StatusRepository;
use App\Repository\UserRepository;
use App\Services\FileUploader;
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
    public function list(OutingRepository $outingRepository, OutingSearch $outingSearch, OutingSearchType $outingSearchType, Request $request, StatusService $statusService): Response
    {
        $outingSearch = new OutingSearch();
        $outingSearchForm = $this->createForm(OutingSearchType::class, $outingSearch);
        $outingSearchForm->handleRequest($request);

        $outings = $outingRepository->findAllPublishedOutings($outingSearch);

        foreach ($outings as $outing) {
            $statusService->setStatusByDate($outing);
        }
//        $outings = $outingRepository->findOutingsPastMonth();

        return $this->render('outing/list.html.twig', [
            'outings' => $outings,
            'outingSearchForm' => $outingSearchForm,
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
        StatusRepository $statusRepository,
        UserRepository $userRepository,
        StatusService $statusService,
        Request $request,
    ): Response {
        $outing = new Outing();

        $outingForm = $this->createForm(OutingType::class, $outing);
        $outingForm->handleRequest($request);

        $action = $request->request->get('action');

        if ($action === 'publier'){
            if($outingForm->isSubmitted() && $outingForm->isValid()){
                //Le user qui créer la sortie = organisateur
                    $file = $outingForm -> get('photo')-> getData();
                    if ($file != null) {
                        $outing->setPhoto(
                            $fileUploader->upload($file, 'images/', $outing->getName())
                        );
                    }else {
                        $outing->setPhoto('images/Outings/Outing-default.png');
                    }
                $outing->setOrganiser($this->getUser());
                //Organisateur est participant par défault
                $outing->addParticipant($this->getUser());
                //Status = ouvert quand on clic sur "Publier"
                $published = $statusRepository->getStatusByName('Ouverte');
                $outing->setStatus($published);

                $entityManager->persist($outing);
                $entityManager->flush();

                $this->addFlash('success', 'La sortie ' .$outing->getName(). ' a bien été publiée.');
                //redirection vers la page de détail de la sortie
                return $this->redirectToRoute('outing_details', ['id' => $outing->getId()]);
            }
        }

        if($action === 'enregistrer'){
            if($outingForm->isSubmitted() && $outingForm->isValid()){
                if($outing->getPhoto() !== null){
                    $file = $outingForm -> get('photo')-> getData();
                    $outing -> setPhoto(
                        $fileUploader->upload($file, 'images/Outings', $outing->getName())
                    );
                }else {
                    $outing -> setPhoto('Outing-default.png');
                }
                $outing->setOrganiser($this->getUser());
                $outing->addParticipant($this->getUser());
                //Status = en création si on clic sur "enregistrer"
                $published = $statusRepository->getStatusByName('En création');
                $outing->setStatus($published);
            $statusService->statusOpenClose($outing);

                $entityManager->persist($outing);
                $entityManager->flush();

                $this->addFlash('success', 'La sortie ' .$outing->getName(). ' a bien été enregistrée.');

                //Redirection vers la liste de sorties enregistrées
                return $this->redirectToRoute('outing_privateList', ['id' => $this->getUser()->getId()]);
            }
        }
        return $this->render('outing/create.html.twig', [
            'outingForm'=> $outingForm
        ]);
}

    //Liste privé d'un utilisateur
    #[Route('/privateList/{id}', name: 'privateList')]
    public function privateList(OutingRepository $outingRepository): Response{
        $outings = $outingRepository->findMyOutings();

        return $this->render('outing/privateList.html.twig', [
            'outings' => $outings
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

