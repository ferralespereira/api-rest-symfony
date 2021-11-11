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

    public function index(): Response{

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

        // Hacer una respuesta por defecto
        
        // Comprobar y validar datos
        $vaidator_error = array();
        if($json != null){
            $name    = (ctype_alpha($params->name)) ? $params->name : array_push($vaidator_error, $vaidator_error['name']='Name error');
            
            $surname = (isset($params->surname) && ctype_alpha($params->surname)) ? $params->surname : array_push($vaidator_error, $vaidator_error['surname']='Surname error');
            
            $email   = (!empty($params->email)) ? $params->email : null;
            
            $pasword = (!empty($params->pasword)) ? $params->pasword : null;

            $validator = Validation::createValidator();
            $validate_email = $validator->validate($email, [
                new Email()
            ]); 

            if($email && count($validate_email) == 0 && $pasword && $name && $surname && $pasword){
                $data = [
                    'status' => 'success',
                    'code'   => 200,
                    'message'=> 'El usuario se ha creado'
                ];    
            }else{
                $data = [
                    'status' => 'error',
                    'code'   => 200,
                    'message'=> 'Validation error',
                    'vaidator_error' => $vaidator_error
                ];
            }
        }else{
            $data = [
                'status' => 'error',
                'code'   => 200,
                'message'=> 'Envie los datos'
            ];
        }

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
