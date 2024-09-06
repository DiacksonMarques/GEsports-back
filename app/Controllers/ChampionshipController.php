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

  private function getTeams($idTeam = null) {
    $jsonObj  = $this->returnDb();
    foreach ($jsonObj->teams  as &$team) {
      if($team->id == $idTeam){ 
        return $team;
      }
    }

    return null;
  }

  private function getGruop($idGruop = null) {
    $jsonObj  = $this->returnDb();
    foreach ($jsonObj->group  as &$group) {
      if($group->id == $idGruop){ 
        return $group;
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
          if($role == $group->naipe || $role == "AMB"){ 
            $position = 0;

            $response[]['name'] = $group->name;
            $response[$index]['teams'] = [];
            
            foreach($group->classification as &$team){
              $teamS = $this->getTeams($team->teamId);
              $position++;
              $response[$index]['teams'][] = [
                "id" => $teamS->id,
                "name" => $teamS->name,
                "position" => $position.'º',
                "points" => $team->points,
                "logo" => 'https://apia.gesport.com.br/public/assets/'.$team->teamId.'.png'
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

  public function getGruopDetails($role = null) {
    try{
        $jsonObj = $this->returnDb();
        $response = [];

        foreach ($jsonObj->group  as &$group) {
          if($role == $group->id){ 
            $position = 0;
            
            foreach($group->classification as &$team){
              $teamS = $this->getTeams($team->teamId);
              $position++;

              $response[] = [
                "position" => $position,
                "name" => $teamS->name,
                "logoTeam" =>'https://apia.gesport.com.br/public/assets/'.$team->teamId.'.png',
                "points" => $team->points,
                "setsWon" => $team->setsWon,
                "pointBalance" => $team->pointBalance,
              ];
  
            } 
          }
        }

        return $this->respond($response);
    } catch (Exception $e) {
        return $this->fail($e->getMessage());
    }
  }
  
  public function getGames($role = null) {
    try{
        $jsonObj = $this->returnDb();
        $response = [];

        foreach ($jsonObj->gamesHistoric  as &$gamesHistoric) {
          if($role == $gamesHistoric->naipe){ 
            foreach($gamesHistoric->games as &$game){
              $teamHome = $this->getTeams($game->teamHome);
              $teamAway = $this->getTeams($game->teamAway);

              $gruop = $this->getGruop($game->groupId);

              $response[] = [
                "order" => $game->id + 1,
                "gruopId" => $gruop->id,
                "gruop" => $gruop->name,
                "logoTeamHome" => 'https://apia.gesport.com.br/public/assets/'.$teamHome->id.'.png',
                "teamHomeId" => $teamHome->id,
                "teamHome" => $teamHome->name,
                "teamAwayId" => $teamAway->id,
                "teamAway" => $teamAway->name,
                "logoTeamAway" => 'https://apia.gesport.com.br/public/assets/'.$teamAway->id.'.png',
                "setHome" => $game->setHome,
                "setAway" => $game->setAway,
                "setsPlayed" => $game->sets
              ];
            }
          }
        }

        return $this->respond($response);
    } catch (Exception $e) {
        return $this->fail($e->getMessage());
    }
  }

  public function overallCalssification($role = null) {
    try{
      $jsonObj = $this->returnDb();
      $response = [];

      foreach ($jsonObj->classification  as &$classificationOverall) {
        if($role == $classificationOverall->naipe){ 
          $position = 1;
          foreach($classificationOverall->classification as &$classification){
            $team = $this->getTeams($classification->teamId);

            $response[] = [
              "position" => $position,
              "name" => $team->name,
              "logoTeam" =>'https://apia.gesport.com.br/public/assets/'.$team->id.'.png',
              "points" => $classification->points,
              "setsWon" => $classification->setsWon,
              "pointBalance" => $classification->pointBalance,
            ];

            $position++;
          }
        }
      }

      return $this->respond($response);
    } catch (Exception $e) {
        return $this->fail($e->getMessage());
    }
  }

  public function setGameGruop($role=null, $roleG=null) { 
    try{
      $data = $this->request->getJSON();
      $jsonObj = $this->returnDb();
      $response = [];

      foreach ($jsonObj->games->groupStatge  as &$game) {
        if($game->naipe == $role && $roleG == null){
          $newId = count($game->games);
          
          $data->id = $newId;

          $game->games[$newId] = $data;
        } else if($game->naipe == $roleG){
          $newId = count($game->games);
          
          $data->id = $newId;

          $game->games[$newId] = $data;
        }
      }

      foreach ($jsonObj->gamesHistoric  as &$game) {
        if($game->naipe == $role && $roleG == null){
          $game->games[$data->id] = $data;
        } else if($game->naipe == $roleG){
          $game->games[$data->id] = $data;
        }
      }

      $newJsonObj = $this->updateClassification($jsonObj, $data, $role);

      $this->saveChampionship($newJsonObj);

      return $this->respond($newJsonObj);
    } catch (Exception $e) {
        return $this->fail($e->getMessage());
    }
  }

  private function updateClassification($jsonObj = null,$data=null, $role=null){
    $response = [];

    $indexGruopSelected = array_search($data->groupId, array_column($jsonObj->group, 'id'));

    $indexTeamHome = array_search($data->teamHome, array_column($jsonObj->group[$indexGruopSelected]->classification, 'teamId'));
    $indexTeamAway = array_search($data->teamAway, array_column($jsonObj->group[$indexGruopSelected]->classification, 'teamId'));

    $someBalanceHome = 0;
    $someBalanceAwait = 0;
    $setsBalanceHome = 0;
    $setsBalanceAwait = 0;

    foreach($data->sets as &$set){
      $someBalanceHome = $someBalanceHome + $set->teamOne;
      $someBalanceAwait = $someBalanceAwait + $set->teamTwo;
    }

    $pointBalanceHome = $someBalanceHome - $someBalanceAwait;
    $pointBalanceAwait = $someBalanceAwait - $someBalanceHome;

    $setsBalanceHome = $data->setHome - $data->setAway;
    $setsBalanceAwait = $data->setAway - $data->setHome;

    $jsonObj->group[$indexGruopSelected]->classification[$indexTeamHome]->points= $jsonObj->group[$indexGruopSelected]->classification[$indexTeamHome]->points + $data->pointHome;
    $jsonObj->group[$indexGruopSelected]->classification[$indexTeamHome]->setsWon= $jsonObj->group[$indexGruopSelected]->classification[$indexTeamHome]->setsWon + $setsBalanceHome;
    $jsonObj->group[$indexGruopSelected]->classification[$indexTeamHome]->pointBalance= $jsonObj->group[$indexGruopSelected]->classification[$indexTeamHome]->pointBalance + $pointBalanceHome;

    $jsonObj->group[$indexGruopSelected]->classification[$indexTeamAway]->points= $jsonObj->group[$indexGruopSelected]->classification[$indexTeamAway]->points + $data->pointAway;
    $jsonObj->group[$indexGruopSelected]->classification[$indexTeamAway]->setsWon= $jsonObj->group[$indexGruopSelected]->classification[$indexTeamAway]->setsWon + $setsBalanceAwait;
    $jsonObj->group[$indexGruopSelected]->classification[$indexTeamAway]->pointBalance= $jsonObj->group[$indexGruopSelected]->classification[$indexTeamAway]->pointBalance + $pointBalanceAwait;

    usort($jsonObj->group[$indexGruopSelected]->classification, function ($a, $b) {
      if($a->points > $b->points){
        return -1;
      }
  
      if($a->points == $b->points && $a->setsWon > $b->setsWon){
        return -1;
      }
  
      if($a->points == $b->points && $a->setsWon == $b->setsWon && $a->pointBalance > $b->pointBalance){
        return -1;
      }

      if($a->points == $b->points && $a->setsWon == $b->setsWon && $a->pointBalance > $b->pointBalance){
        return 0;
      }
  
      return 1;
    });

    foreach ($jsonObj->classification  as &$classification) {
      if($classification->naipe == $role){
        $indexTeamHomeC = array_search($data->teamHome, array_column($classification->classification, 'teamId'));
        $indexTeamAwayC = array_search($data->teamAway, array_column($classification->classification, 'teamId'));

        $classification->classification[$indexTeamHomeC]->points= $classification->classification[$indexTeamHomeC]->points +$data->pointHome;
        $classification->classification[$indexTeamHomeC]->setsWon= $classification->classification[$indexTeamHomeC]->setsWon +$data->setHome;
        $classification->classification[$indexTeamHomeC]->pointBalance= $classification->classification[$indexTeamHomeC]->pointBalance +$pointBalanceHome;

        $classification->classification[$indexTeamAwayC]->points= $classification->classification[$indexTeamAwayC]->points +$data->pointAway;
        $classification->classification[$indexTeamAwayC]->setsWon= $classification->classification[$indexTeamAwayC]->setsWon +$data->setAway;
        $classification->classification[$indexTeamAwayC]->pointBalance= $classification->classification[$indexTeamAwayC]->pointBalance +$pointBalanceAwait;

        usort($classification->classification, function ($a, $b) {
          if($a->points > $b->points){
            return -1;
          }
      
          if($a->points == $b->points && $a->setsWon > $b->setsWon){
            return -1;
          }
      
          if($a->points == $b->points && $a->setsWon == $b->setsWon && $a->pointBalance > $b->pointBalance){
            return -1;
          }

          if($a->points == $b->points && $a->setsWon == $b->setsWon && $a->pointBalance > $b->pointBalance){
            return 0;
          }
      
          return 1;
        });
      }
    }

    

    return  $jsonObj;
  }

  public function getFinailsGames($role = null) {
    try{
        $jsonObj = $this->returnDb();
        $response = [];

        foreach ($jsonObj->gamesFinails  as &$gamesFinail) {
          if($role == $gamesFinail->naipe){ 
            foreach($gamesFinail->games as &$game){
              $teamHome = $this->getTeams($game->teamHome);
              $teamAway = $this->getTeams($game->teamAway);

              $response[] = [
                "title" => $game->title,
                "logoTeamHome" => 'https://apia.gesport.com.br/public/assets/'.$teamHome->id.'.png',
                "teamHomeId" => $teamHome->id,
                "teamHome" => $teamHome->acronym,
                "teamAwayId" => $teamAway->id,
                "teamAway" => $teamAway->acronym,
                "logoTeamAway" => 'https://apia.gesport.com.br/public/assets/'.$teamAway->id.'.png',
                "setHome" => $game->setHome,
                "setAway" => $game->setAway,
                "setsPlayed" => $game->sets
              ];
            }
          }
        }

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