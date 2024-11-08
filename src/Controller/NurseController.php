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
// CREATE
    //Create nurse with the next attributes: first_name, last_name, email and password
    #[Route('/create', methods: ['POST'], name: 'app_nurse_create')]
    public function createNurse(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        //We send the attributes of the Nurse with postMan doing the function that the front-end would do with its inputs                
        $firstName = $request->request->get('first_name');
        $lastName = $request->request->get('last_name');
        $email = $request->request->get('email');
        $password = $request->request->get('password');


        // We validate that all required fields are sent
        if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
            return new JsonResponse("Empty fields",Response::HTTP_BAD_REQUEST);
        }

        //We verify within the database that the email is not used by another nurse
        $repeatedEmail = $entityManager->getRepository(Nurses::class)->findBy(['email' => $email]);
        if ($repeatedEmail) {
            return new JsonResponse("Repeated email",Response::HTTP_BAD_REQUEST);
        }else {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)){ //Verificación del formato correcto del Email.
                return new JsonResponse(Response::HTTP_BAD_REQUEST);
            }
        }

        $nurse = new Nurses();
        //We create the nurse and control it as a nurse object that will save all the data
        $nurse->setFirstName($firstName);
        $nurse->setLastName($lastName);
        $nurse->setEmail($email);
        $nurse->setPassword($password);

        
        $entityManager->persist($nurse); 
        /*The persist method is like a create in MySQL, when you call persist($entity), you tell Doctrine that this entity should be managed and 
        that your changes should be saved to the database in the next flush operation */
        $entityManager->flush();

        return new JsonResponse($nurse,Response::HTTP_CREATED);
    }
  
// READ
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

    //Search a nurse by name
    #[Route('/findName', methods: ['GET'], name: 'app_nurse_findName')]
    public function findName(Request $requestNurse, EntityManagerInterface $entityManager): JsonResponse
    {
        $nameNurse = $requestNurse->query->get(key: 'first_name');
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

        // If the nurse's name not found we return 404 error message
        return new JsonResponse(['message' => 'This nurse name does not exist.'], Response::HTTP_NOT_FOUND);
    }
    
    #[Route('/findByID', methods: ['GET'], name: 'app_nurse_findID')]
    public function findByID(Request $requestNurse, EntityManagerInterface $entityManager): JsonResponse
    {
        $nameNurse = $requestNurse->query->get('id');
        $nurseRepository = $entityManager->getRepository(Nurses::class);
        $nurses = $nurseRepository->find(['id' => $nameNurse]);

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

        // If the nurse's name not found we return 404 error message
        return new JsonResponse(Response::HTTP_NOT_FOUND);
    }

// UPDATE
    //Modification of nurses.
    #[Route('/updateById', methods: ['PUT'], name: 'app_nurse_update')]
    public function updateByName(Request $requestId, EntityManagerInterface $entityManager): JsonResponse //Request get the information from de request,
    {
        $nurseById = $requestId->query->get(key: 'id'); //I get the id passed by the ID URL(STRING).
        $nurseByFirstName = $requestId->query->get(key: 'first_name'); //I get the first_name passed by the ID URL(STRING).
        $nurseByLastName = $requestId->query->get(key: 'last_name');
        $nurseByEmail = $requestId->query->get(key: 'email');
        $nurseByPassword = $requestId->query->get(key: 'password'); 
        //I get an object from all the data by searching for it by ID.
        $nurseRepository = $entityManager->getRepository(Nurses::class)->find(['id' => $nurseById]); 
        //El repositorio(get repository) crea un objeto u objetos de la busqueda que devuelve la base de datos, se almacenan ahí.

        if ($nurseRepository == null) { //If the object does not exist
            return new JsonResponse(Response::HTTP_NOT_FOUND);
        }else {
            if (!empty($nurseByFirstName) || !empty($nurseByLastName) || !empty($nurseByEmail) || !empty($nurseByPassword)){ //I see that all data is passed
                if (!filter_var($nurseByEmail, FILTER_VALIDATE_EMAIL)){
                    return new JsonResponse(Response::HTTP_BAD_REQUEST);
                }  
                $nurseRepository->setFirstName($nurseByFirstName); //I change each of the data through the set.
                $nurseRepository->setLastName($nurseByLastName);
                $nurseRepository->setEmail($nurseByEmail);
                $nurseRepository->setPassword($nurseByPassword);

                $entityManager->flush(); //I make the changes to the database.
                
                return new JsonResponse(Response::HTTP_OK); //Show whether there is an error or not.

            }else {
                return new JsonResponse(Response::HTTP_BAD_REQUEST);
            }
        }
    }
  
// DELETE
    // Delete by ID
    #[Route('/deleteById', name: 'app_nurse_deleteById', methods: ['DELETE'])]
    public function deleteById(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {   
        // Front-End Input
        $idNurse = $request->query->get('id_nurse');

        // Acces to database
        $nurseRepository = $entityManager->getRepository(Nurses::class);
        $nurses = $nurseRepository->findOneBy(['id' => $idNurse]);

        if ($nurses != null) {
            $entityManager->remove($nurses);
            $entityManager->flush();

            return new JsonResponse(Response::HTTP_OK);
        } else {

            return new JsonResponse(Response::HTTP_NOT_FOUND);
        }
    }
}
