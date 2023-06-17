<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
class AdminController extends AbstractController
{
    private $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }
/**
 * @Route("/admin/livre", name="admin_livre")
 */

public function adminLivre(Connection $connection, SessionInterface $session): Response
{
    // Vérifier si l'utilisateur a le rôle 'ROLE_ADMIN' dans la session
    if (!$session->has('user') || !isset($session->get('user')['roles']) || strpos($session->get('user')['roles'], 'ROLE_ADMIN') === false) {
        throw $this->createAccessDeniedException('Accès refusé. Rôle administrateur requis.');
    }

    $sql = 'SELECT * FROM livre';
    $livres = $connection->fetchAllAssociative($sql);

    return $this->render('admin_livre.html.twig', [
        'livres' => $livres,
    ]);
}

/**
 * @Route("/admin/emprunteur", name="admin_emprunt")
 */
public function adminEmprunteur(Connection $connection, SessionInterface $session): Response
{
    // Vérifier si l'utilisateur a le rôle 'ROLE_ADMIN' dans la session
    if (!$session->has('user') || !isset($session->get('user')['roles']) || strpos($session->get('user')['roles'], 'ROLE_ADMIN') === false) {
        throw $this->createAccessDeniedException('Accès refusé. Rôle administrateur requis.');
    }

    $sql = 'SELECT * FROM emprunt';
    $emprunts = $connection->fetchAllAssociative($sql);

    return $this->render('admin_emprunt.html.twig', [
        'emprunts' => $emprunts,
    ]);
}

/**
 * @Route("/admin/livre/{id}/edit", name="admin_livre_edit")
 */
public function adminLivreEdit(Request $request, $id, SessionInterface $session): Response
{
    // Vérifier si l'utilisateur a le rôle 'ROLE_ADMIN' dans la session
    if (!$session->has('user') || !isset($session->get('user')['roles']) || strpos($session->get('user')['roles'], 'ROLE_ADMIN') === false) {
        throw $this->createAccessDeniedException('Accès refusé. Rôle administrateur requis.');
    }

    // Récupérer le livre à partir de la base de données
    $livre = $this->db->executeQuery("SELECT * FROM livre WHERE id = ?", [$id])->fetchAssociative();

    // Créer le formulaire
    $form = $this->createFormBuilder($livre)
        ->add('titre', TextType::class, [
            'label' => 'Nouveau titre :'
        ])
        ->add('save', SubmitType::class, [
            'label' => 'Enregistrer'
        ])
        ->getForm();

    // Gérer la soumission du formulaire
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        // Mettre à jour le titre du livre dans la base de données
        $nouveauTitre = $form->getData()['titre'];
        $this->db->executeQuery("UPDATE livre SET titre = ? WHERE id = ?", [$nouveauTitre, $id]);

        // Rediriger vers la page de détails du livre modifié ou une autre page
        return $this->redirectToRoute('admin_livre', ['id' => $id]);
    }

    // Récupérer à nouveau la liste des livres depuis la base de données
    $livres = $this->db->fetchAllAssociative('SELECT * FROM livre');

    return $this->render('admin_livre_edit.html.twig', [
        'form' => $form->createView(),
        'livres' => $livres, // Passer la liste des livres à la vue
    ]);
}

/**
 * @Route("/admin/user", name="admin_user")
 */
public function admin_user(Connection $connection, SessionInterface $session)
{
    // Vérifier si l'utilisateur est authentifié
    if (!$session->has('user') || !isset($session->get('user')['roles'])) {
        throw $this->createAccessDeniedException('Accès refusé.');
    }

    // Vérifier si l'utilisateur a le rôle 'ROLE_ADMIN' dans la session
    $userRolesString = $session->get('user')['roles'];
    if (strpos($userRolesString, 'ROLE_ADMIN') === false) {
        throw $this->createAccessDeniedException('Accès refusé. Rôle administrateur requis.');
    }

    $sql = 'SELECT * FROM user';
    $users = $connection->fetchAllAssociative($sql);

    return $this->render('admin_user.html.twig', [
        'users' => $users,
    ]);
}

    /**
     * @Route("/admin/user/{id}/edit", name="admin_user_edit")
     */
    public function adminUserEdit(Request $request, $id, SessionInterface $session): Response
    {
        // Vérifier si l'utilisateur a le rôle 'ROLE_ADMIN' dans la session
        if (!$session->has('user') || !isset($session->get('user')['roles']) || strpos($session->get('user')['roles'], 'ROLE_ADMIN') === false) {
            throw $this->createAccessDeniedException('Accès refusé. Rôle administrateur requis.');
        }

        // Récupérer le user à partir de la base de données
        $user = $this->db->executeQuery("SELECT * FROM user WHERE id = ?", [$id])->fetchAssociative();

        // Créer le formulaire
        $form = $this->createFormBuilder($user)
            ->add('email', TextType::class, [
                'label' => 'Email :'
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Password :'
            ])
            ->add('roles', TextType::class, [
                'label' => 'Roles :'
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer'
            ])
            ->getForm();

        // Gérer la soumission du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Mettre à jour le titre du user dans la base de données
            $data = $form->getData();
            $email = $data['email'];
            $roles = $data['roles'];
            $password = $data['password'];

            // Mettre à jour le mot de passe seulement s'il est modifié
            if (!empty($password)) {
                // Hasher le nouveau mot de passe
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Mettre à jour le mot de passe dans la base de données
                $this->db->executeQuery("UPDATE user SET password = ? WHERE id = ?", [$hashedPassword, $id]);
            }

            $this->db->executeQuery("UPDATE user SET email = ?, roles = ? WHERE id = ?", [$email, $roles, $id]);

            // Rediriger vers la page de détails du user modifié ou une autre page
            return $this->redirectToRoute('admin_user', ['id' => $id]);
        }

        // Récupérer à nouveau la liste des users depuis la base de données
        $users = $this->db->fetchAllAssociative('SELECT * FROM user');

        return $this->render('admin_user_edit.html.twig', [
            'form' => $form->createView(),
            'users' => $users, // Passer la liste des users à la vue
        ]);
    }

// Cette fonction n'est plus utilisé car directement gérer dans faker mais peut être utilisé pour mettre à jour les mots de passe en brut
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
    return new Response('Mots de passe mis à jour.', Response::HTTP_OK);
}


/**
 * @Route("/admin/emprunt/{id}", name="admin_emprunt_details")
 */
public function adminEmprunteurdetails($id, Connection $connection, SessionInterface $session): Response
{
    // Vérifier si l'utilisateur a le rôle 'ROLE_ADMIN' dans la session
    if (!$session->has('user') || !isset($session->get('user')['roles']) || strpos($session->get('user')['roles'], 'ROLE_ADMIN') === false) {
        throw $this->createAccessDeniedException('Accès refusé. Rôle administrateur requis.');
    }

    $sql = 'SELECT * FROM emprunt WHERE id = ?';
    $emprunt = $connection->fetchAssociative($sql, [$id]);

    if (!$emprunt) {
        throw $this->createNotFoundException('Emprunteur non trouvé.');
    }

    return $this->json($emprunt);

}

/**
 * @Route("/admin/emprunteur/{id}/edit", name="admin_emprunteur_edit")
 */
public function adminEmprunteurEdit(Request $request, $id, Connection $connection, SessionInterface $session): Response
{
    // Vérifier si l'utilisateur a le rôle "ROLE_ADMIN' dans la session
    if (!$session->has('user') || !isset($session->get('user')['roles']) || strpos($session->get('user')['roles'], 'ROLE_ADMIN') === false) {
        throw $this->createAccessDeniedException('Accès refusé. Rôle administrateur requis.');
    }

    $sql = 'SELECT * FROM emprunt WHERE id = ?';
    $emprunt = $connection->fetchAssociative($sql, [$id]);

    if (!$emprunt) {
        throw $this->createNotFoundException('Emprunteur non trouvé.');
    }

    // Créer le formulaire
    $form = $this->createFormBuilder($emprunt)
        ->add('date_emprunt', TextType::class, [
            'label' => 'date_emprunt :'
        ])
        ->add('date_retour', TextType::class, [
            'label' => 'date_retour :'
        ])
        ->add('emprunteur_id', TextType::class, [
            'label' => 'emprunteur_id :'
        ])
        ->add('livre_id', TextType::class, [
            'label' => 'livre_id :'
        ])
        ->add('save', SubmitType::class, [
            'label' => 'Enregistrer'
        ])
        ->getForm();

    // Gérer l'envoi du formulaire
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        // Mettre à jour les données de l'emprunteur dans la base de données
        $nouveauNom = $form->getData()['date_emprunt'];
        $nouveauPrenom = $form->getData()['date_retour'];
        $nouveauTel = $form->getData()['emprunteur_id'];
        $connection->executeQuery("UPDATE emprunt SET date_emprunt = ?, date_retour = ?, emprunteur_id = ? WHERE id = ?", [$nouveauNom, $nouveauPrenom, $nouveauTel, $id]);

        // Rediriger vers la page de détails de l'emprunteur modifié
        return $this->redirectToRoute('admin_emprunt_details', ['id' => $id]);
    }

    return $this->render('admin_emprunteur_edit.html.twig', [
        'form' => $form->createView(),
        'emprunteur' => $emprunt,
    ]);
}

/**
 * @Route("/admin/emprunteur/{id}/delete", name="admin_emprunteur_delete")
 */
public function adminEmprunteurDelete($id, Connection $connection, SessionInterface $session): Response
{
    // Vérifier si l'utilisateur a le rôle 'ROLE_ADMIN' dans la session
    if (!$session->has('user') || !isset($session->get('user')['roles']) || strpos($session->get('user')['roles'], 'ROLE_ADMIN') === false) {
        throw $this->createAccessDeniedException('Accès refusé. Rôle administrateur requis.');
    }

    $sql = 'SELECT * FROM emprunteur WHERE id = ?';
    $emprunteur = $connection->fetchAssociative($sql, [$id]);

    if (!$emprunteur) {
        throw $this->createNotFoundException('Emprunteur non trouvé.');
    }

    // Supprimer l'emprunteur de la base de données
    $connection->executeQuery("DELETE FROM emprunteur WHERE id = ?", [$id]);

    // Rediriger vers la page de liste des emprunteurs (uniquement accessible par l'admin)
    return $this->redirectToRoute('admin_emprunteur');
}
}