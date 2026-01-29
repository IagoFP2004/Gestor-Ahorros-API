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

    #[Route('/users', name: 'post_users', methods: ['POST'])]
    public function addUser(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
        $usuario = new Usuarios();

        $usuario->setUsername($data['username']);
        $usuario->setEmail($data['email']);
        $usuario->setPass(password_hash($data['pass'], PASSWORD_DEFAULT));
        $usuario->setSalario($data['salario']);
        $usuario->setSalarioDisponible($data['disponible']);
        $usuario->setAhorro($data['ahorros']);
        $entityManager->persist($usuario);
        $entityManager->flush();

        $response = $this->json('Usuario creado con exito', 201);

        return $response;
    }
}
