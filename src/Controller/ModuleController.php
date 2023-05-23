<?php

namespace App\Controller;

use App\Entity\Module;
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

    #[Route('/module/{id}', name: 'show_module')]
    public function show(Module $module): Response{

        return $this->render('module/show.html.twig', [
            'module' => $module
        ]);
    }
}
