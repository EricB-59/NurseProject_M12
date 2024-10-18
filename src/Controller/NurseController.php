<?php

namespace App\Controller;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Nurses;

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
    public function nurseLogin(Request $request): JsonResponse
    {
        $firstName = $request->request->get('first_name');
        $password = $request->request->get('password');

        $json_data = file_get_contents('DATA.json');
        $data_array = json_decode($json_data, true);

        for ($i = 0; $i < count($data_array); ++$i) {
            foreach ($data_array[$i] as $desc => $value) {
                if ('first_name' == $desc && $value == $firstName) {
                    if ($data_array[$i]['password'] == $password) {
                        return $this->json(true, Response::HTTP_OK);
                    }
                }
            }
        }
        return $this->json(false, Response::HTTP_NOT_FOUND);
    }

    // Búsqueda de enfermeros por nombre
    #[Route('/findName', name: 'app_nurse_findName')]
    public function findName(Request $peticionNurse): JsonResponse
    {
        $nameNurse = $peticionNurse->query->get('first_name');
        $json_nurse = file_get_contents('DATA.json');
        $json_data = json_decode($json_nurse, associative: true);
        $filtrarNombre = array_filter($json_data, function ($nurse) use ($nameNurse) {
            return strtolower($nurse['first_name']) === strtolower($nameNurse);
        });
        if (!empty($filtrarNombre)) {
            // Retornar los resultados y el código de estado 200
            return new JsonResponse(array_values($filtrarNombre), Response::HTTP_OK);
        } else {
            // Si no se encuentra el nombre, retornar 404 con un mensaje
            return new JsonResponse(['message' => 'El enfermero con ese nombre no existe.'], Response::HTTP_NOT_FOUND);
        }
    }
}
