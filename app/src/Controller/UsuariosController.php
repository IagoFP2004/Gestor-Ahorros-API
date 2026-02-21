<?php

namespace App\Controller;

use App\Entity\Usuarios;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\DocBlock\Tags\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

class UsuariosController extends AbstractController
{
    //Ruta para obtener los usuarios dados de alta en la aplicacion
    #[Route('/users', name: 'get_users', methods: ['GET'])]
    public function getUsers(Request $request, EntityManagerInterface $entitymanager): Response
    {
        $usuarios = $entitymanager->getRepository(Usuarios::class)->findAll();

        if (!$usuarios) {
            return $this->json(['error' => 'No se encontraron usuarios'], 400);
        }

        if (!$request->isMethod('GET')) {
            throw new HttpException(405, 'Method Not Allowed');
        }


        foreach ($usuarios as $usuario)
        {
            $data[] = [
                'id' => $usuario->getId(),
                'username' => $usuario->getUsername(),
                'email' => $usuario->getEmail(),
                'pass' => $usuario->getPass(),
                'salario' => $usuario->getSalario(),
                'disponible' => $usuario->getSalarioDisponible(),
                'ahorros' => $usuario->getAhorro(),
            ];
        }

        $response = $this->json($data);

        return $response;
    }

    //Ruta para dar de alta a un usuario en la app
    #[Route('/users', name: 'post_users', methods: ['POST'])]
    public function addUser(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(['error' => 'JSON inválido'], 400);
        }

        $errors = [];

        if (empty($data['username'])) {
            $errors['username'] = "El campo username es obligatorio";
        }

        if (empty($data['email'])) {
            $errors['email'] = "El campo email es obligatorio";
        }

        if (empty($data['pass'])) {
            $errors['pass'] = "El campo pass es obligatorio";
        }

        if (!isset($data['salario'])) {
            $errors['salario'] = "El campo salario es obligatorio";
        }

        if (!isset($data['disponible'])) {
            $errors['disponible'] = "El campo disponible es obligatorio";
        }

        if (!isset($data['ahorros'])) {
            $errors['ahorros'] = "El campo ahorros es obligatorio";
        }

        if (!empty($errors)) {
            return $this->json($errors, 400);
        }

        $usuario = new Usuarios();
        $usuario->setUsername($data['username']);
        $usuario->setEmail($data['email']);
        $usuario->setPass(password_hash($data['pass'], PASSWORD_DEFAULT));
        $usuario->setSalario($data['salario']);
        $usuario->setSalarioDisponible($data['disponible']);
        $usuario->setAhorro($data['ahorros']);

        $entityManager->persist($usuario);
        $entityManager->flush();

        return $this->json('Usuario creado con éxito', 201);
    }
}
