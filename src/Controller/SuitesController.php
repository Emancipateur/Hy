<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Suites;
use App\Form\SuitesType;
use Doctrine\ORM\EntityManager;
use App\Repository\SuitesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/suites')]
class SuitesController extends AbstractController
{
    #[Route('/', name: 'app_suites_index', methods: ['GET'])]
    public function index(SuitesRepository $suitesRepository): Response
    {
        return $this->render('suites/index.html.twig', [
            'suites' => $suitesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_suites_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SuitesRepository $suitesRepository, EntityManagerInterface $entityManager): Response
    {
        $suite = new Suites();
        $form = $this->createForm(SuitesType::class, $suite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();
            foreach($images as $image){
                $fichier = md5(uniqid()).'.'.$image->guessExtension();
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );
                
                // On crée l'image dans la base de données
                $img = new Images();
                $img->setTitre($fichier);
                $suite->addImage($img);
            }
            
            $entityManager->persist($suite);
            $entityManager->flush();
            // $suitesRepository->add($suite);
            return $this->redirectToRoute('app_suites_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('suites/new.html.twig', [
            'suite' => $suite,
            'form' => $form,
        ]);
    }

#[Route('/supprime/image/{id}', name: 'annonces_delete_image', methods: ['POST','GET'])]
public function deleteImage(Images $image, Request $request, EntityManagerInterface $entityManager){


  
        $nom = $image->getTitre();
        // On supprime le fichier
        unlink($this->getParameter('./uploads').'/'.$nom);

        // On supprime l'entrée de la base
        $entityManager->remove($image);
        $entityManager->flush();

        return $this->redirect($request->headers->get('referer'));
      
        
    
}

    #[Route('/{id}', name: 'app_suites_show', methods: ['GET'])]
    public function show(Suites $suite): Response
    {
        return $this->render('suites/show.html.twig', [
            'suite' => $suite,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_suites_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Suites $suite, SuitesRepository $suitesRepository, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SuitesType::class, $suite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();
            foreach($images as $image){
                $fichier = md5(uniqid()).'.'.$image->guessExtension();
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );
                
                // On crée l'image dans la base de données
                $img = new Images();
                $img->setTitre($fichier);
                $suite->addImage($img);
            }
            
            $entityManager->persist($suite);
            $entityManager->flush();
            $suitesRepository->add($suite);
            return $this->redirectToRoute('app_suites_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('suites/edit.html.twig', [
            'suite' => $suite,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_suites_delete', methods: ['POST'])]
    public function delete(Request $request, Suites $suite, SuitesRepository $suitesRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$suite->getId(), $request->request->get('_token'))) {
            $suitesRepository->remove($suite);
        }

        return $this->redirectToRoute('app_suites_index', [], Response::HTTP_SEE_OTHER);
    }
}
