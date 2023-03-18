<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;

class EmprunteurController extends AbstractController
{
    private $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * @Route("/test/emprunt", name="empruntlist")
     */
    public function listEmprunt(): Response
    {
        $emprunteur = $this->db->fetchAllAssociative('SELECT * FROM emprunteur ORDER BY nom, prenom;');
        return $this->json($emprunteur);
    }

    /**
     * @Route("/test/emprunt/foo", name="empruntfoo")
     */
    public function listEmprint(): Response
    {
        $emprunteur = $this->db->fetchAllAssociative('SELECT * FROM emprunteur WHERE nom LIKE "%foo%" OR prenom LIKE "%foo%" ORDER BY nom, prenom');
        return $this->json($emprunteur);
    }
    /**
     * @Route("/test/emprunt/3", name="empruntByid")
     */
    public function EmpruntById(): Response
    {
        $emprunteur = $this->db->fetchAllAssociative('SELECT * FROM emprunteur WHERE user_id = 3');
        return $this->json($emprunteur);
    }

    /**
     * @Route("/test/emprunt/date", name="empruntByDate")
     */
    public function EmprunteurByDate(): Response
    {
        $emprunteur = $this->db->fetchAllAssociative('SELECT * FROM emprunt ORDER BY date_emprunt DESC LIMIT 3');
        return $this->json($emprunteur);
    }

    /**
     * @Route("/test/emprunt/2", name="EmprunteurId")
     */
    public function EmprunteurId(): Response
    {
        $emprunteur = $this->db->fetchAllAssociative('SELECT * FROM emprunt WHERE emprunteur_id = 2 ORDER BY date_emprunt ASC');
        return $this->json($emprunteur);
    }

    /**
     * @Route("/test/emprunt/livre/3", name="EmpruntLivre3")
     */
    public function EmpruntLivre3(): Response
    {
        $emprunteur = $this->db->fetchAllAssociative('SELECT * FROM emprunt WHERE livre_id = 3 ORDER BY date_emprunt DESC');
        return $this->json($emprunteur);
    }

    /**
     * @Route("/test/emprunt/return", name="EmpruntReturn")
     */
    public function EmpruntReturn(): Response
    {
        $emprunteur = $this->db->fetchAllAssociative('SELECT * FROM emprunt WHERE date_retour IS NULL ORDER BY date_emprunt ASC');
        return $this->json($emprunteur);
    }

    /**
     * @Route("/test/emprunt/create", name="EmpruntCreate")
     */
    public function EmpruntCreate(): Response
    {
        $emprunteur = $this->db->fetchAllAssociative("INSERT INTO emprunt (date_emprunt, date_retour, emprunteur_id, livre_id) VALUES ('2020-12-01 16:00:00', NULL, 1, 1)");
        return new Response('Le livre a été inséré avec succès');
    }

    /**
     * @Route("/test/emprunt/set", name="EmpruntSet")
     */
    public function EmpruntSet(): Response
    {
        $emprunteur = $this->db->fetchAllAssociative("UPDATE emprunt SET date_retour = '2020-05-01 10:00:00' WHERE id = 3;");
        return new Response('Le livre a été inséré avec succès');
    }
}

