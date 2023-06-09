<?php

namespace App\Controller;

use App\Entity\Module;
use App\Entity\Session;
use App\Entity\SessionModule;
use App\Entity\Stagiaire;
use App\Form\SessionType;
use App\Repository\ModuleRepository;
use App\Repository\SessionModuleRepository;
use App\Repository\SessionRepository;
use App\Repository\StagiaireRepository;
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
        $nomSession = $session->getLibelle();
        $entityManager->remove($session);
        $entityManager->flush();
        $this->addFlash('notice', 'La session "' .  $nomSession . '" a été supprimer');

        return $this->redirectToRoute("app_session");
    }


    #[Route('/session/{id}/deleteModule/{module_id}', name: 'deleteModule_session')]
    public function deleteModule(EntityManagerInterface $entityManager, SessionModuleRepository $sessionModuleRepository, Session $id, Module $module_id): Response{
        $sessionModule = $sessionModuleRepository->findOneBy([
            'session' => $id->getId(),
            'module' => $module_id->getId()
        ]);
        $entityManager->remove($sessionModule);
        $entityManager->flush();
        return $this->redirectToRoute("show_session", [
            'id' => $id->getId()
        ]);
    }


    #[Route('/session/{id}/addStagiaire/{stagiaire_id}', name: 'addStagiaire_session')]
    public function addStagiaire(EntityManagerInterface $entityManager, Session $id, Stagiaire $stagiaire_id){
        if(count($id->getStagiaires()) < 3){
            $stagiaire_id->addSession($id);
            $id->addStagiaire($stagiaire_id);
            $entityManager->persist($id);
            $entityManager->persist($stagiaire_id);
            $entityManager->flush();
        }
        return $this->redirectToRoute("show_session", [
            'id' => $id->getId()
        ]);
    }
    #[Route('/session/{id}/addModule/{module_id}', name: 'addModule_session')]
    public function addModule(EntityManagerInterface $entityManager, Session $id, Module $module_id, Request $request): Response{
        if($request->isMethod('POST')){
            $sessionModule = new SessionModule();
            $sessionModule->setSession($id);
            $sessionModule->setModule($module_id);
            $sessionModule->setNombreJours(intval($request->get('nombreJours')));
            $entityManager->persist($sessionModule);
            $entityManager->flush();
        }
        return $this->redirectToRoute('show_session', [
            'id' => $id->getId()
        ]);
    }


    #[Route('/session/{id}', name: 'show_session')]
    public function show(Session $session, ModuleRepository $moduleRepository, StagiaireRepository $stagiaireRepository): Response{
        /* Trouver les modules qui ne sont pas dans la session */
        $modulesPasDansSession = $moduleRepository->getModuleNonInclut($session->getId());
        $stagiairePasDansSession = $stagiaireRepository->getStagiaireNonInscrit($session->getId());
        return $this->render('session/show.html.twig',[
            'session' => $session,
            'modulesPasDansSession' => $modulesPasDansSession,
            'stagiairePasDansSession' => $stagiairePasDansSession
        ]);
    }
}
