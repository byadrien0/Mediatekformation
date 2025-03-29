<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class PlaylistsController extends AbstractController
{
    private $playlistRepository;
    private $formationRepository;
    private $categorieRepository;

    public function __construct(
        PlaylistRepository $playlistRepository,
        CategorieRepository $categorieRepository,
        FormationRepository $formationRepository
    ) {
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository = $categorieRepository;
        $this->formationRepository = $formationRepository;
    }

    /**
     * @Route("/playlists", name="playlists")
     */
    #[Route('/playlists', name: 'playlists')]
    public function index(): Response
    {
        $playlists = $this->playlistRepository->findAllOrderByName('ASC');

        // Calculer le nombre de formations pour chaque playlist
        foreach ($playlists as $playlist) {
            $playlist->formationCount = $playlist->getFormationCount();
        }

        $categories = $this->categorieRepository->findAll();
        return $this->render("pages/playlists.html.twig", [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/playlists/tri/{champ}/{ordre}", name="playlists.sort")
     */
    #[Route('/playlists/tri/{champ}/{ordre}', name: 'playlists.sort')]
    public function sort($champ, $ordre): Response
    {
        switch ($champ) {
            case "name":
                $playlists = $this->playlistRepository->findAllOrderByName($ordre);
                break;
            case "formationCount":
                // Vous devrez implémenter cette méthode dans votre repository
                $playlists = $this->playlistRepository->findAllOrderByFormationCount($ordre);
                break;
            default:
                $playlists = $this->playlistRepository->findAllOrderByName('ASC');
        }

        // Calculer le nombre de formations pour chaque playlist
        foreach ($playlists as $playlist) {
            $playlist->formationCount = $playlist->getFormationCount();
        }

        $categories = $this->categorieRepository->findAll();
        return $this->render("pages/playlists.html.twig", [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/playlists/recherche/{champ}/{table}", name="playlists.findallcontain")
     */
    #[Route('/playlists/recherche/{champ}/{table}', name: 'playlists.findallcontain')]
    public function findAllContain($champ, Request $request, $table = ""): Response
    {
        $valeur = $request->get("recherche");
        $playlists = $this->playlistRepository->findByContainValue($champ, $valeur, $table);

        // Calculer le nombre de formations pour chaque playlist
        foreach ($playlists as $playlist) {
            $playlist->formationCount = $playlist->getFormationCount();
        }

        $categories = $this->categorieRepository->findAll();
        return $this->render("pages/playlists.html.twig", [
            'playlists' => $playlists,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }

    /**
     * @Route("/playlists/playlist/{id}", name="playlists.showone")
     */
    #[Route('/playlists/playlist/{id}', name: 'playlists.showone')]
    public function showOne($id): Response
    {
        $playlist = $this->playlistRepository->find($id);
        $playlistCategories = $this->categorieRepository->findAllForOnePlaylist($id);
        $playlistFormations = $this->formationRepository->findAllForOnePlaylist($id);
        return $this->render("pages/playlist.html.twig", [
            'playlist' => $playlist,
            'playlistcategories' => $playlistCategories,
            'playlistformations' => $playlistFormations
        ]);
    }

    /**
     * @Route("/playlists/edit/{id}", name="playlists.edit")
     */
    #[Route('/playlists/edit/{id}', name: 'playlists.edit')]
    public function edit($id, Request $request): Response
    {
        $playlist = $this->playlistRepository->find($id);
        if (!$playlist) {
            throw $this->createNotFoundException('Playlist non trouvée.');
        }

        // Handle form submission logic here (not implemented in this example)
        // ...

        return $this->render("pages/edit_playlist.html.twig", [
            'playlist' => $playlist
        ]);
    }

    /**
     * @Route("/playlists/delete/{id}", name="playlists.delete", methods={"POST"})
     */
    #[Route('/playlists/delete/{id}', name: 'playlists.delete', methods: ['POST'])]
    public function delete($id, Request $request): RedirectResponse
    {
        $playlist = $this->playlistRepository->find($id);
        if (!$playlist) {
            throw $this->createNotFoundException('Playlist non trouvée.');
        }

        if ($this->isCsrfTokenValid('delete' . $playlist->getId(), $request->request->get('_token'))) {
            $this->playlistRepository->remove($playlist);
        }

        return $this->redirectToRoute('playlists');
    }
}