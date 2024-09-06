<?php namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use Exception;
use App\Models\PersonModel;
use App\Models\ResponsibleModel;
use App\Models\AthleteModel;
use App\Models\UserModel;
use App\Models\MessagesModel;


class EnrolmentController extends ResourceController{
    use ResponseTrait;

    public function getEnrollment($enrollment=null){
        try{
            $enrollmentId = explode(".", $enrollment);

            $model = db_connect();
            $builder = $model->table('person p');
            $builder->join('athlete a', 'a.person_id = p.id');
            $builder->join('category c', 'a.category_id = c.id', 'left');
            $builder->where('p.id ='.$enrollmentId[1]);
            $builder->select('p.name, p.gender, a.enrolment, c.name as category, c.id as categoryId');
            $query = $builder->get()->getResult();
            $data = $query;

            $response = [
                'status'   => 200,
                'value'    => null
            ];

            if(count($data) > 0){
                $response['value'] = $data[0];
            } else {
                $modelPerson = new PersonModel();
                $dataPerson = $modelPerson->getWhere(['id' => $enrollmentId[1]])->getResult();
                if(count($dataPerson) > 0){
                    $response['value'] = $dataPerson[0];
                } 
            }
            
            
            return $this->respond($response);
        } catch (Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    public function getEnrollmentAthelete($enrollment=null){
        try{
            $enrollmentId = explode(".", $enrollment);

            $model = db_connect();
            $builder = $model->table('person p');
            $builder->select('*');
            $builder->join('athlete a', 'a.person_id = p.id');
            $builder->where('p.id ='.$enrollmentId[1]);
            $builder->select('a.category_id as category, p.id as id');
            $query = $builder->get()->getResult();
            $data = $query;

            $response = [
                'status'   => 200,
                'value'    => null
            ];

            if(count($data) > 0){
                $response['value'] = $data[0];
            } else {
                $modelPerson = new PersonModel();
                $dataPerson = $modelPerson->getWhere(['id' => $enrollmentId[1]])->getResult();
                if(count($dataPerson) > 0){
                    $response['value'] = $dataPerson[0];
                } 
            }
            
            
            return $this->respond($response);
        } catch (Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    
    public function createEnrollment() {
        
        try{
            $messageModel = new MessagesModel();
            $data = $this->request->getJSON();
            $responseResponsible = null;

            if($data->responsible != null){
                $modelResponsible = new ResponsibleModel();
                $modelResponsible->setValidationMessages($messageModel->fieldValidationMessagePerson());

                $responseResponsible = $modelResponsible->insert($data->responsible);

                if($responseResponsible){
                    $query = $modelResponsible->getWhere(['id' => $responseResponsible])->getResult();
                    $dataResponsible = $query[0];
                } else {
                    return $this->fail($modelResponsible->errors());
                }
            }

            $modelPerson = new PersonModel();
            $modelPerson->setValidationMessages($messageModel->fieldValidationMessagePerson());

            if($responseResponsible != null){
                $data->responsible_id = $responseResponsible;
            }

            $responsePerson = $modelPerson->insert($data);
           
            if($responsePerson){
                $query = $modelPerson->getWhere(['id' => $responsePerson])->getResult();
                $dataPeson = $query[0];
            } else {
                return $this->fail($modelPerson->errors());
            } 

            $modelAthlete = new AthleteModel();
            $modelAthlete->setValidationMessages($messageModel->fieldValidationMessageAthlete());

            $enrolment = "A".date("Y")."".date("m")."".$responsePerson;

            $dataAthlete = [
                'enrolment' => $enrolment,
                'person_id' => $responsePerson,
                'category_id' => $data->category,
            ];

            $responseAthlete = $modelAthlete->insert($dataAthlete);

            if($responseAthlete){
                $query = $modelAthlete->getWhere(['id' => $responseAthlete])->getResult();
                $dataAthlete = $query[0];
            } else {
                return $this->fail($modelAthlete->errors());
            }

            $nameExplode = explode(" ", $dataPeson->name);

            $modelUser = new UserModel();

            $dataUser = [
                'name' => $nameExplode[0]." ".$nameExplode[1],
                'username' => $dataAthlete->enrolment,
                'password' => 'Ace123456',
                'person_id' => $responsePerson,
                'roles_id' => 2
            ];

            $responseUser = $modelUser->insert($dataUser);

            if(!$responseUser){
                return $this->fail($modelAthlete->errors());
            }


            return $this->respondCreated([
                'status'   => 200,
                'value'    => 'Sucesso'
            ]);
        } catch (Exception $e) {
           return $this->fail($e->getMessage());
        }
    }

    public function updateEnrollment($id = null) {
        
        try{
            $messageModel = new MessagesModel();
            $data = $this->request->getJSON();
            $responseResponsible = null;

            if($data->responsible != null){
                $modelResponsible = new ResponsibleModel();
                $modelResponsible->setValidationMessages($messageModel->fieldValidationMessagePerson());

                $responseResponsible = $modelResponsible->insert($data->responsible);

                if($responseResponsible){
                    $query = $modelResponsible->getWhere(['id' => $responseResponsible])->getResult();
                    $dataResponsible = $query[0];
                } else {
                    return $this->fail($modelResponsible->errors());
                }
            }

            $modelPerson = new PersonModel();
            $modelPerson->setValidationMessages($messageModel->fieldValidationMessagePerson());

            if($responseResponsible != null){
                $data->responsible_id = $responseResponsible;
            }

            $responsePerson = $modelPerson->update($id, $data);
            if($responsePerson){
                $query = $modelPerson->getWhere(['id' => $id])->getResult();
                $dataPeson = $query[0];
            } else {
                return $this->fail($modelPerson->errors());
            } 

            $modelAthlete = new AthleteModel();
            $modelAthlete->setValidationMessages($messageModel->fieldValidationMessageAthlete());

            $queryAthlete = $modelAthlete->getWhere(['person_id' => $id])->getResult();
            $dataAthlete = $queryAthlete[0];

            $nameExplode = explode(" ", $dataPeson->name);

            $modelUser = new UserModel();

            $dataUser = [
                'name' => $nameExplode[0]." ".$nameExplode[1],
                'username' => $dataAthlete->enrolment,
                'password' => 'Ace123456',
                'person_id' => $id,
                'roles_id' => 2
            ];

            $responseUser = $modelUser->insert($dataUser);

            if(!$responseUser){
                return $this->fail($modelAthlete->errors());
            }


            return $this->respondCreated([
                'status'   => 200,
                'value'    => 'Sucesso'
            ]);
        } catch (Exception $e) {
           return $this->fail($e->getMessage());
        }
    }
}