<?php

namespace App\Controller;

use App\Entity\Gerants;
use App\Form\GerantsType;
use App\Repository\GerantsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/gerants')]
class GerantsController extends AbstractController
{
    #[Route('/', name: 'app_gerants_index', methods: ['GET'])]
    public function index(GerantsRepository $gerantsRepository): Response
    {
        return $this->render('gerants/index.html.twig', [
            'gerants' => $gerantsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_gerants_new', methods: ['GET', 'POST'])]
    public function new(Request $request, GerantsRepository $gerantsRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        $gerant = new Gerants();
        $form = $this->createForm(GerantsType::class, $gerant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Me permet de hasher le password avant le persister le ge$gerant grâce au UserPasswordHasherInterface
            $hashedPassword = $passwordHasher->hashPassword(
                $gerant,
                $gerant->getPassword()
            );
            $gerant->setPassword($hashedPassword);
            $gerantsRepository->add($gerant);
            return $this->redirectToRoute('app_gerants_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gerants/new.html.twig', [
            'gerant' => $gerant,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gerants_show', methods: ['GET'])]
    public function show(Gerants $gerant): Response
    {
        return $this->render('gerants/show.html.twig', [
            'gerant' => $gerant,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_gerants_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Gerants $gerant, GerantsRepository $gerantsRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(GerantsType::class, $gerant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword(
                $gerant,
                $gerant->getPassword()
            );
            $gerant->setPassword($hashedPassword);
            $gerantsRepository->add($gerant);
            return $this->redirectToRoute('app_gerants_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gerants/edit.html.twig', [
            'gerant' => $gerant,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gerants_delete', methods: ['POST'])]
    public function delete(Request $request, Gerants $gerant, GerantsRepository $gerantsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$gerant->getId(), $request->request->get('_token'))) {
            $gerantsRepository->remove($gerant);
        }

        return $this->redirectToRoute('app_gerants_index', [], Response::HTTP_SEE_OTHER);
    }
}
