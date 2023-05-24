<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategorieController extends AbstractController
{
    #[Route('/categorie', name: 'app_categorie')]
    public function index(CategorieRepository $categorieRepository): Response {

        /* Trouver toutes les catégories */
        $categories = $categorieRepository->findBy([], ["libelle" => "ASC"]);
        return $this->render('categorie/index.html.twig', [
            'categories' => $categories,
        ]);
    }


    #[Route('categorie/add', name: 'add_categorie')]
    #[Route('/categorie/{id}/edit', name: 'edit_categorie')]
    public function add(EntityManagerInterface $entityManager, Categorie $categorie = null, Request $request): Response{
        
        /* Si la categorie n'existe pas, on le crée */
        if(!$categorie){
            $categorie = new Categorie();
        }
        
        $form = $this->createForm(CategorieType::class, $categorie); // On crée le formulaire
        $form->handleRequest($request); // Inspecte la requête
        if($form->isSubmitted() && $form->isValid()){
            $categorie = $form->getData(); // On récupère les données
            $entityManager->persist($categorie); // Équivalent du prepare
            $entityManager->flush(); // Équivalent du execute

            return $this->redirectToRoute("app_categorie");
        }

        return $this->render('categorie/add.html.twig', [
            'formAddCategorie' => $form->createView(),
            'edit' => $categorie->getId()
        ]);
    }


    #[Route('/categorie/{id}/delete', name : 'delete_categorie')]
    public function delete(EntityManagerInterface $entityManager, Categorie $categorie = null): Response{
        $entityManager->remove($categorie);
        $entityManager->flush();

        return $this->redirectToRoute("app_categorie");
    }


    #[Route('/categorie/{id}', name: 'show_categorie')]
    public function show(Categorie $categorie): Response{

        return $this->render('categorie/show.html.twig', [
            'categorie' => $categorie
        ]);
    }
}
