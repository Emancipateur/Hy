<?php

namespace App\Controller;

use App\Entity\Clients;
use App\Repository\ClientsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/client/{id}', name: 'app_clients_admin_show', methods: ['GET'])]
    public function show( $id, ClientsRepository $clientsRepository): Response
    {
        $client= $clientsRepository->find($id);
        return $this->render('clients/show.html.twig', [
            'client' => $client,
        ] );
    }
}
