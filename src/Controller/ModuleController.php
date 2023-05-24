<?php

namespace App\Controller;

use App\Entity\Module;
use App\Form\ModuleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ModuleController extends AbstractController
{
    #[Route('/module', name: 'app_module')]
    public function index(): Response
    {
        return $this->render('module/index.html.twig', [
            'controller_name' => 'ModuleController',
        ]);
    }


    #[Route('module/add', name: 'add_module')]
    #[Route('/module/{id}/edit', name: 'edit_module')]
    public function add(EntityManagerInterface $entityManager, Module $module = null, Request $request): Response{
        
        /* Si le module n'existe pas, on le crée */
        if(!$module){
            $module = new Module();
        }
        
        $form = $this->createForm(ModuleType::class, $module); // On crée le formulaire
        $form->handleRequest($request); // Inspecte la requête
        if($form->isSubmitted() && $form->isValid()){
            $module = $form->getData(); // On récupère les données
            $entityManager->persist($module); // Équivalent du prepare
            $entityManager->flush(); // Équivalent du execute

            return $this->redirectToRoute("app_module");
        }

        return $this->render('module/add.html.twig', [
            'formAddModule' => $form->createView(),
            'edit' => $module->getId()
        ]);
    }


    #[Route('/module/{id}', name: 'show_module')]
    public function show(Module $module): Response{

        return $this->render('module/show.html.twig', [
            'module' => $module
        ]);
    }
}
