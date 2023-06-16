<?php

namespace App\Controller;


use Doctrine\DBAL\Connection;
use Faker\Factory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class FakerController extends AbstractController
{
    private $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

/**
 * @Route("/generate-fake-data", name="generate_fake_data")
 */
public function generateFakeData(Request $request, Connection $connection): Response
{
    // Créez une instance de Faker
    $faker = Factory::create();

       // Générez les données factices pour la table "user"
       for ($i = 0; $i < 100; $i++) {
        $email = $faker->email;
        $roles = 'ROLE_USER';
        $password = '123';
        $enabled = $faker->boolean;
        $createdAt = $faker->dateTime->format('Y-m-d H:i:s');
        $updatedAt = $faker->dateTime->format('Y-m-d H:i:s');
        $emprunteurId = $faker->numberBetween(1, 100);

        // Cryptez le mot de passe avec password_hash
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sqlUser = "
            INSERT INTO user (email, roles, password, enabled, created_at, updated_at, emprunteur_id)
            VALUES (
                :email,
                :roles,
                :password,
                :enabled,
                :createdAt,
                :updatedAt,
                :emprunteurId
            )
        ";

        $stmt = $connection->prepare($sqlUser);
        $stmt->bindValue('email', $email);
        $stmt->bindValue('roles', $roles);
        $stmt->bindValue('password', $hashedPassword);
        $stmt->bindValue('enabled', $enabled, \PDO::PARAM_BOOL);
        $stmt->bindValue('createdAt', $createdAt);
        $stmt->bindValue('updatedAt', $updatedAt);
        $stmt->bindValue('emprunteurId', $emprunteurId);
        $stmt->execute();
    }


    // Générez les données factices pour la table "livre"
    for ($i = 0; $i < 1000; $i++) {
        $titre = 'Livre' . $i;
        $anneeEdition = $faker->numberBetween(1900, 2023);
        $nombrePages = $faker->numberBetween(50, 1000);
        $codeIsbn = $faker->isbn13();
        $auteurId = $faker->numberBetween(1, 4);

        $sqlLivre = "
            INSERT INTO livre (titre, annee_edition, nombre_pages, code_isbn, auteur_id)
            VALUES (
                '$titre',
                $anneeEdition,
                $nombrePages,
                '$codeIsbn',
                $auteurId
            )
        ";

        $connection->exec($sqlLivre);
    }

    // Générez les données factices pour la table "auteur"
    for ($i = 0; $i < 500; $i++) {
        $nom = 'Nom' . $i;
        $prenom = 'Prenom' . $i;

        $sqlAuteur = "
            INSERT INTO auteur (nom, prenom)
            VALUES (
                '$nom',
                '$prenom'
            )
        ";

        $connection->exec($sqlAuteur);
    }

    return new Response('Amuse toi bien ^^');
}
}