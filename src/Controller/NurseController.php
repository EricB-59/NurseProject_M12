<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/nurse', name: 'app_nurse')]
class NurseController extends AbstractController {
    // Información de todos los enfermeros registrados 
    #[Route('/Info', name: 'app_nurse_Info')]
    public function nurseInfo(): JsonResponse
    {
        $json_nurse = file_get_contents('DATA.json');
        $json_data = json_decode($json_nurse, true);
        // Retorna los datos como una respuesta JSON
        return new JsonResponse($json_data);
    }
    // Validación de login de un enfermero 
    #[Route('/Login', methods: ['POST'], name: 'app_nurse_Login')]
    public function nurseLogin(Request $request): Response {
        $firstName = $request->request->get('first_name');
        $password = $request->request->get('password');

        $json_data = file_get_contents('DATA.json');
        $data_array = json_decode($json_data, true);
 
        for ($i = 0; $i < count($data_array); $i++) {
            foreach ($data_array[$i] as $desc => $value) {
                if ($desc == "first_name" && $value == $firstName) {
                    if ($data_array[$i]["password"] == $password) {
                        return new Response(true);
                    }
                }
            }
        }
        return new Response(false);
    }

    // Búsqueda de enfermeros por nombre    
    #[Route('/FindByName', name: 'app_nurse_FindByName')]
    public function nurseFindByName(Request $peticionNurse): JsonResponse
    {
        $nameNurse = $peticionNurse ->query -> get('first_name');
        $json_nurse = file_get_contents('DATA.json');
        $json_data = json_decode($json_nurse, associative: true);
        $filtrarNombre = array_filter($json_data, function($nurse) use ($nameNurse){
            return strtolower($nurse['first_name']) === strtolower($nameNurse);
        });
        return new JsonResponse(array_values($filtrarNombre));
    }

    
}
