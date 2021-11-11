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

            if(isset($params->name) && ctype_alpha($params->name)){
                $name = $params->name;
            }else{
                $vaidator_error['name']='Name error';    
            }

            if(isset($params->surname) && ctype_alpha($params->surname)){
                $surname = $params->surname;
            }else{
                $vaidator_error['surname']='Surname error';    
            }

            if(isset($params->email)){
                $validator = Validation::createValidator();
                $validate_email = $validator->validate($params->email, [
                    new Email()
                ]);

                if($params->email && count($validate_email) == 0){
                    $email = $params->email;
                }else{
                    $vaidator_error['email']='email error';
                }
            }else{
                $vaidator_error['email']='write email';
            }
            
            if(isset($params->password)){
                // Given password
                $password = $params->password;
    
                // Validate password strength
                $uppercase = preg_match('@[A-Z]@', $password);
                $lowercase = preg_match('@[a-z]@', $password);
                $number    = preg_match('@[0-9]@', $password);
                $specialChars = preg_match('@[^\w]@', $password);

                if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
                    $vaidator_error['password']='Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.';
                }

            }else{
                $vaidator_error['password']='write password';
            }

            if(count($vaidator_error) == 0){
                // Si la validacion es correcta, crear el objeto del usuario
                $user = new User();
                $user->setName($name);
                $user->setSurname($surname);
                $user->setEmail($email);
                $user->setRole('ROLE_USER');
                $user->setCreatedAt(new \Datetime('now'));

                // Cifrar la contrasena
                $pwd = hash('sha256', $password);
                $user->setPassword($pwd);

                // Comprobar si el usuario existe
                $doctrine = $this->getDoctrine();
                $em = $doctrine->getManager();

                $user_repo = $doctrine->getRepository(User::class);
                $isset_user = $user_repo->findBy(array(
                    'email' => $email
                ));

                if(count($isset_user) == 0){
                    // Si no existe guardarlo en la base de datos
                    $em->persist($user);
                    $em->flush();

                    $data = [
                        'status' => 'success',
                        'code'   => 200,
                        'message'=> 'El usuario se ha creado',
                        'user'   => $user
                    ];    
                }else{
                    $data = [
                        'status' => 'error',
                        'code'   => 200,
                        'message'=> 'El usuario existe'
                    ];
                }

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



        
        // Hacer respuesta en json
        // return $this->resjson($data);
        // return $this->json($data);
        return new JsonResponse($data);
    }

    public function login(Request $request){
        // Recibir lod datos por post
        $json = $request->get('json', null);

        // Decodificar el json
        $params = json_decode($json);

        if($json != null){
            // Comprobar y validar datos
            $email    = (isset($params->email) && $params->email) ? $params->email : null;
            $password = (isset($params->password) && $params->password) ? $params->password : null;
            $gettoken = (isset($params->gettoken) && $params->gettoken) ? $params->gettoken : null;

            // valido q el email sea de tipo email
            $validator       = Validation::createValidator();
            $validator_email = $validator->validate($email, [
                new Email()
            ]);

            if($email && $password && count($validator_email) == 0 ){
                // Cifrar la contrasena
                $pwd = hash('sha256', $password);

                // Si todo eso es valido llamaremos a un servicio para identificar al usuario y q nos devuelva un token o un objeto

                // Crear servicio jwt

                // Si nos devuelve bien los datos, daremos respuesta
                $data = [
                    'status' => 'success',
                    'code'   => 200,
                    'message'=> 'validation correct.'
                ];

            }else{
                $data = [
                    'status' => 'error',
                    'code'   => 200,
                    'message'=> 'Some data is missing.'
                ];
            }


        }else{
            $data = [
                'status' => 'success',
                'code'   => 200,
                'message'=> 'All data is missing.'
            ];

        }


        return new JsonResponse($data);

    }
}
