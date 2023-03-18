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
     * @Route("/test/livre", name="livrelist")
     */
    public function list(): Response
    {
        $livre = $this->db->fetchAllAssociative('SELECT * FROM livre');
        return $this->json($livre);
    }
    /**
     * @Route("/test/livre/create", name="livrecreate")
     */
    public function CreateLivre(): Response
    {

        $this->db->executeQuery("INSERT INTO livre (titre, annee_edition, nombre_pages, code_isbn, auteur_id)
        VALUES ('Totum autem id externum', 2020, 300, '9790412882714', 2)");

        return new Response('Le livre a été créé avec succès');
    }

    /**
     * @Route("/test/livre/{id}", name="livredetails")
     */
    public function details($id): Response
    {
        $livre = $this->db->fetchAssociative('SELECT * FROM livre WHERE id = ?', [$id]);
        return $this->json($livre);
    }

    /**
     * @Route("/test/livre/search/{titre}", name="livresearch")
     */
    public function search($titre): Response
    {
        $livre = $this->db->fetchAllAssociative('SELECT * FROM livre WHERE titre LIKE ?', ["%$titre%"]);
        return $this->json($livre);
    }

    /**
     * @Route("/test/livre/byauteur/{id}", name="livrebyauteur")
     */
    public function listByAuteur($id): Response
    {
        $livre = $this->db->fetchAllAssociative('SELECT * FROM livre WHERE auteur_id = ?', [$id]);
        return $this->json($livre);
    }

    /**
     * @Route("/test/livre/bygenre/roman", name="ilvrebygenre")
     */
    public function listByGenre(): Response
    {
        $livre = $this->db->fetchAllAssociative('SELECT livre.* FROM livre JOIN Livre_Genre ON livre.id = Livre_Genre.livre_id JOIN genre ON genre.id = Livre_Genre.genre_id WHERE genre.nom LIKE "%roman%"');
        return $this->json($livre);
    }


    /**
     * @Route("/test/livre/update2/{id}", name="livreupdate")
     */
    public function updateLivre($id): Response
    {
        $livre = $this->db->executeQuery("UPDATE livre SET titre = 'Aperiendum est igitur' WHERE id = $id");
        return new Response('Le livre a été mis à jour avec succès');
    }
/**
 * @Route("/test/livre/delete/{id}", name="livredelete")
 */
public function deleteLivre($id): Response
{
    $livre = $this->db->executeQuery("DELETE FROM livre WHERE id = $id");
    return new Response('Le livre a été supprimé avec succès');
}
}