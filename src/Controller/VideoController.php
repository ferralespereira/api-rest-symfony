<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Email;
use Knp\Component\Pager\PaginatorInterface;

use App\Entity\User;
use App\Entity\Video;
use App\Services\JwtAuth;

class VideoController extends AbstractController
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
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/VideoController.php',
        ]);
    }

    public function create(Request $request, JwtAuth $jwt_auth){

        // recoger el token
        $token = $request->headers->get('Authorization');
        
        // comprobar si es correcto
        $authCheck = $jwt_auth->checkToken($token);

        // recoger datos por post
        if($authCheck){

            // comprobar y validar los datos
            $json = $request->get('json', null);
            $params = json_decode($json);

            if($params){
                // recoger datos del usuario identificado
                $identity = $jwt_auth->checkToken($token, true);
    
                // guardar el nuevo video en la db
                $user_id = ($identity->sub) ? $identity->sub : null ; 
                $title = (isset($params->title) && $params->title) ? $params->title : null ;          
                $description = (isset($params->description) && $params->description) ? $params->description : null ;       
                $url = (isset($params->url) && $params->url) ? $params->url : null ;            
                
                if($user_id && $title && $description && $url){

                    // conseguir el entity manager
                    $em = $this->getDoctrine()->getManager();
                    $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
                        'id' => $user_id
                    ]);

                    // crear y guardar el objeto
                    $video = new Video();
                    $video->setUser($user);
                    $video->setTitle($title);
                    $video->setDescription($description);
                    $video->setUrl($url);
                    $video->setStatus('normal');

                    $video->setCreatedAt(new \Datetime('now'));
                    $video->setUpdatedAt(new \Datetime('now'));

                    $em->persist($video);
                    $em->flush();

                    $data = [
                        'status' => 'success',
                        'code'   => 200,
                        'message'=> 'New video created',
                        'video'  => $video
                    ];

                }else{
                    $data = [
                        'status' => 'error',
                        'code'   => 200,
                        'message'=> 'At least one of these informations is missing: title, description, url'
                    ];    
                }
            }else{
                $data = [
                    'status' => 'error',
                    'code'   => 200,
                    'message'=> 'Send the data'
                ];
            }
            
        }else{
            $data = [
                'status' => 'error',
                'code'   => 200,
                'message'=> 'Token incorrect'
            ];
        }

        
        return $this->resjson($data);
        // return new JsonResponse($data);
    }

    public function videos(Request $request, JwtAuth $jwt_auth, PaginatorInterface $paginator){

        // recoger el token
        $token = $request->headers->get('Authorization');
        
        // comprobar si es correcto
        $authCheck = $jwt_auth->checkToken($token);

        // recoger datos por post
        if($authCheck){
            // recoger datos del usuario identificado
            $identity = $jwt_auth->checkToken($token, true);

            // conseguir el entity manager
            $em = $this->getDoctrine()->getManager();
            
            // configurar el bundle de paginacion
            $dql = "SELECT v FROM App\Entity\Video v WHERE v.user = {$identity->sub} ORDER BY v.id DESC";
            $query = $em->createQuery($dql);

            // recoger el prametro de la url
            $page = $request->query->getInt('page', 1);
            $items_per_page = 5;

            // invocar la paginacion
            $pagination = $paginator->paginate($query, $page, $items_per_page);
            $total = $pagination->getTotalItemCount();

            
            $data = [
                'status'         => 'success',
                'code'           => 200,
                'message'        => 'These are the videos',
                'total'          => $total,
                'page'           => $page,
                'items_per_page' => $items_per_page,
                'total_pages'    => ceil($total / $items_per_page),
                'videos'         => $pagination,
                'user_id'        => $identity->sub
            ];

        }else{
            $data = [
                'status' => 'error',
                'code'   => 200,
                'message'=> 'Token incorrect'
            ];
        }
    
        return $this->resjson($data);
    }
}
