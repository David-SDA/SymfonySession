<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Entity\Session;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormationController extends AbstractController
{
    #[Route('/formation', name: 'app_formation')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $formations = $entityManager->getRepository(Formation::class)->findBy([], ["libelle" => "ASC"]);
        return $this->render('formation/index.html.twig', [
            'formations' => $formations,
        ]);
    }

    #[Route('/formation/{id}', name: 'show_formation')]
    public function show(Formation $formation, EntityManagerInterface $entityManager): Response{
        $sessions = $entityManager->getRepository(Session::class)->findBy(["formation" => $formation->getId()], ["dateDebut" => "ASC"]);
        return $this->render('formation/show.html.twig',[
            'formation' => $formation,
            'sessions' => $sessions
        ]);
    }
}