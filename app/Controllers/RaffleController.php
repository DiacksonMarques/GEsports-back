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
          $builder->select('a.enrolment, p.id, p.name, p.gender,c.name as category, c.id as categoryId');
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

    public function getAllRaffleNumber() {
      try{
            $raffles = $this->returnDb();

            $response = [
              "AdultoFem" => 0,
              "AdultoMas" => 0,
              "Sub17Mas" => 0,
              "Sub17Fem" => 0,
              "Sub14" => 0,
            ];

            foreach($raffles as &$raffle){
              $athlete = $this->getAthleteSeach($raffle->idAthlete);
              
              if($athlete->enrolment){
                switch ($athlete->categoryId) {
                  case 1:
                    $response['Sub14']++;
                    break;

                  case 2:
                    if($athlete->gender == "MASCULINO"){
                      $response['Sub17Mas']++;
                    } else if($athlete->gender == "FEMININO"){
                      $response['Sub17Fem']++;
                    }
                    break;

                  case 3:
                    if($athlete->gender == "MASCULINO"){
                      $response['AdultoMas']++;
                    } else if($athlete->gender == "FEMININO"){
                      $response['AdultoFem']++;
                    }
                    break;
                
                  default:
                    # code...
                    break;
                }
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

    public function addNumberRaflle(){
     try {
        $data = $this->request->getJSON();
        $raffles = $this->returnDb();
        $response = [];

        foreach($data as &$raffle){
          $raffleSelecedIndex = array_search($raffle->enrolment, array_column($raffles, 'idAthlete'));

          $numbersRaffles = $raffles[$raffleSelecedIndex]->numberRaffle;

          $numberSelecedIndex = array_search($raffle->number, array_column($numbersRaffles, 'number'));
          $numberSeleced = $numbersRaffles[$numberSelecedIndex];
          
          $numberSeleced->person = $raffle->person;
          $numberSeleced->typePayment = $raffle->typePayment;

          $raffles[$raffleSelecedIndex]->numberRaffle[$numberSelecedIndex] = $numberSeleced;

          $response[] = $numberSeleced;
        }

        $this->saveRaffle($raffles);

        return $this->respond($response);

     } catch (Exception $e) {
        return $this->fail($e->getMessage());
    }

  }
}