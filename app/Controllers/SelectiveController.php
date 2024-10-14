<?php namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use \DateTime; 
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

    public function editCandidate() {
      try {
        $data = $this->request->getJSON();

        $candidates  = $this->returnDb();

        $candidateIndex = array_search($data->enrollment, array_column($candidates, 'enrollment'));

        $candidates[$candidateIndex] = $data;

        $this->saveSelective($candidates);
          
        return $this->respond($candidates[$candidateIndex]);
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

    public function putDeferCanditate(){
      try{
        $data = $this->request->getJSON();

        $candidates  = $this->returnDb();

        $candidateIndex = array_search($data->enrollment, array_column($candidates, 'enrollment'));

        $candidates[$candidateIndex]->approvedPix = true;

        $this->saveSelective($candidates);
          
        return $this->respond($candidates[$candidateIndex]);

      } catch (Exception $e) {
        return $this->fail($e->getMessage());
      }
    }

    public function putConfirmPresenceCanditate(){
      try{
        $data = $this->request->getJSON();

        $candidates  = $this->returnDb();

        $candidateIndex = array_search($data->enrollment, array_column($candidates, 'enrollment'));

        $candidates[$candidateIndex]->approvedRegistration = true;

        $this->saveSelective($candidates);
          
        return $this->respond($candidates[$candidateIndex]);

      } catch (Exception $e) {
        return $this->fail($e->getMessage());
      }
    }

    public function putResultCanditate(){
      try{
        $data = $this->request->getJSON();

        $candidates  = $this->returnDb();

        $candidateIndex = array_search($data->enrollment, array_column($candidates, 'enrollment'));

        $candidates[$candidateIndex]->levelSelect = $data->level;

        if($candidates[$candidateIndex]->result == null){
          $candidates[$candidateIndex]->result = [];
        }

        $candidateResultIndex = array_search($data->result->appraiser, array_column($candidates[$candidateIndex]->result, 'appraiser'));
        
        if(gettype($candidateResultIndex) == "integer"){
          $candidates[$candidateIndex]->result[$candidateResultIndex] = $data->result;
        }else if(gettype($candidateResultIndex) == "boolean") {
          $candidates[$candidateIndex]->result[] =  $data->result;
        }

        
        $this->saveSelective($candidates);
          
        return $this->respond($candidates[$candidateIndex]);

      } catch (Exception $e) {
        return $this->fail($e->getMessage());
      }
    }

    public function getCandidate($data=null){
      $candidates  = $this->returnDb();
      
      $candidateIndex = array_search($data, array_column($candidates, 'enrollment'));

      if($candidateIndex == false){
        $candidateIndex = array_search($data, array_column($candidates, 'cpf'));
      }
      
      

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

    public function getAllCandidate(){
      $candidates  = $this->returnDb();

      array_shift($candidates);

      return $this->respond($candidates);
    }

    public function getCandidateNotDefer(){
      $candidates  = $this->returnDb();
      $response = [];

      array_shift($candidates);

      foreach($candidates as &$candidate){
        if($candidate->namePix != "" && $candidate->approvedPix == false){
          $response[] = $candidate;
        }
    }

      return $this->respond($response);
    }

    public function getCandidateDefer(){
      $candidates  = $this->returnDb();
      $response = [];

      array_shift($candidates);

      foreach($candidates as &$candidate){
        if($candidate->namePix != "" && $candidate->approvedPix == true && $candidate->approvedRegistration == false){
          $response[] = $candidate;
        }
    }

      return $this->respond($response);
    }

    public function getCandidateForEvaluation($hour=null, $gender=null){
      $candidates  = $this->returnDb();
      $response = [];

      array_shift($candidates);

      foreach($candidates as &$candidate){
        if($candidate->approvedRegistration == true){
          $yearToday = date('Y');
          $birthYear = new DateTime($candidate->birthDate);

          $age = $yearToday - $birthYear->format('Y');
          $hourSelective = '0';

          if($age > 18){
            $hourSelective = '20';
          } else if($age > 15 && $age <= 18 ){
            $hourSelective ='19';
          } else if($age <= 15 ){
            $hourSelective = '18';
          } 


          if($hourSelective == $hour && $candidate->gender == $gender){
            $response[] = $candidate;
          } 
        }
    }

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