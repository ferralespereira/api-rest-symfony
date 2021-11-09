<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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
}
