<?php namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use Exception;
use App\Models\EfiPayModel;

class EfiPayController extends ResourceController{
    use ResponseTrait;

    public function teste() {
        try{
            $model = new EfiPayModel();
            $data = $model->teste();
            
            return $this->respond($data);
        } catch (Exception $e) {
            return $this->fail($e->getMessage());
        }
    }
}
  