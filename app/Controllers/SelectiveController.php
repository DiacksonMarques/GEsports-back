<?php namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use \DateTime; 
use App\Models\EfiPayModel;
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
          $yearsAccepted = array(2002,2003,2004,2005,2006,2007,2008,2009,2010,2011,2012,2013,2014);

          $valueCheck = $this->checkCandidate($data);
          if($valueCheck){
            return $this->respond($valueCheck);
          }

          $birthDate = date('Y' ,strtotime($data->birthDate));
          if(!in_array($birthDate, $yearsAccepted)){
            return $this->fail("Idade fora da faixa permitida");
          }

          $modelEdi = new EfiPayModel();
          if($data->gender == "MASCULINO"){
            $yearsNotPage = array(2012,2013,2014);
            $yearsPageDay02 = array(2009, 2010, 2011);
            $yearsPageDay04 = array(2002,2003, 2004, 2005, 2006, 2007, 2008);

            if(in_array($birthDate, $yearsPageDay02)){
              $responseEfi = $modelEdi->createPixMaturity("2025-09-02", $data->cpf, $data->name, "10.00");
            } else if(in_array($birthDate, $yearsPageDay04)) {
              $responseEfi = $modelEdi->createPixMaturity("2025-09-04", $data->cpf, $data->name, "10.00");
            } else if(in_array($birthDate, $yearsNotPage)) {
              $responseEfi = ["status" => 201, "body" => ["txid" => "NOTPAGE"]];
            }
          } else if($data->gender == "FEMININO"){
            $responseEfi = $modelEdi->createPixMaturity("2025-02-26", $data->cpf, $data->name, "10.00");
          }

          if($responseEfi['status'] != 201){
            return $this->fail($responseEfi);
          }

          $newId = count($candidates);
          $data->id = $newId;
          $data->enrollment = '20250'.$newId;
          $data->txid = $responseEfi['body']['txid'];
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

      if($candidates[$candidateIndex]->txid == "NOTPAGE"){
        $response['value']->pixStatus = "CONCLUIDA";
      } else {
        $modelEdi = new EfiPayModel();
        $responsePix = $modelEdi->searchPix($response['value']->txid);

        if($responsePix['status'] != 201){
          return $this->fail($responsePix);
        }

        $qrCodPix = $modelEdi->generatePixQrCode($responsePix['body']['loc']['id']);

        if($qrCodPix['status'] != 201){
          return $this->fail($qrCodPix);
        }
        
        $response['value']->pixQrCode = $qrCodPix['body']['imagemQrcode'];
        $response['value']->pixCopyPaste = $responsePix['body']['pixCopiaECola'];
        $response['value']->pixName = $responsePix['body']['recebedor']['nome'];
        $response['value']->pixValue = $responsePix['body']['valor']['original'];
        $response['value']->pixMaturity = $responsePix['body']['calendario']['dataDeVencimento'];
        $response['value']->pixStatus = $responsePix['body']['status'];
        }

      return $this->respond($response);
    }

    public function getAllCandidate(){
      $candidates  = $this->returnDb();

      array_shift($candidates);

      return $this->respond($candidates);
    }

    public function getCandidatePayment(){
      $candidates  = $this->returnDb();
      $response = [];
      $yearsNotPage = array(2012,2013,2014);

      array_shift($candidates);

      foreach($candidates as $key=>$candidate){
        $birthDateYear = date('Y' ,strtotime($candidate->birthDate));
        if(!property_exists($candidate, "approvedRegistration") || 
          (property_exists($candidate, "approvedRegistration") && !$candidate->approvedRegistration)
        ){

          if(property_exists($candidate, "pixStatus") && $candidate->pixStatus == "CONCLUIDA"){
            $response[] = $candidate;
            
          } else if(!property_exists($candidate, "pixStatus") || 
            (property_exists($candidate, "pixStatus") && $candidate->pixStatus != "CONCLUIDA")
          ){
            if(in_array($birthDateYear, $yearsNotPage)){
              $response[] = $candidate;
            } else {
              $modelEdi = new EfiPayModel();
              $responsePix = $modelEdi->searchPix($candidate->txid);
    
              if($responsePix['status'] != 201){
                return $this->fail($responsePix);
              }
              if($responsePix['body']['status'] == "CONCLUIDA"){
                $response[] = $candidate;
              }
            }
          }
        }
      }
      
      return $this->respond($response);
    }

    public function getCandidateForEvaluation($hour=null, $gender=null){
      $candidates  = $this->returnDb();
      $response = [];

      array_shift($candidates);

      foreach($candidates as &$candidate){
        if(property_exists($candidate, "approvedRegistration") && $candidate->approvedRegistration == true){
          $yearToday = date('Y');
          $birthYear = new DateTime($candidate->birthDate);

          $age = $yearToday - $birthYear->format('Y');
          $hourSelective = '0';


          if(in_array($birthYear->format('Y'), [2012, 2013, 2014])){
            $hourSelective = '17';
          } else if(in_array($birthYear->format('Y'), [2009, 2010, 2011])){
            $hourSelective = '18';
          } else if(in_array($birthYear->format('Y'), [2008, 2007, 2006])){
            $hourSelective = '19';
          } else if(in_array($birthYear->format('Y'), [2005, 2004, 2003, 2002])){
            $hourSelective = '20';
          }



          if($hourSelective == $hour && $candidate->gender == $gender){
            $response[] = $candidate;
          } 
        }
      }

      return $this->respond($response);
    }

    public function getCandidateForEvaluationPage($hour=null, $gender=null){
      $candidates  = $this->returnDb();
      $response = [];
      
      $modelEdi = new EfiPayModel();
      array_shift($candidates);

      foreach($candidates as &$candidate){
        $responsePix = $modelEdi->searchPix($candidate->txid);

        if($responsePix['status'] != 201){
          return $this->fail($responsePix);
        }
 
        if($responsePix['body']['status'] == "CONCLUIDA"){
          $yearToday = date('Y');
          $birthYear = new DateTime($candidate->birthDate);

          $age = $yearToday - $birthYear->format('Y');
          $hourSelective = '0';

          if(in_array($birthYear->format('Y'), ["2013","2012","2011","2010"])){
            $hourSelective = '17';
          } else if(in_array($birthYear->format('Y'), ["2009", "2008", "2007", "2006"])){
            $hourSelective = '18';
          } else if(in_array($birthYear->format('Y'), ["2005", "2004", "2003", "2002"])){
            $hourSelective = '19';
          } else if($age >= 18){
            $hourSelective = '19';
          } else {
            $hourSelective = '17';
          }

          if($hourSelective == $hour && $candidate->gender == $gender){
            $response[] = $candidate;
          } 
        }
      }

      return $this->respond($response);
    }

    public function createPix($cpf=null, $name=null){
      $modelEdi = new EfiPayModel();
      $responsePix = $modelEdi->createPixMaturity("2025-02-26", $cpf, $name, "10.00");

      if($responsePix['status'] != 201){
        return $this->fail($responsePix);
      }

      return $this->respond($responsePix);
    }

    public function searchPix($txid = null){
      $modelEdi = new EfiPayModel();
      $responsePix = $modelEdi->searchPix($txid);

      if($responsePix['status'] != 201){
        return $this->fail($responsePix);
      }

      return $this->respond($responsePix);
    }

    private function checkCandidate($candidate){
      $jsonObj  = $this->returnDb();

      foreach($jsonObj as &$candidateSave){
          if($candidate->cpf == $candidateSave->cpf) return $candidateSave;
      }

      return null;
    }
}