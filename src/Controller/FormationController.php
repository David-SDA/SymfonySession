<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\FormationRepository;
use App\Repository\SessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormationController extends AbstractController
{
    #[Route('/formation', name: 'app_formation')]
    public function index(FormationRepository $formationRepository): Response{
        
        /* Trouver toutes les formations */
        $formations = $formationRepository->findBy([], ["libelle" => "ASC"]);
        return $this->render('formation/index.html.twig', [
            'formations' => $formations,
        ]);
    }


    #[Route('formation/add', name: 'add_formation')]
    #[Route('/formation/{id}/edit', name: 'edit_formation')]
    public function add(EntityManagerInterface $entityManager, Formation $formation = null, Request $request): Response{
        
        /* Si la formation n'existe pas, on le crée */
        if(!$formation){
            $formation = new Formation();
        }
        
        $form = $this->createForm(FormationType::class, $formation); // On crée le formulaire
        $form->handleRequest($request); // Inspecte la requête
        if($form->isSubmitted() && $form->isValid()){
            $formation = $form->getData(); // On récupère les données
            $entityManager->persist($formation); // Équivalent du prepare
            $entityManager->flush(); // Équivalent du execute

            return $this->redirectToRoute("app_formation");
        }

        return $this->render('formation/add.html.twig', [
            'formAddFormation' => $form->createView(),
            'edit' => $formation->getId()
        ]);
    }

    #[Route('/formation/{id}/delete', name : 'delete_formation')]
    public function delete(EntityManagerInterface $entityManager, Formation $formation = null): Response{
        $entityManager->remove($formation);
        $entityManager->flush();

        return $this->redirectToRoute("app_formation");
    }


    #[Route('/formation/{id}', name: 'show_formation')]
    public function show(Formation $formation, SessionRepository $sessionRepository): Response{

        /* Trouver toutes les sessions de cette formation */
        $sessions = $sessionRepository->findBy(["formation" => $formation->getId()], ["dateDebut" => "ASC"]);
        return $this->render('formation/show.html.twig',[
            'formation' => $formation,
            'sessions' => $sessions
        ]);
    }
}