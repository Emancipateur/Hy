<?php

namespace App\Controller;

use App\Entity\Clients;
use App\Form\ClientsType;
use App\Repository\ClientsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/clients')]
class ClientsController extends AbstractController
{
    #[Route('/', name: 'app_clients_index', methods: ['GET'])]
    public function index(ClientsRepository $clientsRepository): Response
    {
        return $this->render('clients/index.html.twig', [
            'clients' => $clientsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_clients_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ClientsRepository $clientsRepository,UserPasswordHasherInterface $passwordHasher): Response
    {
        $client = new Clients();
        $form = $this->createForm(ClientsType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                    // Me permet de hasher le password avant le persister  grâce au UserPasswordHasherInterface
                    $hashedPassword = $passwordHasher->hashPassword(
                        $client,
                        $client->getPassword()
                    );
                    $client->setPassword($hashedPassword);
        
            $clientsRepository->add($client);
            return $this->redirectToRoute('app_etablissements_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('clients/new.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_clients_show', methods: ['GET'])]
    public function show(Clients $client): Response
    {
       
        return $this->render('clients/show.html.twig', [
            'client' => $client,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_clients_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Clients $client, ClientsRepository $clientsRepository): Response
    {

        $form = $this->createForm(ClientsType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $clientsRepository->add($client);
            return $this->redirectToRoute('app_clients_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('clients/edit.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_clients_delete', methods: ['POST'])]
    public function delete(Request $request, Clients $client, ClientsRepository $clientsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$client->getId(), $request->request->get('_token'))) {
            $clientsRepository->remove($client);
        }

        return $this->redirectToRoute('app_clients_index', [], Response::HTTP_SEE_OTHER);
    }
}
