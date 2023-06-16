<?php
// src/Controller/Controller.php

// Ce fichier contient toutes les requêtes pour la table user 
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    private $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * @Route("/users", name="userlist")
     */
    public function index(): Response
    {
        $users = $this->db->fetchAllAssociative('SELECT * FROM user');

        return $this->json($users);
    }
    /**
     * @Route("/users/role", name="roleuser")
     */
    public function listByRoleUser(): Response
    {

        $user = $this->db->fetchAllAssociative('SELECT * FROM user WHERE roles LIKE "%ROLE_USER%"');

        return $this->json($user);
    }
    
    /**
     * @Route("/users/{id}", name="usershow")
     */
    public function show($id): Response
    {
        $user = $this->db->fetchAllAssociative('SELECT * FROM user WHERE id = ?', [$id]);

        return $this->json($user);
    }

    /**
     * @Route("/users/email/{email}", name="showemail")
     */
    public function showEmail($email): Response
    {
        // Route /users/email/foo.foo@example.com
        $user = $this->db->fetchAllAssociative('SELECT * FROM user WHERE email = ?', [$email]);

        return $this->json($user);
    }

    /**
 * @Route("/admin/delete/{id}", name="user_delete")
 */
public function deleteLivre($id): Response
{
    $livre = $this->db->executeQuery("DELETE FROM user WHERE id = $id");
    return new Response('L\'utilisateur a été supprimé avec succès');
}

/**
* @Route("/inscription", name="inscription")
*/
public function inscription(Request $request, Connection $connection): Response
{
// Vérifier si le formulaire d'inscription a été envoyé
if ($request->isMethod('POST')) {
$email = $request->request->get('_email');
$password = $request->request->get('_password');
 // Vérifier si l'utilisateur existe déjà dans la base de données
 $query = 'SELECT * FROM user WHERE email = ?';
 $params = [$email];
 $existingUser = $connection->executeQuery($query, $params)->fetchAssociative();
 
 if ($existingUser) {
     // Utilisateur déjà enregistré
     $error = 'Un compte avec cet email existe déjà.';
 } else {
     // Hasher le mot de passe
     $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
     
     // Enregistrer le nouvel utilisateur dans la base de données
     $insertQuery = 'INSERT INTO user (email, password, roles) VALUES (?, ?, ?)';
     $insertParams = [$email, $hashedPassword, '["ROLE_USER"]'];
     $connection->executeQuery($insertQuery, $insertParams);
     
     // Rediriger vers la page de connexion ou une autre page
     return $this->redirectToRoute('login');
 }
}

return $this->render('inscription.html.twig', [
'error' => $error ?? null,
]);
}

/**
 * @Route("/login", name="login")
 */
public function login(Request $request, Connection $connection): Response
{
    // Vérifier si le formulaire de connexion a été envoyé
    if ($request->isMethod('POST')) {
        $email = $request->request->get('_email');
        $password = $request->request->get('_password');

        // Récupérer l'utilisateur depuis la base de données
        $query = 'SELECT * FROM user WHERE email = ?';
        $params = [$email];
        $user = $connection->executeQuery($query, $params)->fetchAssociative();

        if ($user && password_verify($password, $user['password'])) {
            // Authentification réussie
            // Récupérer les rôles de l'utilisateur depuis la base de données
            $rolesQuery = 'SELECT roles FROM user WHERE email = ?';
            $rolesParams = [$email];
            $rolesResult = $connection->executeQuery($rolesQuery, $rolesParams)->fetchOne();
            $roles = explode(',', $rolesResult);

            // Enregistrer l'utilisateur et ses rôles dans la session
            $request->getSession()->set('user', $user);
            $request->getSession()->set('roles', $roles);

            // Rediriger vers la page d'accueil ou une autre page
            return $this->redirectToRoute('admin_emprunt');
        } else {
            // Authentification échouée
            $error = 'Identifiants invalides.';
        }
    }

    return $this->render('login.html.twig', [
        'error' => $error ?? null,
    ]);
}
/**
 * @Route("/logout", name="logout")
 */
public function logout(SessionInterface $session): RedirectResponse
{
    // Supprimer le cookie de session et invalider la session
    $session->invalidate();

    // Rediriger vers la page d'accueil ou une autre page
    return $this->redirectToRoute('homepage');
}

/**
 * @Route("/update-passwords", name="update_passwords")
 */
public function updatePasswords(Connection $connection): Response
{
    // Récupérer les utilisateurs avec des mots de passe en brut "123"
    $sql = "SELECT id, password FROM user WHERE password = '123'";
    $users = $connection->executeQuery($sql)->fetchAll();

    // Mettre à jour les mots de passe
    foreach ($users as $user) {
        $hashedPassword = password_hash($user['password'], PASSWORD_DEFAULT);

        $updateSql = "UPDATE user SET password = :hashedPassword WHERE id = :userId";
        $connection->executeStatement($updateSql, [
            'hashedPassword' => $hashedPassword,
            'userId' => $user['id'],
        ]);
    }

    // Retourner une réponse indiquant que les mots de passe ont été mis à jour
    return new Response('Mots de passe mis à jour.');
}





}