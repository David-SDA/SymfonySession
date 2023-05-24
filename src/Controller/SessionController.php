<?php

namespace App\Controller;

use App\Entity\Session;
use App\Form\SessionType;
use App\Repository\SessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SessionController extends AbstractController
{
    #[Route('/session', name: 'app_session')]
    public function index(SessionRepository $sessionRepository): Response{

        /* Trouver toutes les sessions */
        $sessions = $sessionRepository->findBy([],["dateDebut" => "ASC"]);
        return $this->render('session/index.html.twig', [
            'sessions' => $sessions
        ]);
    }


    #[Route('session/add', name: 'add_session')]
    #[Route('/session/{id}/edit', name: 'edit_session')]
    public function add(EntityManagerInterface $entityManager, Session $session = null, Request $request): Response{
        
        /* Si le session n'existe pas, on le crée */
        if(!$session){
            $session = new Session();
        }
        
        $form = $this->createForm(SessionType::class, $session); // On crée le formulaire
        $form->handleRequest($request); // Inspecte la requête
        if($form->isSubmitted() && $form->isValid()){
            $session = $form->getData(); // On récupère les données
            $entityManager->persist($session); // Équivalent du prepare
            $entityManager->flush(); // Équivalent du execute

            return $this->redirectToRoute("app_session");
        }

        return $this->render('session/add.html.twig', [
            'formAddSession' => $form->createView(),
            'edit' => $session->getId()
        ]);
    }


    #[Route('/session/{id}/delete', name : 'delete_session')]
    public function delete(EntityManagerInterface $entityManager, Session $session = null): Response{
        $entityManager->remove($session);
        $entityManager->flush();

        return $this->redirectToRoute("app_session");
    }


    #[Route('/session/{id}', name: 'show_session')]
    public function show(Session $session): Response{
        
        return $this->render('session/show.html.twig',[
            'session' => $session
        ]);
    }
}
