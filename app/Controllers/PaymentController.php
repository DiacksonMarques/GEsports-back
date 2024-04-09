<?php namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use Exception;
use App\Models\PaymentModel;

use App\Libraries\JwtLibraryLibraries;

class PaymentController extends ResourceController{
    use ResponseTrait;
    private $jwtLib;

    public function __construct() {
        $this->jwtLib = new JwtLibraryLibraries();
	}

    public function getAll(){
        try{
            $model = new PaymentModel();
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
            $model = new PaymentModel();
            $data = $model->getWhere(['id' => $id])->getResult();
            
            return $this->respond($data);
        } catch (Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    public function getPaymentsUser(){
        try{
           $bearer_token = $this->jwtLib->get_bearer_token();

            $model = db_connect();
            $builder = $model->table('user u');
            $builder->join('person p', 'u.person_id = p.id');
            $builder->join('athlete a', 'a.person_id = p.id');
            $builder->select('a.id, u.name, u.person_id');
            $builder->where("u.token = '".$bearer_token."'");
            $query = $builder->get()->getResult();
            $dataAthlete = $query[0];

            $builderPayment = $model->table('payment py');
            $builderPayment->join('form-payment fp', 'py.formPayment_id = fp.id');
            $builderPayment->join('monthly-fee mf', 'py.monthlyFee_id = mf.id');
            $builderPayment->join('monthly-payment mp', 'py.monthlyPayment_id = mp.id', 'left');
            $builderPayment->select('py.id, py.paymentDate, fp.description as formPayment, mf.value as valuePayment, mp.maturity');
            $builderPayment->where("py.athlete_id = '".$dataAthlete->id."'");
            $queryPayment = $builderPayment->get()->getResult();
            
            return $this->respond($queryPayment);
        } catch (Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    public function create(){
        $model = new PaymentModel();
        $data = $this->request->getJSON();
        
        try {

            $model = new PaymentModel();
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
        $model = new PaymentModel();
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
        $model = new PaymentModel();
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