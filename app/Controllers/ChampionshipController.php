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

  private function saveChampionship($jsonObj = null) {
    file_put_contents(ROOTPATH.'/app/Assets/Json/championship.json', json_encode($jsonObj));
  }

  public function createTeam() {
    try {
      $data = $this->request->getJSON();

      $teams  = $this->returnDb();

      $newId = count($teams);
      $data->id = $newId;
      $data->enrollment = '202603'.$newId;
      $teams[$newId] = $data;

      $this->saveChampionship($teams);
      
      return $this->respond($teams[$newId]);
    } catch (Exception $e) {
      return $this->fail($e->getMessage());
    }
  }

  public function editTeam(){
    try{
      $data = $this->request->getJSON();
      $teams  = $this->returnDb();

      $teamIndex = array_search($data->enrollment, array_column($teams, 'enrollment'));

      if($teamIndex == false){
        return $this->respond("ERROR enrollment Não encontrado");
      }

      $teams[$teamIndex] = $data;

      
      $this->saveChampionship($teams);
        
      return $this->respond($teams[$teamIndex]);

    } catch (Exception $e) {
      return $this->fail($e->getMessage());
    }
  }

  public function deleteTeam($data=null){
    try{
      $teams  = $this->returnDb();

      $teamIndex = array_search($data, array_column($teams, 'enrollment'));

      if($teamIndex == false){
        return $this->respond("ERROR enrollment Não encontrado");
      }

      array_splice($teams, $teamIndex, 1);
      
      $this->saveChampionship($teams);

      $response = [
        'status'   => 200,
        'value'    => true
      ];
        
      return $this->respond($response);

    } catch (Exception $e) {
      return $this->fail($e->getMessage());
    }
  }

  public function allTeams() {
    try {
      $teams  = $this->returnDb();

      array_shift($teams);

      $response = [
        'status'   => 200,
        'value'    => null
      ];


      foreach ($teams  as &$team) {
        switch ($team->naipe) {
          case 'MASCULINO':
            $team->naipe = 'Masc';
            break;

          case 'FEMININO':
            $team->naipe = 'Femi';
            break;
          
          case 'AMB':
            $team->naipe = 'Masc|Femi';
            break;
              
          default:
            $team->naipe = '??';
            break;
        }
      }

      $response['value'] = $teams;

      return $this->respond($response);
    } catch (Exception $e) {
      return $this->fail($e->getMessage());
    }
  }

  public function getTeam($data=null){
    $teams  = $this->returnDb();
    
    $teamIndex = array_search($data, array_column($teams, 'enrollment'));

    $response = [
      "status" => 200,
      "value" => ["enrollment" => null]
    ];

    if($teamIndex == false){
      return $this->respond($response);
    }
    
    $response['value'] = $teams[$teamIndex];

    return $this->respond($response);
  }

}