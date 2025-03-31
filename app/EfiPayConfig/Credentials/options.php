<?php

/**
 * Environment
 */
$sandbox = false; // false = Production | true = Homologation

/**
 * Credentials of Production
 */
$clientIdProd = "Client_Id_ad51cbeb95cfa4bd1a4bc69ad0d70e44e9baa499";
$clientSecretProd = "Client_Secret_a4de48e82e359f1810a7962aeebea90baa4c4eaf";
$pathCertificateProd = realpath(ROOTPATH . "/app/EfiPayConfig/Credentials/producao-669198-GEsport.p12"); // Absolute path to the certificate in .pem or .p12 format

/**
 * Credentials of Homologation
 */
$clientIdHomolog = "Client_Id_de1760f6f03a7546f49e536e7f7ad45d9e297d7a";
$clientSecretHomolog = "Client_Secret_baa351aec32bd40e0e52b0da71a64eb4c37cadaa";
$pathCertificateHomolog = realpath(ROOTPATH . "/app/EfiPayConfig/Credentials/homologacao-669198-GEsport.p12"); // Absolute path to the certificate in .pem or .p12 format

/**
 * Array with credentials and other settings
 */
return [
	"clientId" => ($sandbox) ? $clientIdHomolog : $clientIdProd,
	"clientSecret" => ($sandbox) ? $clientSecretHomolog : $clientSecretProd,
	"certificate" => ($sandbox) ? $pathCertificateHomolog : $pathCertificateProd,
	"pwdCertificate" => "", // Optional | Default = ""
	"sandbox" => $sandbox, // Optional | Default = false
	"debug" => false, // Optional | Default = false
	"timeout" => 30, // Optional | Default = 30
	"responseHeaders" => true, //  Optional | Default = false
];
