<?php

namespace App\Controller;


use App\Entity\Suites;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ClientsRepository;
use App\Repository\SuitesRepository;
use App\Repository\ReservationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/reservation')]
class ReservationController extends AbstractController
{
    #[Route('/', name: 'app_reservation_index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository): Response
    {
        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservationRepository->findAll(),
        ]);
    }
    #[Route('/resaByEta/{id}', name: 'app_reservation_eta', methods: ['GET'])]
    public function resaByEta($id,ReservationRepository $reservationRepository): Response
    {

   $resa = ($reservationRepository->findByEtablissement($id));
        return $this->render('reservation/index.html.twig', [
            'reservations' => $resa,
        ]);
    }

    #[Route('/resaBySuite/{id}', name: 'app_reservation_suite', methods: ['GET'])]
    public function resaBySuite($id,ReservationRepository $reservationRepository): Response
    {

   $resa = ($reservationRepository->findBySuite($id));
        return $this->render('reservation/index.html.twig', [
            'reservations' => $resa,
        ]);
    }
    #[Route('/sucess', name: 'app_reservation_sucess', methods: ['GET'])]
    public function sucess(ReservationRepository $reservationRepository, ClientsRepository $clientsRepository): Response
    {
      $user = $this->getUser();
      $client = $clientsRepository->find($user);
    //    $reservation = $reservationRepository->findBy('clientId');
     
        return $this->render('reservation/sucess.html.twig', [
            // 'reservations' => $reservation,
            'reservations'=> $client
        ]);
    }

    #[Route('/new/{id}', name: 'app_reservation_new', methods: ['GET', 'POST'])]
    public function new($id,Request $request, ReservationRepository $reservationRepository, SuitesRepository $suitesRepository): Response
    {
        $suite = $suitesRepository->find($id);
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);
     
        if ($form->isSubmitted() && $form->isValid()) {
   
        
        
          
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

            $client = $this->getUser();

            $reservation->setClients($client);
            $reservationRepository->add($reservation);
            die();
            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' =>$form,
            'suite' => $suite,
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {

        if ($reservation->getClients() == $this->getUser()) {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }
else{
 
        return $this->render('clients/show.html.twig', [
            'id' => $this->getUser()->getId(),
        ]);
}
}

    #[Route('/{id}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservation $reservation, ReservationRepository $reservationRepository): Response
    {

        
        $this->denyAccessUnlessGranted('ROLE_GERANT');
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reservationRepository->add($reservation);
            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, ReservationRepository $reservationRepository): Response
    {

        
        $this->denyAccessUnlessGranted('ROLE_GERANT');
     
        if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->request->get('_token'))) {
            $reservationRepository->remove($reservation);
        }

        return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/delete/{id}', name: 'app_reservation_delete_by_client', methods: ['POST'])]
    public function delete_by_client(Request $request, Reservation $reservation, ReservationRepository $reservationRepository): Response
    {
        $client = ($request->request->get('client_id'));
        
        $interval = date_diff(new \DateTime(), $reservation->getDebut());
        if ($interval->d > 3){

        if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->request->get('_token'))) {
            $reservationRepository->remove($reservation);
        }

        return $this->redirectToRoute('app_clients_show', ['id' => $client], Response::HTTP_SEE_OTHER);
            
    }else{
        $this->addFlash(
            'warning',
            'Vous ne pouvez pas annuler une réservation à moins de 3jours !'
        );
    }

    return $this->redirectToRoute('app_clients_show', ['id' => $client], Response::HTTP_SEE_OTHER);
    }
}
