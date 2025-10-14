<?php

namespace App\Controller;

use App\Entity\Project;
use App\Enum\NavbarVariant;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    public function __construct(
        private ProjectRepository $projectRepository,
        private EntityManagerInterface $entityManager,
    ) {}

    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(): Response
    {
        $projects = $this->projectRepository->findAll();

        return $this->render('home/index.html.twig', [
            'projects' => $projects,
            'navbar_variant' => NavbarVariant::HOME,
        ]);
    }

    #[Route('/add', name: 'app_home_add', methods: ['GET', 'POST'])]
    public function add(
        Request $request,
    ): Response
    {
        $project = new Project();

        // Création du formulaire
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        // Vérification de la soumission et de la validité du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            $project->setOwner($this->getUser());
            $this->entityManager->persist($project);
            $this->entityManager->flush();

            // Redirection après la soumission
            return $this->redirectToRoute('app_home');
        }

        // Affichage du formulaire
        return $this->render('home/add.html.twig', [
            'form' => $form,
            'navbar_variant' => NavbarVariant::HOME,
        ]);
    }
}
