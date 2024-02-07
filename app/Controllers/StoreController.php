<?php namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use Exception;

class StoreController extends ResourceController{
    use ResponseTrait;

    public function __construct() {}

    public function getAllCitys(){
        try{
            $json = file_get_contents("https://servicodados.ibge.gov.br/api/v1/localidades/municipios?orderBy=nome");
            $data = json_decode($json,true);

            foreach ($data as $indice => $valor){
               $response[] = ['id' => $valor['id'], 'name' => $valor['nome']];
            }

            return $this->respond($response);
        } catch (Exception $e) {
            return $this->fail($e->getMessage());
        }
    }
}