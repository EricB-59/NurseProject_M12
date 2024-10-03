<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class NurseController extends AbstractController {
    // Información de todos los enfermeros registrados 
    #[Route('/nurse', name: 'app_nurse')]
    public function index(): JsonResponse
    {
        $json_nurse = file_get_contents('DATA.json');
        $json_data = json_decode($json_nurse, true);
        // Retorna los datos como una respuesta JSON
        return new JsonResponse($json_data);
    }
    // Validación de login de un enfermero
    #[Route('/nurse', name: 'app_nurse')]
    public function login(Request $request): Response {
        if ($request->isMethod('POST')) {
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
        return new Response("NO ES POST");
    }

    // Búsqueda de enfermeros por nombre
}
