<?php
namespace App\Services;

use Firebase\JWT\JWT;
use App\Entity\User;

class JwtAuth{

    public $manager;
    public $key;

    public function __construct($manager){
        $this->manager = $manager;  
        $this->key = 'este_es_la_key_del_token_hjknxnlzqlmx56565@@%%njm';
    }
    
    public function signup($email, $pwd, $gettoken = null){
        
        // comprobar si el usuario existe
        $user = $this->manager->getRepository(User::class)->findOneBy([
            'email' => $email,
            'password' => $pwd
        ]);
        
        // si existe el usuario
        if($user){
            
            // creo el token
            $token = [
                'sub' => $user->getId(),
                'name' => $user->getName(),
                'surname' => $user->getSurname(),
                'email' => $user->getEmail(),
                'iat' => time(),
                'exp' => time() + (7 * 24 * 60 * 60)
            ];
            
            // devuelvo el token decodificado o codificado
            if($gettoken){
                $data = JWT::encode($token, $this->key, 'HS256');
            }else{
                $data = $token;
            }

        }else{
            $data = [
                'status' => 'error',
                'message'=> 'login incorrect'
            ];
        }

        return $data;
    }
    
    public function checkToken($jwt, $identity = false){
        $auth = false;
        
        try{        
            $jwt = str_replace('"', '', $jwt);
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
        }catch(\UnexpectedValueException $e){
            $auth = false;
        }catch(\DomainException $e){
            $auth = false;
        }
        
        if(isset($decoded) && isset($decoded->sub)){
            
            if($identity){
                $auth = $decoded;
            }else{
                $auth = true;
            }
            
        }else{
            $auth = false;
        }

        return $auth;
    }
}
