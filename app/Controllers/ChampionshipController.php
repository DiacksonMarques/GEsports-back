<?php namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use Exception;

class ChampionshipController extends ResourceController{
    use ResponseTrait;

    public function __construct() {}

    private function returnDb() {
        $contents = file_get_contents(ROOTPATH.'/app/Assets/Json/championship.json');
        return json_decode($contents);
    }
    public function getGruops($role = null) {
      try{
          $response = $this->returnDb();

          foreach ($response  as &$group) {
              $index = 0;
              foreach($group->teams as &$team){
                  $group->teams[$index] = $this->dbJsson->teams[$team];
                  $index++;
              }
          }

          return $this->respond($response);
      } catch (Exception $e) {
          return $this->fail($e->getMessage());
      }
    }

    public function createTeam() {
      try {
        $contents = file_get_contents(ROOTPATH.'/app/Assets/Json/championship.json');
        $data = $this->request->getJSON();

        $jsonObj  = json_decode($contents);
        $teams = $jsonObj->teams;

        $newId = count($teams);
        $data->id = $newId;
        $data->enrollment = $newId.'2050';
        $jsonObj->teams[$newId] = $data;
        file_put_contents(ROOTPATH.'/app/Assets/Json/championship.json', json_encode($jsonObj));
        
        return $this->respond($jsonObj->teams[$newId]);
      } catch (\Throwable $th) {
        return $this->fail($e->getMessage());
      }
    }

  /*if($method === 'DELETE'){
    if($json[$path[0]]){
      if($param1==""){
        echo 'error';
      }else{
        $encontrado = findById($json[$path[0]], $param1);
        if($encontrado>=0){
          echo json_encode($json[$path[0]][$encontrado]);
          unset($json[$path[0]][$encontrado]);
          file_put_contents('db.json', json_encode($json));
        }else{
          echo 'ERROR.';
          exit;
        }
      }
    }else{
      echo 'error.';
    }
  }

  if($method === 'PUT'){
    if($json[$path[0]]){
      if($param1==""){
        echo 'error';
      }else{
        $encontrado = findById($json[$path[0]], $param1);
        if($encontrado>=0){
          $jsonBody = json_decode($body, true);
          $jsonBody['id'] = $param1;
          $json[$path[0]][$encontrado] = $jsonBody;
          echo json_encode($json[$path[0]][$encontrado]);
          file_put_contents('db.json', json_encode($json));
        }else{
          echo 'ERROR.';
          exit;
        }
      }
    }else{
      echo 'error.';
    }
  } */
}