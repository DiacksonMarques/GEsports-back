<?php namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use Exception;
use App\Models\SchoolModel;
use App\Models\MessagesModel;


class SchoolController extends ResourceController{
    use ResponseTrait;

    public function getAll(){
        try{
            $model = new SchoolModel();
            $data = $model->findAll();
            
            if($data){
                return $this->respond($data);
            }

            return $this->failNotFound('Nenhum dado encontrado com id '.$id);
        } catch (Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    public function getOne($id = null){
        try{
            $model = new SchoolModel();
            $data = $model->getWhere(['id' => $id])->getResult();
            
            return $this->respond($data);
        } catch (Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    public function create(){
        try {
            $model = new SchoolModel();
            $messageModel = new MessagesModel();
            $model->setValidationMessages($messageModel->fieldValidationMessageSchool());
            $data = $this->request->getJSON();     

            $responseData = $model->insert($data);

            if($responseData){
                $data = $model->getWhere(['id' => $responseData])->getResult();
                $response = [
                    'status'   => 201,
                    'value'    =>  $data[0],
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
        $model = new SchoolModel();
        $data = $this->request->getJSON();
        $rules = [
            'name' => 'required|min_length[5]',
        ];

        try {
            if (!$this->validate($rules)) {
                return $this->fail($this->validator->getErrors());
            }

            $responseData = $model->update($id, $data);

            if($responseData){
                $data = $model->getWhere(['id' => $id])->getResult();

                $response = [
                    'status'   => 200,
                    'value'    => $data[0],
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

    public function delete($id = null){
        $model = new SchoolModel();
        $data = $model->find($id);
        
        

        try{
            if($data){
                $model->delete($id);
                $response = [
                    'status'   => 200,
                    'error'    => null,
                    'messages' => [
                        'success' => 'Dados removidos'
                    ]
                ];
                return $this->respondDeleted($response);
            }
            
            return $this->failNotFound('Nenhum dado encontrado com id '.$id);  
        } catch (Exception $e) {
            return $this->fail($e->getMessage());
        }
              
    }
}