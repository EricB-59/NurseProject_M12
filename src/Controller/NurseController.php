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
    // * CREATE
    // Create nurse with the next attributes: first_name, last_name, email and password


    #[Route('/create', name: 'app_nurse_create', methods: ['POST'])]
    public function createNurse(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // We send the attributes of the Nurse with postMan doing the function that the front-end would do with its inputs
        $data = json_decode($request->getContent(), true);
        $firstName = $data['first_name'];
        $lastName = $data['last_name'];
        $email = $data['email'];
        $profileImg = $data['profileImg'];
        $password = $data['password'];

        // ! verification parameters that must has the password.
        // 1. a number /^(?=.*?[0-9])
        // 2. mandatory special character (?=.*?[#?!@$%^&*-])
        // 3. length of six .{6,}
        $reg = '/^(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/';

        // We validate that all required fields are sent
        if (empty($firstName) || empty($lastName) || empty($email) || empty($profileImg) || empty($password)) {
            return new JsonResponse('Empty fields', Response::HTTP_BAD_REQUEST);
        } else {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { // email format verification.
                return new JsonResponse('Invalid email', Response::HTTP_BAD_REQUEST);
            } elseif (!preg_match($reg, $password)) { // regex function.
                return new JsonResponse('Password paremeters wrong', Response::HTTP_BAD_REQUEST);
            }
        }

        // We verify within the database that the email is not used by another nurse
        $repeatedEmail = $entityManager->getRepository(Nurses::class)->findBy(['email' => $email]);
        if ($repeatedEmail) {
            return new JsonResponse('Repeated email', Response::HTTP_BAD_REQUEST);
        } else {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { // Verificación del formato correcto del Email.
                return new JsonResponse(Response::HTTP_BAD_REQUEST);
            }
        }

        $nurse = new Nurses();
        // We create the nurse and control it as a nurse object that will save all the data
        $nurse->setFirstName(first_name: $firstName);
        $nurse->setLastName($lastName);
        $nurse->setEmail($email);
        $nurse->setProfileImg($profileImg);
        $nurse->setPassword($password);

        $entityManager->persist($nurse);
        /*The persist method is like a create in MySQL, when you call persist($entity), you tell Doctrine that this entity should be managed and
        that your changes should be saved to the database in the next flush operation */
        $entityManager->flush();

        return new JsonResponse('Created', Response::HTTP_CREATED);
    }

    // * READ
    // Information of all registred nurses
    #[Route('/getAll', name: 'app_nurse_getAll')]
    public function getAll(EntityManagerInterface $entityManagerInterface): JsonResponse
    {
        $nursesRepository = $entityManagerInterface->getRepository(Nurses::class);
        $nurses = $nursesRepository->findAll();

        $nursesArray = [];
        foreach ($nurses as $nurse) {
            $nursesArray[] = [
                'id' => $nurse->getId(),
                'first_name' => $nurse->getFirstName(),
                'last_name' => $nurse->getLastName(),
                'email' => $nurse->getEmail(),
                'profileImg' => $nurse->getProfileImg(),
                'password' => $nurse->getPassword(),
            ];
        }

        // Return the data as a Json
        return new JsonResponse($nursesArray, Response::HTTP_OK);
    }

    // Validation of a Nurse login
    #[Route('/login', methods: ['POST'], name: 'app_nurse_login')]
    public function nurseLogin(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'];
        $password = $data['password'];

        $nurses_repo = $entityManager->getRepository(Nurses::class);
        $nurses = $nurses_repo->findOneBy(['email' => $email]);

        if (null == $nurses) {
            return $this->json(false, Response::HTTP_NOT_FOUND);
        } 
        if (!($nurses->getPassword() === $password)) {
            return $this->json(false, Response::HTTP_NOT_FOUND);
        }

        return $this->json($nurses->getId(), Response::HTTP_OK);
    }

    // Search a nurse by name
    #[Route('/findName/{first_name}', methods: ['GET'], name: 'app_nurse_findName')]
    public function findName(string $first_name, EntityManagerInterface $entityManager): JsonResponse
    {
        $nurseRepository = $entityManager->getRepository(Nurses::class);
        $nurses = $nurseRepository->findBy(['first_name' => $first_name]);

        $nurseArray = [];
        if (!empty($nurses)) {
            foreach ($nurses as $nurse) {
                $nurseArray[] = [
                    'id' => $nurse->getId(),
                    'first_name' => $nurse->getFirstName(),
                    'last_name' => $nurse->getLastName(),
                    'email' => $nurse->getEmail(),
                ];
            }
            return new JsonResponse($nurseArray, Response::HTTP_OK);
        }

        // If the nurse's name not found we return 404 error message
        return new JsonResponse(['message' => 'This nurse name does not exist.'], Response::HTTP_NOT_FOUND);
    }

    #[Route('/findByID/{id}', methods: ['GET'], name: 'app_nurse_findID')]
    public function findByID(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $nurseRepository = $entityManager->getRepository(Nurses::class);
        $nurse = $nurseRepository->find($id);

        // Si no se encuentra la enfermera, devuelve un 404
        if (!$nurse) {
            return new JsonResponse(Response::HTTP_NOT_FOUND);
        }

        // Si se encuentra, retorna la información
        $nurseArray = [
            'id' => $nurse->getId(),
            'first_name' => $nurse->getFirstName(),
            'last_name' => $nurse->getLastName(),
            'email' => $nurse->getEmail(),
            'profileImg' => $nurse->getProfileImg()
        ];

        return new JsonResponse($nurseArray, Response::HTTP_OK);
    }

    // * UPDATE
    // Modification of nurses.
    #[Route('/updateById/{id}', methods: ['PUT'], name: 'app_nurse_update')]
    public function updateById(int $id, Request $request, EntityManagerInterface $entityManager): JsonResponse // Request get the information from de request,
    {
        $data = json_decode($request->getContent(), true);

        $nurseByFirstName = $data['first_name'];  // I get the first_name passed by the ID URL(STRING).
        $nurseByLastName = $data['last_name'];
        $nurseByEmail = $data['email'];
        $nurseByProfileImg = $data['profileImg'];
        $nurseByPassword = $data['password'];
        // I get an object from all the data by searching for it by ID.
        $nurseRepository = $entityManager->getRepository(Nurses::class)->find(['id' => $id]);
        // El repositorio(get repository) crea un objeto u objetos de la busqueda que devuelve la base de datos, se almacenan ahí.

        if (null == $nurseRepository) { // If the object does not exist
            return new JsonResponse('Nurse does not exist', Response::HTTP_NOT_FOUND);
        } else {
            if (!empty($nurseByFirstName) || !empty($nurseByLastName) || !empty($nurseByEmail) || !empty($nurseByProfileImg) || !empty($nurseByPassword)) { // I see that all data is passed
                if (!filter_var($nurseByEmail, FILTER_VALIDATE_EMAIL)) {
                    return new JsonResponse('Email invalid', Response::HTTP_BAD_REQUEST);
                }
                $nurseRepository->setFirstName($nurseByFirstName); // I change each of the data through the set.
                $nurseRepository->setLastName($nurseByLastName);
                $nurseRepository->setEmail($nurseByEmail);
                $nurseRepository->setProfileImg($nurseByProfileImg);
                $nurseRepository->setPassword($nurseByPassword);

                $entityManager->flush(); // I make the changes to the database.

                return new JsonResponse(Response::HTTP_OK); // Show whether there is an error or not.
            } else {
                return new JsonResponse('Empty fields', Response::HTTP_BAD_REQUEST);
            }
        }
    }

    // * DELETE
    // Delete by ID
    #[Route('/deleteById/{id}', name: 'app_nurse_deleteById', methods: ['DELETE'])]
    public function deleteById(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        // Acces to database
        $nurseRepository = $entityManager->getRepository(Nurses::class);
        $nurses = $nurseRepository->findOneBy(['id' => $id]);

        if (null != $nurses) {
            $entityManager->remove($nurses);
            $entityManager->flush();

            return new JsonResponse(Response::HTTP_OK);
        } else {
            return new JsonResponse(Response::HTTP_NOT_FOUND);
        }
    }
}
