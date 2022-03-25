<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Etablissements;
use App\Form\EtablissementsType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\EtablissementsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/etablissements')]
class EtablissementsController extends AbstractController
{
    #[Route('/', name: 'app_etablissements_index', methods: ['GET'])]
    public function index(EtablissementsRepository $etablissementsRepository): Response
    {
        return $this->render('etablissements/index.html.twig', [
            'etablissements' => $etablissementsRepository->findAll(),
        ]);
    }

    #[Route('/gerant', name: 'app_etablissements_a', methods: ['GET'])]
    public function gerant(EtablissementsRepository $etablissementsRepository): Response
    {

       $etablissement = $etablissementsRepository->findByGerant($this->getUser()->getId());
        return $this->render('etablissements/etablissement_gerant.html.twig', [
            'etablissement' => $etablissement,
        ]);
    }

    #[Route('/new', name: 'app_etablissements_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EtablissementsRepository $etablissementsRepository, EntityManagerInterface $entityManager): Response
    {
        $etablissement = new Etablissements();
        $form = $this->createForm(EtablissementsType::class, $etablissement);
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
                $etablissement->addImage($img);
            }
            
            $entityManager->persist($etablissement);
            $entityManager->flush();
            // $etablissementsRepository->add($etablissement);
            return $this->redirectToRoute('app_etablissements_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('etablissements/new.html.twig', [
            'etablissement' => $etablissement,
            'form' => $form,
        ]);
    }
    #[Route('/supprime/imageEta/{id}', name: 'etablissement_delete_image', methods: ['POST','GET'])]
public function deleteImage(Images $image, Request $request, EntityManagerInterface $entityManager){


  
        $nom = $image->getTitre();
        // On supprime le fichier
   
        unlink($this->getParameter('kernel.project_dir').'/public/uploads'.'/'.$nom);

        // On supprime l'entrée de la base
        $entityManager->remove($image);
        $entityManager->flush();

        return $this->redirect($request->headers->get('referer'));
      
        
    
}

    #[Route('/{id}', name: 'app_etablissements_show', methods: ['GET'])]
    public function show(Etablissements $etablissement): Response
    {
        return $this->render('etablissements/show.html.twig', [
            'etablissement' => $etablissement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_etablissements_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Etablissements $etablissement, EtablissementsRepository $etablissementsRepository, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EtablissementsType::class, $etablissement);
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
                $etablissement->addImage($img);
            }
            
            $entityManager->persist($etablissement);
            $entityManager->flush();
            $etablissementsRepository->add($etablissement);
            $etablissementsRepository->add($etablissement);
            return $this->redirectToRoute('app_etablissements_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('etablissements/edit.html.twig', [
            'etablissement' => $etablissement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_etablissements_delete', methods: ['POST'])]
    public function delete(Request $request, Etablissements $etablissement, EtablissementsRepository $etablissementsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$etablissement->getId(), $request->request->get('_token'))) {
            $etablissementsRepository->remove($etablissement);
        }

        return $this->redirectToRoute('app_etablissements_index', [], Response::HTTP_SEE_OTHER);
    }
}
