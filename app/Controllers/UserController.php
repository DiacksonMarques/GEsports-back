<?php namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use Exception;
use App\Models\UserModel;

use App\Libraries\JwtLibraryLibraries;

class UserController extends ResourceController{
    use ResponseTrait;
    private $jwtLib;

    public function __construct() {
        $this->jwtLib = new JwtLibraryLibraries();
	}

    public function getAll(){
        try{
            $model = new UserModel();
            $buider = $model->builder();
            $buider->select('id,name,username,email,password');
            $data = $model->findAll();
            
            return $this->respond($data);
        } catch (Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    public function getOne(){
        try{
            $bearer_token = $this->jwtLib->get_bearer_token();
            $model = new UserModel();
            $buider = $model->builder();
            $buider->select('name,username,email,password');
            $data = $model->getWhere(['token' => $bearer_token])->getResult();
            
            return $this->respond($data);
        } catch (Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    public function create(){
        $model = new UserModel();
        $data = $this->request->getJSON();
        $rules = [
            'name' => 'required|min_length[5]',
            'username' => 'required|min_length[5]|is_unique[user.username]',
            'email' => 'required|min_length[5]|is_unique[user.email]',
            'password' => 'required|min_length[8]',
        ];
       

        try {
            if (!$this->validate($rules)) {
                return $this->fail($this->validator->getErrors());
            }

            if($model->insert($data)){
                $response = [
                    'status'   => 201,
                    'error'    => null,
                    'messages' => [
                        'success' => 'Dados salvos'
                    ]
                ];
                return $this->respondCreated($response);
            } else {
                return $this->fail($model->errors());
            }
        } catch (Exception $e) {
           return $this->fail($e->getMessage());
        }

        

        return $this->fail($model->errors());
    }

    public function update($id = null){
        $model = new UserModel();
        $data = $this->request->getJSON();
        $rules = [
            'name' => 'required|min_length[5]',
            'username' => 'required|min_length[5]',
            'email' => 'required|min_length[5]',
            'password' => 'required|min_length[8]',
        ];

        try {
            if($model->update($id, $data)){
                $response = [
                    'status'   => 200,
                    'error'    => null,
                    'messages' => [
                        'success' => 'Dados atualizados'
                    ]
                ];
                return $this->respond($response);
            } else {
                return $this->fail($model->errors());
            }
        } catch (Exception $e) {
           return $this->fail($e->getMessage());
        }
    }


    //Login
    public function login(){
        $model = new UserModel();
        $dataJson = $this->request->getJSON();

        $username = $dataJson->username;
        $password = $dataJson->password;

       try {
            $data = $model->where(array('username' => $username, 'password' => $password))->first();
        } catch (Exception $e) {
           return $this->fail($e->getMessage());
        }

        if($data){
            try {
                if(!$this->jwtLib->is_jwt_valid($data['token'])){ 
                    $data['token'] = $this->jwtLib->generate_jwt(array('username'=> $username, 'password'=> $password));
                    $model->update($data['id'], $data);
                }
                
                $model = db_connect();
                $builder = $model->table('user u');
                $builder->join('roles r', 'u.roles_id = r.id');
                $builder->where('u.id ='.$data['id']);
                $builder->select('u.name,u.token,r.role');
                $query = $builder->get()->getResult();
                $data = $query[0];

                return $this->respond($data);
            } catch (Exception $e) {
               return $this->fail($e->getMessage());
            }
            
            return $this->respond($data);
        } else {
            return $this->failNotFound('No user found');
        } 
    }

    //Logout
    public function logout(){
        try {
            $model = new UserModel();
            $dataJson = $this->request->getJSON();
            $data = $model->getWhere(['id' => $dataJson->id])->getResult();

            $data['token']= null;

            $model->update($dataJson->id, $data);

            return $this->respond([
                'status'   => 200,
                'error'    => null,
                'messages' => [
                    'success' => 'Logut realizado!'
                ]
            ]);
        } catch (Exception $e) {
           return $this->fail($e->getMessage());
        }
        
    }

    //Checks if the token is valid
    public function checkToken(){
        try{
            $data = $this->request->getJSON();

            if(!$data->token){
                return $this->respond(['token' => false]);
            }

            $model = new UserModel();
            $buider = $model->builder();
            $arrayResponse = $model->getWhere(['token' => $data->token])->getResult();
            $response = $arrayResponse[0];
            if($this->jwtLib->is_jwt_valid($response->token)){
                return $this->respond(['token' => true]);
            }
            
            return $this->respond(['token' => false]);
        } catch (Exception $e) {
            return $this->fail($e->getMessage());
        }
    }
}