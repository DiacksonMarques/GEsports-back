<?php namespace App\Models;

use Efi\Exception\EfiException;
use Efi\EfiPay;
use Exception;

class EfiPayModel {

    private function createEfiApi(){
        $optionsFile = __DIR__ . "../../EfiPayConfig/Credentials/options.php";
        if (!file_exists($optionsFile)) {
            die("Options file not found or on path <code>$options</code>.");
        }
        $options = include $optionsFile;
        return new EfiPay($options);
    }

    public function teste() {
        $optionsFile = __DIR__ . "../../EfiPayConfig/Credentials/options.php";
        return !file_exists($optionsFile);
    }

    public function createPixMaturity($dueDate, $cpf, $name, $value){
        $bytes = random_bytes(rand(13, 16));
        $params = [
            "txid" => bin2hex($bytes)
        ];

        $body = [
            "calendario" => [
                "dataDeVencimento" => $dueDate,
                "validadeAposVencimento"=> 0
            ],
            "devedor" => [
                "cpf" => $cpf,
                "nome" => $name
            ],
            "valor" => [
                "original" => $value
            ],
            "chave" => "4716dec1-2697-42f6-a3f5-2d2b58b716a8",
            "solicitacaoPagador" => "Cobrança para seletiva ACE.",
            "infoAdicionais" => [
                [
                    "nome" => "ACE",
                    "valor" => "Seletiva ACE 2025"
                ]
	        ]
        ];

        try {
            $api = $this->createEfiApi();
            $response = $api->pixCreateDueCharge($params, $body);
            return [
                "status"=>201,
                "body"=> $response->body
            ];
        } catch (EfiException $e) {
            return [
                "status"=>$e->status,
                "error"=> $e->errorDescription
            ];
        }catch (Exception $e) {
            return [
                "status"=> 500,
                "error"=> $e->getMessage()
            ];
        }
    }

    public function searchPix($txid){
        try {
            $params = [
                "txid" => $txid
            ];

            $api = $this->createEfiApi();
            $response = $api->pixDetailDueCharge($params);

            return [
                "status"=>201,
                "body"=> $response->body
            ];
        } catch (EfiException $e) {
            return [
                "status"=>$e->status,
                "error"=> $e->errorDescription
            ];
        }catch (Exception $e) {
            return [
                "status"=> 500,
                "error"=> $e->getMessage()
            ];
        }
    }

    public function generatePixQrCode($idPix){
        try {
            $params = [
                "id" => $idPix
            ];

            $api = $this->createEfiApi();
            $response = $api->pixGenerateQRCode($params);
            
            return [
                "status"=>201,
                "body"=> $response->body
            ];
        } catch (EfiException $e) {
            return [
                "status"=>$e->status,
                "error"=> $e->errorDescription
            ];
        }catch (Exception $e) {
            return [
                "status"=> 500,
                "error"=> $e->getMessage()
            ];
        }
    }
}