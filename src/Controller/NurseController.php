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
        //Enviamos los atributos del Nurse con postMan haciendo la función que haría el front-end con sus inputs
        $firstName = $request->request->get('first_name');
        $lastName = $request->request->get('last_name');
        $email = $request->request->get('email');
        $password = $request->request->get('password');


        // Validamos que se envien todos los campos requeridos
        if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
            return new JsonResponse(Response::HTTP_BAD_REQUEST);
        }

        //Verificamos dentro de la base de datos que el email no este utilizado por otro emfermero
        $emailRepetido = $entityManager->getRepository(Nurses::class)->findBy(['email' => $email]);
        if ($emailRepetido) {
            return new JsonResponse(Response:: HTTP_BAD_REQUEST);
        }

        $nurse = new Nurses();
        //Creamos el nurse y lo controlamos como un objecto nurse que va a guardar todos los datos
        $nurse->setFirstName($firstName);
        $nurse->setLastName($lastName);
        $nurse->setEmail($email);
        $nurse->setPassword($password);

        
        $entityManager->persist($nurse); 
        /*El metodo persist es como un create en MySQL, cuando llamas a persist($entity), le indicas a Doctrine que esta entidad debe ser gestionada y 
        que sus cambios deben ser guardados en la base de datos en la siguiente operación de "flush" */
        $entityManager->flush();

        return new JsonResponse(data: Response::HTTP_CREATED);
    }
  
// READ
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
    #[Route('/findName', methods: ['GET'], name: 'app_nurse_findName')]
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
    
    #[Route('/findByID', methods: ['GET'], name: 'app_nurse_findID')]
    public function findByID(Request $peticionNurse, EntityManagerInterface $entityManager): JsonResponse
    {
        $nameNurse = $peticionNurse->query->get('id');
        $nurseRepository = $entityManager->getRepository(Nurses::class);
        $nurses = $nurseRepository->findBy(['id' => $nameNurse]);

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
        return new JsonResponse(Response::HTTP_NOT_FOUND);
    }

// UPDATE
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
