<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\User;
use App\Entity\Video;


class UserController extends AbstractController
{

    private function resjson($data){

        // Serializar los datos con el servicio serialize
        $json = $this->get('serializer')->serialize($data, 'json');

        // Response con httpfounfation
        $response = new Response();

        // Asignar contendo a la respuesta
        $response->setContent($json);

        // Indicar formato de respuesta
        $response->headers->set('Content-type', 'application/json');

        // Devolver respuesta   
        return $response;

    }

    public function index(): Response
    {

        $user_repo = $this->getDoctrine()->getRepository(User::Class);
        $video_repo = $this->getDoctrine()->getRepository(Video::Class);

        $users = $user_repo->findAll();
        $user = $user_repo->find(1);
        $videos = $video_repo->findAll();


        // foreach($users as $user){
        //     echo "<h1>{$user->getName()}  {$user->getSurname()}</h1>";

        //     foreach($user->getVideos() as $video){
        //         echo "<p>{$video->getTitle()} - {$video->getUser()->getEmail()}</p>";
        //     }
        // }

        // die();

        // return $this->json([
        //     'message' => 'Welcome to your new controller!',
        //     'path' => 'src/Controller/UserController.php'
        // ]);

        return $this->resjson([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
            'users' => $users,
            'videos' => $videos
        ]);
    }

    public function create(Request $request){

        // Recoger los datos por post
        $json = $request->get('json', null);

        // Decodificar el json
        $params = json_decode($json);
        // var_dump($params);
        // die();

        // Hacer una respuesta por defecto
        $data = [
            'status' => 'error',
            'code'   => 200,
            'message'=> 'El usuario nos se ha creado',
            'params' => $params,
            'json' => $json
        ];
        // 'json'   => $json,

        // Comprobar y validar datos

        // Si la validacion es correcta, crear el objeto del usuario

        // Cifrar la contrasena

        // Comprobar si el usuario existe

        // Si no existe guardarlo en la base de datos

        // Hacer respuesta en json
        // return $this->resjson($data);
        // return $this->json($data);
        return new JsonResponse($data);
    }
}
