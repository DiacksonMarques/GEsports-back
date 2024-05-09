<?php namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use Exception;
use App\Models\AthleteModel;

class RaffleController extends ResourceController{
    use ResponseTrait;

    public function __construct() {}

    private function returnDb() {
        $contents = file_get_contents(ROOTPATH.'/app/Assets/Json/raffle.json');
        return json_decode($contents);
    }

    private function raffleAthleteSeach($enrolment = null) {
        $raffles = $this->returnDb();
        $raffleAthlete = array_search($enrolment, array_column($raffles, 'idAthlete'));

        return $raffleAthlete;
    }

    private function saveRaffle($jsonObj = null) {
        file_put_contents(ROOTPATH.'/app/Assets/Json/raffle.json', json_encode($jsonObj));
    }

    private function getAthleteSeach($value = null){
      try{
          $model = db_connect();
          $builder = $model->table('athlete a');
          $builder->join('person p', 'a.person_id = p.id');
          $builder->join('category c', 'a.category_id = c.id');
          $builder->where('p.name =', $value);
          $builder->orWhere('p.cpf =', $value);
          $builder->orWhere('p.rg =', $value);
          $builder->orWhere('a.enrolment =', $value);
          $builder->select('a.enrolment, p.id, p.name, c.name as category');
          $query = $builder->get()->getResult();
          
          return count($query) > 0 ? $query[0] : (object)["enrolment" => null];
      } catch (Exception $e) {
          return [];
      }
  }

    public function getRaffle($enrolment = null) {
      try{
            $raffles = $this->returnDb();
            $raffleAthleteIndex = $this->raffleAthleteSeach($enrolment);

            $response = [];

            if($raffleAthleteIndex != false){
                $response = $raffles[$raffleAthleteIndex];
            }

            return $this->respond($response);
      } catch (Exception $e) {
          return $this->fail($e->getMessage());
      }
    }

    public function getAllRaffle() {
      try{
            $raffles = $this->returnDb();

            $response = [];

            foreach($raffles as &$raffle){
              $athlete = $this->getAthleteSeach($raffle->idAthlete);
              
              if($athlete->enrolment){
                $athleteRaffle = [
                  "enrolment" => $athlete->enrolment,
                  "name" => $athlete->name,
                  "category" => $athlete->category,
                  "qtdRaflles" => count($raffle->numberRaffle),
                  "qtdRaflleSolds" => null,
                ];
  
                $qtdRaflleSolds = 0;
                foreach($raffle->numberRaffle as &$number){
                  if($number->person){
                    $qtdRaflleSolds++;
                  }
                }
  
                $athleteRaffle['qtdRaflleSolds'] = $qtdRaflleSolds;
  
                $response[] = $athleteRaffle;
              }
            }

            return $this->respond($response);
      } catch (Exception $e) {
          return $this->fail($e->getMessage());
      }
    }

    public function createRaffleAthlete() {
      try {
        $data = $this->request->getJSON();
        $raffles = $this->returnDb();
        $lastNumber = $raffles[0];

        $raffleAthleteIndex = $this->raffleAthleteSeach($data->enrolment);
        $rafflesAthlete = null;

        if($raffleAthleteIndex != false){
            $rafflesAthlete = $raffles[$raffleAthleteIndex];
        }

        $lastNumberAthlete = $lastNumber->lastNumber;


        if($rafflesAthlete != null){
            for ($i=0; $i < 8; $i++) { 
                $lastNumberAthlete++;
                $newNumberAthlete = [
                    'number'   => $lastNumberAthlete,
                    'person'   => null,
                    'typePayment'   => null
                ];


                $rafflesAthlete->numberRaffle[] = $newNumberAthlete;
            }

            $raffles[$raffleAthleteIndex] = $rafflesAthlete;

            $lastNumber->lastNumber = $lastNumberAthlete;
            $raffles[0] = $lastNumber;
            $this->saveRaffle($raffles);

            return $this->respond($rafflesAthlete);
        }

        $newRaffleAthlete = [
            'idAthlete' => $data->enrolment,
            'numberRaffle' => []
        ];

        for ($i=0; $i < 8; $i++) { 
            $lastNumberAthlete++;
            $newNumberAthlete = [
                'number'   => $lastNumberAthlete,
                'person'   => null,
                'typePayment'   => null
            ];


            $newRaffleAthlete['numberRaffle'][] = $newNumberAthlete;
        }

        $raffles[] = $newRaffleAthlete;

        $lastNumber->lastNumber = $lastNumberAthlete;
        $raffles[0] = $lastNumber;
        $this->saveRaffle($raffles);
        
        return $this->respond($newRaffleAthlete);
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