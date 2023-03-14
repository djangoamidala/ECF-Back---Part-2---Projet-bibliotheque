<?php
// src/Controller/Controller.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;

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
     * @Route("/users/role", name="roleuser")
     */
    public function listByRoleUser(): Response
    {
        
        $users = $this->db->fetchAllAssociative('SELECT * FROM user WHERE roles LIKE "%ROLE_USER%" ' );

            return $this->json($users);   
    }
   

    /**
     * @Route("/users/inactive", name="listinactive")
     */
    public function listInactive($id): Response
    {
        $users = $this->db->fetchAllAssociative('SELECT * FROM user WHERE enabled = 1 ');

        return $this->json($users);
    }
}