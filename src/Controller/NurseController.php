<?php

namespace App\Controller;

use App\Entity\Nurses;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/nurse', name: 'app_nurse')]
class NurseController extends AbstractController
{
    // Información de todos los enfermeros registrados
    #[Route('/getAll', name: 'app_nurse_getAll')]
    public function getAll(EntityManagerInterface $entityManagerInterface): JsonResponse
    {
        $nursesRepository = $entityManagerInterface->getRepository(Nurses::class);
        $nurses = $nursesRepository->findAll();

        $nursesArray = [];
        foreach ($nurses as $nurse) {
            $nursesArray[] = [
                'first_name' => $nurse->getFirstName(),
                'last_name' => $nurse->getLastName(),
                'email' => $nurse->getEmail(),
                'password' => $nurse->getPassword(),
            ];
        }

        // Retorna los datos como una respuesta JSON
        return new JsonResponse($nursesArray, Response::HTTP_OK);
    }

    // Validación de login de un enfermero
    #[Route('/login', methods: ['POST'], name: 'app_nurse_login')]
    public function nurseLogin(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $nurses_repo = $entityManager->getRepository(Nurses::class);
        $nurses = $nurses_repo->findAll();

        $firstName = $request->request->get('first_name');
        $password = $request->request->get('password');

        foreach ($nurses as $nurse) {
            if ($nurse->getFirstName() === $firstName) {
                if ($nurse->getPassword() === $password) {
                    return $this->json(true, Response::HTTP_OK);
                }
            }
        }

        return $this->json(false, Response::HTTP_NOT_FOUND);
    }

    // Búsqueda de enfermeros por nombre
    #[Route('/findName', name: 'app_nurse_findName')]
    public function findName(Request $peticionNurse, EntityManagerInterface $entityManager): JsonResponse
    {
        $nameNurse = $peticionNurse->query->get('first_name');
        $nurseRepository = $entityManager->getRepository(Nurses::class);
        $nurses = $nurseRepository->findBy(['first_name' => $nameNurse]);
        $nurseArray = [];
        if (!empty($nurses)) {
            foreach ($nurses as $nurse) {
                $nurseArray[] = [
                    'id' => $nurse->getId(),
                    'first_name' => $nurse->getFirstName(),
                    'last_name' => $nurse->getLastName(),
                    'email' => $nurse->getEmail(),
                ];

                return new JsonResponse($nurseArray, Response::HTTP_OK);
            }
        }

        // Si no se encuentra el nombre, retornar 404 con un mensaje
        return new JsonResponse(['message' => 'El enfermero con ese nombre no existe.'], Response::HTTP_NOT_FOUND);
    }
}
