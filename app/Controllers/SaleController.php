<?php namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use \DateTime; 
use App\Models\EfiPayModel;
use Exception;

class SaleController extends ResourceController{
    use ResponseTrait;

    public function __construct() {}

    private function returnSaleDb() {
        $contents = file_get_contents(ROOTPATH.'/app/Assets/Json/sale.json');
        return json_decode($contents);
    }

    private function returnSellerDb() {
        $contents = file_get_contents(ROOTPATH.'/app/Assets/Json/seller.json');
        return json_decode($contents);
    }

    private function returnProductDb() {
        $contents = file_get_contents(ROOTPATH.'/app/Assets/Json/product.json');
        return json_decode($contents);
    }
  
    private function saveSale($jsonObj = null) {
      file_put_contents(ROOTPATH.'/app/Assets/Json/sale.json', json_encode($jsonObj));
    }

    private function saveSeller($jsonObj = null) {
        file_put_contents(ROOTPATH.'/app/Assets/Json/seller.json', json_encode($jsonObj));
    }

    public function createSale() {
        try {
            $data = $this->request->getJSON();
            $sales  = $this->returnSaleDb();

            $newId = count($sales);
            $data->id = $newId;
            $data->numberSale = '20250'.$newId;
            $data->deliveryStatus = 0;

            $sales[$newId] = $data;
            $this->saveSale($sales);

            return $this->respond($data);
        } catch (Exception $e) {
            return $this->fail($e->getMessage());
          }
    }

    public function createSeller() {
        try {
            $data = $this->request->getJSON();
            $sellers  = $this->returnSellerDb();

            $newId = count($sellers);
            $data->id = $newId;

            $sellers[$newId] = $data;
            $this->saveSeller($sellers);

            return $this->respond($data);
        } catch (Exception $e) {
            return $this->fail($e->getMessage());
          }
    }

    public function updateSaleSatusDelivery() {
        try {
            $data = $this->request->getJSON();
            $sales  = $this->returnSaleDb();

            $saleIndex = array_search($data->id, array_column($data, 'id'));

            $sales[$saleIndex]->deliveryStatus = $data->deliveryStatus;

            $this->saveSale($sales);

            return $this->respond($sales[$newId]);
        } catch (Exception $e) {
            return $this->fail($e->getMessage());
          }
    }

    public function updatePaymentMethod(){
        $data = $this->request->getJSON();
        $sales  = $this->returnSaleDb();

        $saleIndex = array_search($data->numberSale, array_column($sales, 'numberSale'));

        if($data->paymentMethod == 1){
            $sales[$saleIndex]->paymentMehod = [
                "paymentMehodId" => $data->paymentMethod,
                "paid"=>false,
                "linkCard"=>"ADD"
            ];
        } elseif($data->paymentMethod == 0){
            $modelEdi = new EfiPayModel();
            
            $responseEfi = $modelEdi->createPixMaturityName("2025-04-06", $sales[$saleIndex]->buyerName, number_format($data->price, 2, '.', ''));

            if($responseEfi['status'] != 201){
                return $this->fail($responseEfi);
            }

            $sales[$saleIndex]->paymentMehod = [
                "paymentMehodId" => $data->paymentMethod, 
                "paid"=>false,
                "txid"=>$responseEfi['body']['txid']
            ];
        }

        $this->saveSale($sales);

        $response = [
            'status'   => 200,
            'value'    => $sales[$saleIndex]
        ];
        return $this->respond($response);
    }

    public function updateLinkCardPayment(){
        $data = $this->request->getJSON();
        $sales  = $this->returnSaleDb();

        $saleIndex = array_search($data->numberSale, array_column($sales, 'numberSale'));
        
        if(property_exists($sales[$saleIndex], "paymentMehod")){
            $sales[$saleIndex]->paymentMehod->linkCard = $data->link;
        };

        $this->saveSale($sales);

        $response = [
            'status'   => 200,
            'value'    => $sales[$saleIndex]
        ];
        return $this->respond($response);
    }

    public function updatePaidCardPayment(){
        $data = $this->request->getJSON();
        $sales  = $this->returnSaleDb();

        $saleIndex = array_search($data->numberSale, array_column($sales, 'numberSale'));
        
        if(property_exists($sales[$saleIndex], "paymentMehod")){
            $sales[$saleIndex]->paymentMehod->paid = $data->paid;
        };

        $this->saveSale($sales);

        $response = [
            'status'   => 200,
            'value'    => $sales[$saleIndex]
        ];
        return $this->respond($response);
    }

    public function updateDeliveryStatus(){
        $data = $this->request->getJSON();
        $sales  = $this->returnSaleDb();

        $saleIndex = array_search($data->numberSale, array_column($sales, 'numberSale'));
        
        if(property_exists($sales[$saleIndex], "deliveryStatus")){
            $sales[$saleIndex]->deliveryStatus = $data->statusId;
        };

        $this->saveSale($sales);

        $response = [
            'status'   => 200,
            'value'    => $sales[$saleIndex]
        ];
        return $this->respond($response);
    }

    public function searchPixSale($txid = null){

        $response = [
            'status'   => 200,
            'value'    => ["txid" => $txid]
        ];

        $modelEdi = new EfiPayModel();
        $responsePix = $modelEdi->searchPix($txid);

        if($responsePix['status'] != 201){
            return $this->fail($responsePix);
        }

        $qrCodPix = $modelEdi->generatePixQrCode($responsePix['body']['loc']['id']);

        if($qrCodPix['status'] != 201){
            return $this->fail($qrCodPix);
        }
        
        $response['value']['pixQrCode'] = $qrCodPix['body']['imagemQrcode'];
        $response['value']['pixCopyPaste'] = $responsePix['body']['pixCopiaECola'];
        $response['value']['pixName'] = $responsePix['body']['recebedor']['nome'];
        $response['value']['pixValue'] = $responsePix['body']['valor']['original'];
        $response['value']['pixMaturity'] = $responsePix['body']['calendario']['dataDeVencimento'];
        $response['value']['pixStatus'] = $responsePix['body']['status'];

        return $this->respond($response);
    }

    public function getAllSale() {
        try {
            $sales  = $this->returnSaleDb();
            $sellers  = $this->returnSellerDb();
            $products  = $this->returnProductDb();
            $updated = false;

            foreach($sales as $key=>$sale){
                
                foreach($sale->product as $key=>$product){
                    $productIndex = array_search($product->idProduct, array_column($products, 'id'));
    
                    $product->description = $products[$productIndex]->description;
                    $product->value = $products[$productIndex]->value;
                }

                if(property_exists($sale, "paymentMehod") && $sale->paymentMehod->paymentMehodId == 0 && !$sale->paymentMehod->paid){
                    $modelEdi = new EfiPayModel();
                    $responsePix = $modelEdi->searchPix($sale->paymentMehod->txid);

                    if($responsePix['status'] != 201){
                        return $this->fail($responsePix);
                    }

                    if($responsePix['body']['status'] == "CONCLUIDA"){
                        $sale->paymentMehod->paid = true;
                        $sale->deliveryStatus = 1;
                        $updated = true;
                    }
                }

                if(property_exists($sale, "sellerId") && !property_exists($sale, "nameSeller")){
                    $sellerIndex = array_search($sale->sellerId, array_column($sellers, 'id'));
                    if($sellerIndex != false){
                        $sale->nameSeller = $sellers[$sellerIndex]->name;
                    }
                }

                if((property_exists($sale, "deliveryStatus") && property_exists($sale, "paymentMehod")) && $sale->deliveryStatus == 0 && $sale->paymentMehod->paid == true){
                    $sale->deliveryStatus = 1;
                }
            }

            if($updated){
                $this->saveSale($sales);
            }

            array_shift($sales);

            $response = [
                'status'   => 200,
                'value'    => $sales
            ];
            
            return $this->respond($response);
        } catch (Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    public function getAllSaleStatus() {
        try {
            $sales  = $this->returnSaleDb();
            $products  = $this->returnProductDb();

            $response = [
                'status'   => 200,
                'value'    => null
            ];

            array_shift($sales);

            foreach($sales as $key=>$sale){
                if(property_exists($sale, "deliveryStatus") && $sale->deliveryStatus < 2){
                    foreach($sale->product as $key=>$product){
                        $productIndex = array_search($product->idProduct, array_column($products, 'id'));
        
                        $product->description = $products[$productIndex]->description;
                        $product->value = $products[$productIndex]->value;
                    }

                    $response['value'][] = $sale;
                }
            }

            return $this->respond($response);
        } catch (Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    public function getAllSalePaid() {
        try {
            $sales  = $this->returnSaleDb();
            $updated = false;

            array_shift($sales);

            $response = [
                'status'   => 200,
                'value'    => null
            ];

            foreach($sales as $key=>$sale){
                if(property_exists($sale, "paymentMehod") && $sale->paymentMehod->paid == true){
                    $response['value'][] = $sale;
                }
            }
            
            return $this->respond($response);
        } catch (Exception $e) {
            return $this->fail($e->getMessage());
          }
    }

    public function getAllSeller() {
        try {
            $sellers  = $this->returnSellerDb();

            array_shift($sellers);

            $response = [
                'status'   => 200,
                'value'    => $sellers
            ];
            
            return $this->respond($response);
        } catch (Exception $e) {
            return $this->fail($e->getMessage());
          }
    }

    public function getSale($numberSale=null) {
        try {
            $sales  = $this->returnSaleDb();
            $products  = $this->returnProductDb();

            $saleIndex = array_search($numberSale, array_column($sales, 'numberSale'));

            if($saleIndex == false){
                $response = [
                    'status'   => 200,
                    'value'    => null
                ];

                return $this->respond($response);
            }

            
            foreach($sales[$saleIndex]->product as $key=>$product){
                $productIndex = array_search($product->idProduct, array_column($products, 'id'));

                $product->description = $products[$productIndex]->description;
                $product->value = $products[$productIndex]->value;
            }

            $response = [
                'status'   => 200,
                'value'    => $sales[$saleIndex]
            ];

            
            
            return $this->respond($response);
        } catch (Exception $e) {
            return $this->fail($e->getMessage());
          }
    }

    public function getCheckSaleSeller($id=null) {
        try {
            $sales  = $this->returnSaleDb();
            $products  = $this->returnProductDb();

            array_shift($sales);

            $response = [
                'status'   => 200,
                'value'    => null
            ];

            foreach($sales as $key=>$sale){
                if($sale->sellerId == $id){
                    foreach($sale->product as $key=>$product){
                        $productIndex = array_search($product->idProduct, array_column($products, 'id'));
        
                        $product->description = $products[$productIndex]->description;
                        $product->value = $products[$productIndex]->value;
                    }
                    $response['value'][] = $sale;
                }
            }

            return $this->respond($response);
        } catch (Exception $e) {
            return $this->fail($e->getMessage());
          }
    }

    public function getAllProduct() {
        try {
            $products  = $this->returnProductDb();

            $response = [
                'status'   => 200,
                'value'    => $products
            ];

            return $this->respond($response);
        } catch (Exception $e) {
            return $this->fail($e->getMessage());
          }
    }

    public function getSeller($id=null) {
        try {
            $seller  = $this->returnSellerDb();

            $sellerIndex = array_search($id, array_column($seller, 'id'));

            $response = [
                'status'   => 200,
                'value'    => $seller[$sellerIndex]
            ];

            if($sellerIndex == false){
                $response = [
                    'status'   => 200,
                    'value'    => null
                ];
            }

            return $this->respond($response);
        } catch (Exception $e) {
            return $this->fail($e->getMessage());
          }
    }
}