<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BackOfficeCategoriesController extends AbstractController
{
    private CategorieRepository $categorieRepository;

    public function __construct(CategorieRepository $categorieRepository)
    {
        $this->categorieRepository = $categorieRepository;
    }

    #[Route('/admin/categories', name: 'admin.categories')]
    public function index(Request $request): Response
    {
        $categories = $this->categorieRepository->findAll();
        $error = $request->query->get('error', null);

        return $this->render('admin/categories.html.twig', [
            'categories' => $categories,
            'error' => $error,
        ]);
    }

    #[Route('/admin/categories/add', name: 'admin.categories.add', methods: ['POST'])]
    public function add(Request $request, ManagerRegistry $doctrine): Response
    {
        $name = $request->request->get('name');

        // Vérifier si la catégorie existe déjà
        $existingCategory = $this->categorieRepository->findOneBy(['name' => $name]);
        if ($existingCategory) {
            return $this->redirectToRoute('admin.categories', [
                'error' => 'Une catégorie avec ce nom existe déjà.',
            ]);
        }

        // Ajouter la nouvelle catégorie
        $category = new Categorie();
        $category->setName($name);

        $entityManager = $doctrine->getManager();
        $entityManager->persist($category);
        $entityManager->flush();

        $this->addFlash('success', 'Catégorie ajoutée avec succès.');
        return $this->redirectToRoute('admin.categories');
    }

    #[Route('/admin/categories/delete/{id}', name: 'admin.categories.delete', methods: ['POST'])]
    public function delete(Request $request, Categorie $categorie, ManagerRegistry $doctrine): Response
    {
        if ($this->isCsrfTokenValid('delete' . $categorie->getId(), $request->request->get('_token'))) {
            if ($categorie->getFormations()->count() > 0) {
                $this->addFlash('error', 'Impossible de supprimer une catégorie rattachée à des formations.');
            } else {
                $entityManager = $doctrine->getManager();
                $entityManager->remove($categorie);
                $entityManager->flush();
                $this->addFlash('success', 'Catégorie supprimée avec succès.');
            }
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('admin.categories');
    }
}