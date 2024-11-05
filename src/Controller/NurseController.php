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
        $firstName = $request->request->get('first_name');
        $password = $request->request->get('password');

        $nurses_repo = $entityManager->getRepository(Nurses::class);
        $nurses = $nurses_repo->findOneBy(array('first_name' => $firstName));

        if ($nurses == null) {
            return $this->json(false, Response::HTTP_NOT_FOUND);
        } else{
            if ($nurses->getPassword() === $password) {
                return $this->json(true, Response::HTTP_OK);
            } else{
                return $this->json(false, Response::HTTP_NOT_FOUND);
            }
        }
    }

    // Búsqueda de enfermeros por nombre
    #[Route('/findName', name: 'app_nurse_findName')]
    public function findName(Request $peticionNurse, EntityManagerInterface $entityManager): JsonResponse
    {
        $nameNurse = $peticionNurse->query->get(key: 'first_name');
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

    //Modificación de enfermeros.
    #[Route('/updateById', methods: ['PUT'], name: 'app_nurse_update')]
    public function updateByName(Request $peticionId, EntityManagerInterface $entityManager): JsonResponse //Request obtiene la información de la petición,
    {
        $nurseById = $peticionId->query->get(key: 'id'); //Obtengo el id pasado por la URL del ID (STRING).
        $nurseByFirstName = $peticionId->query->get(key: 'first_name'); //Obtengo el first_name pasado por la URL del ID (STRING).
        $nurseByLastName = $peticionId->query->get(key: 'last_name');
        $nurseByEmail = $peticionId->query->get(key: 'email');
        $nurseByPassword = $peticionId->query->get(key: 'password'); 
        //Obtengo un objecto de todos los datos buscándolo por el ID.
        $nurseRepository = $entityManager->getRepository(Nurses::class)->find(['id' => $nurseById]); 
        
        if ($nurseRepository == null) { //Si no existe el objeto
            return new JsonResponse(Response::HTTP_NOT_FOUND);
        }else {
            if (!empty($nurseByFirstName) || !empty($nurseByLastName) || !empty($nurseByEmail) || !empty($nurseByPassword)){ //Veo que todos los datos sean pasados
                $nurseRepository->setFirstName($nurseByFirstName); //Cambio cada uno de los datos mediante el set.
                $nurseRepository->setLastName($nurseByLastName);
                $nurseRepository->setEmail($nurseByEmail);
                $nurseRepository->setPassword($nurseByPassword);

                $entityManager->flush(); //Hago los cambios en la base de datos.
                
                return new JsonResponse(Response::HTTP_OK); //Muestro si hay o no error.
            }else {
                return new JsonResponse(Response::HTTP_BAD_REQUEST);
            }
        }
    }


}
