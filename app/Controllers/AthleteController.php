<?php namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use Exception;
use App\Models\AthleteModel;


class AthleteController extends ResourceController{
    use ResponseTrait;

    public function getAll(){
        try{
            $model = new AthleteModel();
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
            $model = new AthleteModel();
            $data = $model->getWhere(['id' => $id])->getResult();
            
            return $this->respond($data);
        } catch (Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    public function getAthleteSeach($value = null){
        try{
            $model = db_connect();
            $builder = $model->table('athlete a');
            $builder->join('person p', 'a.person_id = p.id');
            $builder->where('p.name =', $value);
            $builder->orWhere('p.cpf =', $value);
            $builder->orWhere('p.rg =', $value);
            $builder->orWhere('a.enrolment =', $value);
            $builder->select('a.enrolment, p.id, p.name');
            $query = $builder->get()->getResult();
            
            return $this->respond(count($query) > 0 ? $query[0] : []);
        } catch (Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    public function create(){
        $model = new AthleteModel();
        $data = $this->request->getJSON();
        
        try {

            $model = new AthleteModel();
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
        $model = new AthleteModel();
        $data = $this->request->getJSON();

        try {
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
        $model = new AthleteModel();
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