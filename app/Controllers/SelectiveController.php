<?php namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use Exception;

class SelectiveController extends ResourceController{
    use ResponseTrait;
  
    public function __construct() {}
  
    private function returnDb() {
        $contents = file_get_contents(ROOTPATH.'/app/Assets/Json/selective.json');
        return json_decode($contents);
    }
  
    private function saveSelective($jsonObj = null) {
      file_put_contents(ROOTPATH.'/app/Assets/Json/selective.json', json_encode($jsonObj));
    }

    public function createCandidate() {
        try {
          $data = $this->request->getJSON();
    
          $candidates  = $this->returnDb();

          $valueCheck = $this->checkCandidate($data);
          if($valueCheck){
            return $this->respond($valueCheck);
          }
          $newId = count($candidates);
          $data->id = $newId;
          $data->enrollment = '20240'.$newId;
          $data->namePix = "";
          $data->approvedPix = false;
          $data->approvedRegistration = false;
          $data->approvedSelective = false;
          $data->levelSelect = null;
          $data->result = null;

          $candidates[$newId] = $data;
          
          $this->saveSelective($candidates);
          
          return $this->respond($candidates[$newId]);
        } catch (Exception $e) {
          return $this->fail($e->getMessage());
        }
    }

    public function putPixName(){
      try{
        $data = $this->request->getJSON();

        $candidates  = $this->returnDb();

        $candidateIndex = array_search($data->enrollment, array_column($candidates, 'enrollment'));

        $candidates[$candidateIndex]->namePix = $data->name;

        $this->saveSelective($candidates);
          
        return $this->respond($candidates[$candidateIndex]);

      } catch (Exception $e) {
        return $this->fail($e->getMessage());
      }
    }

    public function getCandidate($data=null){
      $candidates  = $this->returnDb();
      
      $candidateIndex = array_search($data, array_column($candidates, 'enrollment'));

      $response = [
        "status" => 200,
        "value" => ["enrollment" => null]
      ];

      if($candidateIndex == false){
        
        return $this->respond($response);
      }
      
      $response['value'] = $candidates[$candidateIndex];

      return $this->respond($response);
    }

    private function checkCandidate($candidate){
        $jsonObj  = $this->returnDb();

        foreach($jsonObj as &$candidateSave){
            if($candidate->cpf == $candidateSave->cpf) return $candidateSave;
        }

        return null;
    }
}