<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Repository\FormationRepository;
use App\Repository\SessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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