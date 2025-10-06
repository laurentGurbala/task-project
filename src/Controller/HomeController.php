<?php

namespace App\Controller;

use App\Entity\Project;
use App\Enum\NavbarVariant;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    public function __construct(
        private ProjectRepository $projectRepository,
        private EntityManagerInterface $entityManager,
    ) {}

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $projects = $this->projectRepository->findAll();

        return $this->render('home/index.html.twig', [
            'projects' => $projects,
            'navbar_variant' => NavbarVariant::HOME,
        ]);
    }

    public function add(
        Request $request,
    ): Response
    {
        $project = new Project();

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($project);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/add.html.twig', [
            'form' => $form->createView(),
            'navbar_variant' => NavbarVariant::HOME,
        ]);
    }
}
