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
        $nombreSessions = $entityManager->getRepository(Session::class)->getNombreSessionsDansFormation(1);
        return $this->render('formation/index.html.twig', [
            'formations' => $formations,
            'nombreSession' => $nombreSessions
        ]);
    }
}
