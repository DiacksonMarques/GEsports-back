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

    private function getTeams($idTeam = null) {
      $jsonObj  = $this->returnDb();
      foreach ($jsonObj->teams  as &$team) {
        if($team->id == $idTeam){ 
          return $team;
        }
      }

      return null;
    }

    public function createTeam() {
      try {
        $data = $this->request->getJSON();

        $jsonObj  = $this->returnDb();
        $teams = $jsonObj->teams;

        $newId = count($teams);
        $data->id = $newId;
        $data->enrollment = $newId.'2050';
        $jsonObj->teams[$newId] = $data;
        file_put_contents(ROOTPATH.'/app/Assets/Json/championship.json', json_encode($jsonObj));
        
        return $this->respond($jsonObj->teams[$newId]);
      } catch (Exception $e) {
        return $this->fail($e->getMessage());
      }
    }

    public function allTeams() {
      try {
        $jsonObj  = $this->returnDb();
        $teams = $jsonObj->teams;

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

        return $this->respond($teams);
      } catch (Exception $e) {
        return $this->fail($e->getMessage());
      }
    }

    public function getGruops($role = null) {
      try{
          $jsonObj = $this->returnDb();
          $response = [];

          $index = 0;
          foreach ($jsonObj->group  as &$group) {
            if($role == $group->naipe){ 
              $position = 0;

              $response[]['name'] = $group->name;
              $response[$index]['teams'] = [];
              
              foreach($group->classification as &$team){
                $teamS = $this->getTeams($team->teamId);
                $position++;
                $response[$index]['teams'][] = [
                  "id" => $teamS->id,
                  "name" => $teamS->name,
                  "position" => $position,
                  "points" => $team->points
                ];
              } 

              $index ++;
            }
          }

          return $this->respond($response);
      } catch (Exception $e) {
          return $this->fail($e->getMessage());
      }
    }

    
    /* public function getGruops($role = null) {
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
    } */
}