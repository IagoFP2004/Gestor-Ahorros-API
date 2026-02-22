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
use Symfony\Component\Validator\Validator\ValidatorInterface;

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

        $errors = $this->checkData($data, $entityManager);

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

    public function checkData(array $data, EntityManagerInterface $entityManager) : array
    {
        $errors = [];

        if (!isset($data['username'])) {
            $errors['username'] = 'El nombre de usuario es requerido';
        }else if (empty($data['username'])) {
            $errors['username'] = "El campo username no puede estar vacio";
        }else if (!is_string($data['username'])) {
            $errors['username'] = "El campo username debe ser un string";
        }else if (strlen($data['username']) < 3 || strlen($data['username']) > 64) {
            $errors['username'] = "El campo username debe tener al menos 3 caracteres y menos de 64";
        }

        if (!isset($data['email'])) {
            $errors['email'] = 'El email es requerido';
        }else if (empty($data['email'])) {
            $errors['email'] = "El campo email no puede estar vacio";
        }else if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "El campo email debe tener un formato correcto";
        }else if ($this->duplicateEmail($data['email'], $entityManager)) {
           $errors['email'] = "El email ya esta en uso";
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

        return $errors;
    }

    public function duplicateEmail(string $email, EntityManagerInterface $entityManager): bool
    {
        return $entityManager
                ->getRepository(Usuarios::class)
                ->findOneBy(['email' => $email]) !== null;
    }

}
