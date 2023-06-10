<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepage(Connection $connection)
    {
        $sql = 'SELECT * FROM livre';
        $livres = $connection->fetchAllAssociative($sql);

        return $this->render('home.html.twig', [
            'livres' => $livres,
        ]);
    }
}

