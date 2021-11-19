<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Email;
use App\Entity\User;
use App\Entity\Video;
use App\Services\JwtAuth;


class VideoController extends AbstractController
{
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/VideoController.php',
        ]);
    }

    public function create(Request $request, JwtAuth $jwt_auth){

        // recoger el token
        
        // comprobar si es correcto

        // recoger datos por post

        // recoger datos del usuario identificado

        // comprobar y validar datos

        // guardar el nuevo video en la db

        

        $data = [
            'status' => 'success',
            'message'=> 'New video created'
        ];

        return new JsonResponse($data);
    }
}
