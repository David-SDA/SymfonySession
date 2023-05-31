<?php

namespace App\Controller;

use App\Entity\Formateur;
use App\Form\FormateurType;
use App\Repository\FormateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FormateurController extends AbstractController
{
    #[Route('/formateur', name: 'app_formateur')]
    public function index(FormateurRepository $formateurRepository): Response{
        /* Trouver touts les formateurs */
        $formateurs = $formateurRepository->findBy([], ["nom" => "ASC"]);
        return $this->render('formateur/index.html.twig', [
            'formateurs' => $formateurs,
        ]);
    }


    #[Route('formateur/add', name: 'add_formateur')]
    #[Route('/formateur/{id}/edit', name: 'edit_formateur')]
    public function add(EntityManagerInterface $entityManager, Formateur $formateur = null, Request $request): Response{
        
        /* Si le formateur n'existe pas, on le crée */
        if(!$formateur){
            $formateur = new Formateur();
        }
        
        $form = $this->createForm(FormateurType::class, $formateur); // On crée le formulaire
        $form->handleRequest($request); // Inspecte la requête
        if($form->isSubmitted() && $form->isValid()){
            $formateur = $form->getData(); // On récupère les données
            $entityManager->persist($formateur); // Équivalent du prepare
            $entityManager->flush(); // Équivalent du execute

            return $this->redirectToRoute("app_formateur");
        }

        return $this->render('formateur/add.html.twig', [
            'formAddFormateur' => $form->createView(),
            'edit' => $formateur->getId()
        ]);
    }


    #[Route('/formateur/{id}/delete', name : 'delete_formateur')]
    public function delete(EntityManagerInterface $entityManager, Formateur $formateur = null): Response{
        $entityManager->remove($formateur);
        $entityManager->flush();

        return $this->redirectToRoute("app_formateur");
    }


    #[Route('formateur/{id}', name: 'show_formateur')]
    public function show(Formateur $formateur): Response{
        
        return $this->render('formateur/show.html.twig', [
            'formateur' => $formateur,
        ]);
    }
}
