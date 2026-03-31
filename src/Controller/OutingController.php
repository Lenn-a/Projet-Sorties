<?php

namespace App\Controller;

use App\Entity\Outing;
use App\Form\Model\OutingCancel;
use App\Form\Model\OutingSearch;
use App\Form\OutingCancelType;
use App\Form\OutingSearchType;
use App\Form\OutingType;
use App\Repository\OutingRepository;
use App\Repository\OutingUserRepository;
use App\Repository\StatusRepository;
use App\Repository\UserRepository;
use App\Security\OutingVoter;
use App\Services\FileUploader;
use App\Services\StatusService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use function Symfony\Component\Clock\now;


#[Route('/outing', name: 'outing_')]
final class OutingController extends AbstractController
{
    #[Route('', name: 'list')]
    public function list(
        OutingRepository $outingRepository,
        Request          $request,
        StatusService    $statusService): Response
    {
        $statusService->setStatusByDate();

        $outingSearch = new OutingSearch();
        // Instance and handling of search/filter form on Outings list page
        $outingSearchForm = $this->createForm(OutingSearchType::class, $outingSearch);
        $outingSearchForm->handleRequest($request);


        if ($outingSearchForm->isSubmitted() && $outingSearchForm->isValid()) {
            $outings = $outingRepository->findAllPublishedOutings($outingSearch);
        } else {
            $outings = $outingRepository->findAllPublishedOutings(new OutingSearch());
        }

        return $this->render('outing/list.html.twig', [
            'outings' => $outings,
            'outingSearchForm' => $outingSearchForm,
        ]);
    }

    #[Route('/{id}', name: 'details', requirements: ['id' => '\d+'])]
    public function details(
        int                    $id,
        OutingRepository       $outingRepository,
        Request                $request,
        StatusService          $statusService,
        UserRepository         $userRepository,
        EntityManagerInterface $entityManager,
        OutingUserRepository   $outingUserRepository,
    ): Response
    {
        $outing = $outingRepository->find($id);
        //récupération des utilisateurs liés à une sortie (par son id)
        $userOutingIds = $outingUserRepository->findOutingUsersByOutingId($id);

        $userIds = array_map(function ($item) {
            return $item->getUserId();
        }, $userOutingIds);

        //On récupère la liste des utilisateurs correspondant à la liste des ids d'user
        $users = $userRepository->findUsersById($userIds);

        if (!$outing) {
            throw $this->createNotFoundException("Oups ! Sortie non trouvée !");
        }

        return $this->render('outing/details.html.twig', [
            'outing' => $outing,
            'users' => $users,
        ]);
    }

    #[Route('/cancel/{id}', name: 'cancel', requirements: ['id' => '\d+'])]
    public function delete(
        int                    $id,
        OutingRepository       $outingRepository,
        StatusService          $statusService,
        OutingCancel           $outingCancel,
        EntityManagerInterface $entityManager,
        Request $request,
    ): Response
    {
        $outing = $outingRepository->find($id);

        if(!$outing){
            throw $this->createNotFoundException("Oups ! Sortie non trouvée !");
        }

        if ($outing->getOrganiser() !== $this->getUser()) {
            $this->addFlash('error', 'Vous n\'avez pas le droit d\'annuler une sortie que vous n\'avez pas créée.');
            return $this->redirectToRoute('outing_list');
        }

        $outingCancel = new OutingCancel();
        $outingCancelForm = $this->createForm(OutingCancelType::class, $outingCancel);
        $outingCancelForm->handleRequest($request);

        if ($outingCancelForm->isSubmitted() && $outingCancelForm->isValid()) {
            $statusService->setStatusWithName($outing, 'Annulée');

            $outing->setOutingInfo($outing->getOutingInfo() . ' ' . $outing->getStatus() . ' Motif : ' . $outingCancel->getCancelMotive());

            $entityManager->persist($outing);
            $entityManager->flush();

            return $this->redirectToRoute('outing_details', ['id' => $outing->getId()]);
        }

        return $this->render('outing/cancel.html.twig', [
            'outing' => $outing,
            'outingCancelForm' => $outingCancelForm,
            'outingCancel' => $outingCancel,
        ]);
    }

//    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
//    public function create(
//        EntityManagerInterface $entityManager,
//    ) : Response {
//        $outing = $outingRepository->find($id);
//
////        if(!$outing){
////            throw $this->createNotFoundException("Oups ! Activité non trouvée !");
////        }
////
////        if ($outing->getOrganiser() !== $this->getUser()) {
////            $this->addFlash('error', 'You cannot cancel an outing you didn\'t create.');
////            return $this->redirectToRoute('outing_list');
////        }
////
////        $statusService->setStatusWithName($outing, 'Annulée');
////
////        $entityManager->persist($outing);
////        $entityManager->flush();
//
//        return $this->redirectToRoute('outing_details', ['id' => $id]);
//    }
    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(
        EntityManagerInterface $entityManager,
        StatusRepository $statusRepository,
        FileUploader $fileUploader,
        Request $request,
    ): Response {
        $outing = new Outing();

        $outingForm = $this->createForm(OutingType::class, $outing);
        $outingForm->handleRequest($request);

        $action = $request->request->get('action');

        if($outingForm->isSubmitted() && $outingForm->isValid()){

            $file = $outingForm -> get('photo')-> getData();
            if ($file != null) {
                $outing->setPhoto(
                    $fileUploader->upload($file, 'images/Outings/', $outing->getName())
                );
            }else {
                $outing->setPhoto('Outing-default.png');
            }
            //Le user qui créé la sortie = organisateur
            $outing->setOrganiser($this->getUser());
            //Organisateur est participant par défault
            $outing->addParticipant($this->getUser());
            //Status = ouvert quand on clic sur "Publier"

            if($action === 'enregistrer') {
                $enCreation = $statusRepository->getStatusByName('En création');
                $outing->setStatus($enCreation);

                $entityManager->persist($outing);
                $entityManager->flush();

                $this->addFlash('success', 'La sortie ' .$outing->getName(). ' a bien été enregistrée.');
                //Redirection vers la liste de sorties enregistrées
                return $this->redirectToRoute('outing_privateList', ['id' => $this->getUser()->getId()]);
            } else if ($action === 'publier') {
                $published = $statusRepository->getStatusByName('Ouverte');
                $outing->setStatus($published);

                $entityManager->persist($outing);
                $entityManager->flush();

                $this->addFlash('success', 'La sortie ' .$outing->getName(). ' a bien été publiée.');

                return $this->redirectToRoute('outing_details', ['id' => $outing->getId()]);
                }
        }

        return $this->render('outing/create.html.twig', [
            'outingForm' => $outingForm
        ]);
    }

    //Liste privée d'un utilisateur
    #[Route('/privateList/{id}', name: 'privateList')]
    public function privateList(
        int              $id,
        OutingRepository $outingRepository): Response
    {
        $outings = $outingRepository->findMyOutings($id);

        return $this->render('outing/privateList.html.twig', [
            'outings' => $outings
        ]);
    }

    #[Route('/participate/{id}', name: 'participate', requirements: ['id' => '\d+'])]
    #[IsGranted(OutingVoter::PARTICIPATE, 'outing')]
    public function participateInOuting(Outing $outing,
                                    EntityManagerInterface $entityManager,
                                    StatusService $statusService,
                                   ): RedirectResponse
{
//        $outing = $outingRepository->find($id);
        $currentUser = $this->getUser();

        $outing->addParticipant($currentUser);

        $statusService->statusOpenClose($outing);

        $entityManager->persist($outing);
        $entityManager->flush();

        return $this->redirectToRoute('outing_details', ['id' => $outing->getId()]);

}

#[Route('/quit/{id}', name: 'quit', requirements: ['id' => '\d+'])]
#[IsGranted(OutingVoter::QUIT, 'outing')]
public function quitAnOuting(Outing $outing,
                             EntityManagerInterface $entityManager,
                             StatusService $statusService) {
    $currentUser = $this->getUser();

    $outing->removeParticipant($currentUser);

    #[Route('/quit/{id}', name: 'quit', requirements: ['id' => '\d+'])]
    public function quitAnOuting(int                    $id,
                                 EntityManagerInterface $entityManager,
                                 StatusService          $statusService,
                                 OutingRepository       $outingRepository)
    {
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

