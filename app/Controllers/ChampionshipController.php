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
      $data->enrollment = '202501'.$newId;
      $teams[$newId] = $data;

      $this->saveChampionship($teams);
      
      return $this->respond($teams[$newId]);
    } catch (Exception $e) {
      return $this->fail($e->getMessage());
    }
  }

  public function allTeams() {
    try {
      $teams  = $this->returnDb();

      $response = [
        'status'   => 200,
        'value'    => null
      ];


      foreach ($teams  as &$team) {
        switch ($team->naipe) {
          case 'MAS':
            $team->naipe = 'Masc';
            break;

          case 'FEM':
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

}
/* {
  "id": 0,
  "groupId": 0,
  "teamHome": 28,
  "teamAway": 29,
  "setHome": 2,
  "setAway": 0,
  "pointHome": 3,
  "pointAway": 0,
  "sets": [
      {
          "teamOne": 25,
          "teamTwo": 20
      },
      {
          "teamOne": 25,
          "teamTwo": 15
      },
      {
          "teamOne": 0,
          "teamTwo": 0
      }
  ]
} */