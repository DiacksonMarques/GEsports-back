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
            $data = $model->searchPix("b997834a7be22f70a603b874d88f8325");
            
            return $this->respond($data);
        } catch (Exception $e) {
            return $this->fail($e->getMessage());
        }
    }
}
  