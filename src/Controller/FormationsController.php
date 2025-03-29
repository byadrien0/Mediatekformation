<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\FormationRepository;
use App\Repository\CategorieRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class FormationsController extends AbstractController
{
    private FormationRepository $formationRepository;
    private CategorieRepository $categorieRepository;

    public function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository)
    {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository = $categorieRepository;
    }

    #[Route('/formations', name: 'formations')]
    public function index(): Response
    {
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();

        return $this->render('pages/formations.html.twig', [
            'formations' => $formations,
            'categories' => $categories,
        ]);
    }

    #[Route('/formations/tri/{champ}/{ordre}/{table}', name: 'formations.sort')]
    public function sort($champ, $ordre, $table = ""): Response
    {
        $formations = $this->formationRepository->findAllOrderBy($champ, $ordre, $table);
        $categories = $this->categorieRepository->findAll();

        return $this->render('pages/formations.html.twig', [
            'formations' => $formations,
            'categories' => $categories,
        ]);
    }

    #[Route('/formations/recherche/{champ}/{table}', name: 'formations.findallcontain')]
    public function findAllContain($champ, Request $request, $table = ""): Response
    {
        $valeur = $request->get("recherche");
        $formations = $this->formationRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();

        return $this->render('pages/formations.html.twig', [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table,
        ]);
    }

    #[Route('/formations/formation/{id}', name: 'formations.showone')]
    public function showOne($id): Response
    {
        $formation = $this->formationRepository->find($id);
        return $this->render('pages/formation.html.twig', [
            'formation' => $formation,
        ]);
    }

    #[Route('/formations/edit/{id}', name: 'formations.edit')]
    public function edit(Request $request, Formation $formation, ManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($formation);
            $entityManager->flush();

            $this->addFlash('success', 'Formation modifiée avec succès.');
            return $this->redirectToRoute('formations');
        }

        return $this->render('pages/formation_edit.html.twig', [
            'form' => $form->createView(),
            'formation' => $formation,
        ]);
    }

    #[Route('/formations/delete/{id}', name: 'formations.delete', methods: ['POST', 'DELETE'])]
    public function delete(Request $request, Formation $formation, ManagerRegistry $doctrine): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete' . $formation->getId(), $request->request->get('_token'))) {
            $entityManager = $doctrine->getManager();

            // Retirer la formation de la playlist (si elle est associée)
            if ($formation->getPlaylist() !== null) {
                $formation->getPlaylist()->removeFormation($formation);
            }

            $entityManager->remove($formation);
            $entityManager->flush();

            $this->addFlash('success', 'Formation supprimée avec succès.');
        } else {
            $this->addFlash('error', 'Token invalide.');
        }
        return $this->redirectToRoute('formations');
    }

    #[Route('/formations/filter', name: 'formations.filter', methods: ['GET', 'POST'])]
    public function filter(Request $request): Response
    {
        // Logique de filtrage des formations
        $filter = $request->query->get('filter', '');
        $formations = $this->formationRepository->findByFilter($filter);

        return $this->render('formations/index.html.twig', [
            'formations' => $formations,
        ]);
    }

    #[Route('/formations/add', name: 'formations.add', methods: ['GET', 'POST'])]
    public function add(Request $request, ManagerRegistry $doctrine): Response
    {
        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($formation);
            $entityManager->flush();

            $this->addFlash('success', 'Formation ajoutée avec succès.');
            return $this->redirectToRoute('formations');
        }

        return $this->render('formations/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}