<?php namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use Exception;

class StoreController extends ResourceController{
    use ResponseTrait;

    public function __construct() {}

    public function getAllCitys(){
        try{
            $arrContextOptions=array(
                "ssl"=>array(
                    "verify_peer"=>false,
                    "verify_peer_name"=>false,
                ),
            );

            $json = file_get_contents("https://servicodados.ibge.gov.br/api/v1/localidades/municipios?orderBy=nome", false, stream_context_create($arrContextOptions));
            $data = json_decode($json,true);

            foreach ($data as $indice => $valor){
               $response[] = ['id' => $valor['id'], 'name' => $valor['nome']];
            }

            return $this->respond($response);
        } catch (Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    public function getTypeMenus($role = null) {
        try{
            $json  = file_get_contents(ROOTPATH.'/app/Assets/Json/menus.json');
            $jsonObj  = json_decode($json);
            $response = null;

            foreach ($jsonObj as &$value) {
                if($value->role == $role){
                    $response = $value;
                }
            }

            return $this->respond($response);
        } catch (Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    public function dowloadTerm() {
        try{
            $file  = ROOTPATH.'/app/Assets/Document/TERMO.pdf';
            return $this->response->download($file , null );
        } catch (Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    public function dowloadRegulation() {
        try{
            $file  = ROOTPATH.'/app/Assets/Document/REGULAMENT_ II_COPA_ACE_2025.pdf';
            return $this->response->download($file , null );
        } catch (Exception $e) {
            return $this->fail($e->getMessage());
        }
    }
}