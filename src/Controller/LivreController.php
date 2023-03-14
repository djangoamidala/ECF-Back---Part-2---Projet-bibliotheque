<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;

class LivreController extends AbstractController
{
    private $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * @Route("/livre", name="livrelist")
     */
    public function list(): Response
    {
        $livre = $this->db->fetchAllAssociative('SELECT * FROM livre');
        return $this->json($livre);
    }

    /**
     * @Route("/livre/{id}", name="livredetails")
     */
    public function details($id): Response
    {
        $livre = $this->db->fetchAssociative('SELECT * FROM livre WHERE id = ?', [$id]);
        return $this->json($livre);
    }

    /**
     * @Route("/livre/search/{titre}", name="livresearch")
     */
    public function search($titre): Response
    {
        $livre = $this->db->fetchAllAssociative('SELECT * FROM livre WHERE titre LIKE ?', ["%$titre%"]);
        return $this->json($livre);
    }

    /**
     * @Route("/livre/byauteur/{id}", name="livrebyauteur")
     */
    public function listByAuteur($id): Response
    {
        $livre = $this->db->fetchAllAssociative('SELECT * FROM livre WHERE auteur_id = ?', [$id]);
        return $this->json($livre);
    }

    /**
     * @Route("/livre/bygenre/roman", name="ilvrebygenre")
     */
    public function listByGenre(): Response
    {
        $livre = $this->db->fetchAllAssociative('SELECT livre.* FROM livre JOIN Livre_Genre ON livre.id = Livre_Genre.livre_id JOIN genre ON genre.id = Livre_Genre.genre_id WHERE genre.nom LIKE "%roman%"');
        return $this->json($livre);
    }

    /**
     * @Route("/livre/update/{id}", name="livre_update")
     */
    public function updateLivre($id): Response
    {
        $query = "UPDATE livre SET titre = 'Aperiendum est igitur' WHERE id = $id;
              UPDATE Livre_Genre SET genre_id = 5 WHERE livre_id = $id;";
        $result = $this->db->executeQuery($query);

        return new Response('Le livre a été mis à jour avec succès');
    }

}