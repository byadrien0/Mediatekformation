<?php

namespace App\Controller;

use App\Entity\Playlist;
use App\Form\PlaylistType;
use App\Repository\PlaylistRepository;
use App\Repository\FormationRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class BackOfficePlaylistsController extends AbstractController
{
    private PlaylistRepository $playlistRepository;
    private FormationRepository $formationRepository;

    public function __construct(PlaylistRepository $playlistRepository, FormationRepository $formationRepository)
    {
        $this->playlistRepository = $playlistRepository;
        $this->formationRepository = $formationRepository;
    }

    #[Route('/admin/playlists', name: 'admin.playlists')]
    public function index(Request $request): Response
    {
        $champ = $request->get('champ', 'name');
        $ordre = $request->get('ordre', 'ASC');
        $valeur = $request->get('filter', '');

        $playlists = $this->playlistRepository->findByContainValue($champ, $valeur);
        return $this->render('admin/playlists.html.twig', [
            'playlists' => $playlists,
            'valeur' => $valeur,
        ]);
    }

    #[Route('/admin/playlists/add', name: 'admin.playlists.add')]
    public function add(Request $request, ManagerRegistry $doctrine): Response
    {
        $playlist = new Playlist();
        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($playlist);
            $entityManager->flush();

            $this->addFlash('success', 'Playlist ajoutée avec succès.');
            return $this->redirectToRoute('admin.playlists');
        }

        return $this->render('admin/playlist_form.html.twig', [
            'form' => $form->createView(),
            'playlist' => $playlist,
        ]);
    }

    #[Route('/admin/playlists/edit/{id}', name: 'admin.playlists.edit')]
    public function edit(Request $request, Playlist $playlist, ManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $doctrine->getManager()->flush();
            $this->addFlash('success', 'Playlist modifiée avec succès.');
            return $this->redirectToRoute('admin.playlists');
        }

        $formations = $this->formationRepository->findBy(['playlist' => $playlist]);

        return $this->render('admin/playlist_form.html.twig', [
            'form' => $form->createView(),
            'playlist' => $playlist,
            'formations' => $formations,
        ]);
    }

    #[Route('/admin/playlists/delete/{id}', name: 'admin.playlists.delete', methods: ['POST'])]
    public function delete(Request $request, Playlist $playlist, ManagerRegistry $doctrine): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete' . $playlist->getId(), $request->request->get('_token'))) {
            if ($playlist->getFormations()->count() > 0) {
                $this->addFlash('error', 'Impossible de supprimer une playlist contenant des formations.');
            } else {
                $entityManager = $doctrine->getManager();
                $entityManager->remove($playlist);
                $entityManager->flush();
                $this->addFlash('success', 'Playlist supprimée avec succès.');
            }
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('admin.playlists');
    }
}