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
                        'message'=> 'The user is created',
                        'user'   => $user
                    ];    
                }else{
                    $data = [
                        'status' => 'error',
                        'code'   => 200,
                        'message'=> 'This user already exists'
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
                'message'=> 'Please, send the data'
            ];
        }
        
        // Hacer respuesta en json
        // return $this->resjson($data);
        // return $this->json($data);
        return new JsonResponse($data);
    }

    public function login(Request $request, JwtAuth $jwt_auth){
        // Recibir lod datos por post
        $json = $request->get('json', null);

        // Decodificar el json
        $params = json_decode($json);

        if($json){
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

                $data = $jwt_auth->signup($email, $pwd, $gettoken);

            }else{
                $data = [
                    'status' => 'error',
                    'code'   => 200,
                    'message'=> 'Validation error'    
                ];
            }
        }else{
            $data = [
                'status' => 'error',
                'code'   => 200,
                'message'=> 'All data is missing.'
            ];

        }

        return new JsonResponse($data);
    }

    public function edit(Request $request, JwtAuth $jwt_auth){
        // recoger la cabecera de autenticacion
        $token = $request->headers->get('Authorization');

        // crear un metodo para comprobar si el token es correcto 
        $authCheck = $jwt_auth->checkToken($token);
        
        // si el token es correcto
        if($authCheck){

            // conseguir el entity manager
            $em = $this->getDoctrine()->getManager();

            // conseguir los datos del usuario identificado
            $identity = $jwt_auth->checkToken($token, true);

            // comprobar y validar los datos
            $json = $request->get('json', null);
            $params = json_decode($json);

            $vaidator_error = array();  
            if($params){
                
                //*--valido los datos------ini
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
    
                if(isset($params->email) && $params->email){
                    $validator = Validation::createValidator();
                    $validate_email = $validator->validate($params->email, [
                        new Email()
                    ]);
    
                    if(count($validate_email) == 0){
                        $email = $params->email;
                    }else{
                        $vaidator_error['email']='Email error';
                    }
                }else{
                    $vaidator_error['email']='write email';
                }
                //*--valido los datos------end
                
                // si pasa la validacion
                if(count($vaidator_error) == 0){

                    // compruebo q el si va a cambiar el email no exista otro usuario con el mismo email
                    if($identity->email != $email){
                        
                        $user_repo = $this->getDoctrine()->getRepository(User::class);
                        $user = $user_repo->findOneBy([
                            'email' => $email
                        ]);

                        if($user){
                            $data = [
                                'status' => 'error',
                                'message'=> 'There is another user within the same email'
                            ];
                        }else{

                            // actualizo los datos del usuario y devuelvo el usuario actualizado y su nuevo token
                            $user_repo = $this->getDoctrine()->getRepository(User::class);
                            $user = $user_repo->findOneBy([
                                'id' => $identity->sub
                            ]);

                            $user->setName($name);
                            $user->setSurname($surname);
                            $user->setEmail($email);

                            $em->persist($user);
                            $em->flush();

                            $data = [
                                'status' => 'success',
                                'message'=> 'Usuario actualizado',
                                'user' => $user
                            ];
                        }

                    }else{
                        // actualizo los datos del usuario y devuelvo el usuario actualizado y su nuevo token
                        $user_repo = $this->getDoctrine()->getRepository(User::class);
                        $user = $user_repo->findOneBy([
                            'id' => $identity->sub
                        ]);

                        $user->setName($name);
                        $user->setSurname($surname);
                        $user->setEmail($email);

                        $em->persist($user);
                        $em->flush();

                        $data = [
                            'status' => 'success',
                            'message'=> 'Usuario actualizado',
                            'user' => $user
                        ];
                    }
                }else{
                    $data = [
                        'status' => 'error',
                        'message'=> 'Error de validacion',
                        'vaidator_error' => $vaidator_error
                    ];
                }
            }else{
                $data = [
                    'status' => 'error',
                    'message'=> 'Envie los datos a actualizar'
                ];    
            }


        }else{
            $data = [
                'status' => 'error',
                'message'=> 'Token incorrect'
            ];
        }


        // return $this->resjson($data);
        return new JsonResponse($data);


        
    }
}
