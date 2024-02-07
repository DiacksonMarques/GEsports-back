<?php namespace App\Models;

use CodeIgniter\Model;

class PersonModel extends Model{
    protected $table = 'person';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'height',
        'weight',
        'name',
        'birthDate',
        'cpf',
        'rg',
        'issuing-body',
        'ufEmitter',
        'cep',
        'adress',
        'neighborhood',
        'city',
        'naturalness',
        'electoralDomicile',
        'ddPhone',
        'numberPhone',
        'profilePhoto',
        'gender',
        'create_time',
        'responsible_id',
        'school_id',
    ];
    protected $validationRules = [
        'height' => 'required',
        'weight' => 'required',
        'name' => 'required|min_length[5]',
        'birthDate' => 'required|min_length[5]',
        //'cpf' => 'required|min_length[11]|max_length[11],is_unique',
        //'rg' => 'required|max_length[14],is_unique',
        //'issuingBody' => 'required|max_length[5]',
        //'ufEmitter' => 'required|min_length[2]|max_length[2]',
        //'cep' => 'required|max_length[8]|min_length[8]',
        //'adress' => 'required|min_length[5]',
        //'neighborhood' => 'required|min_length[5]',
        //'city' => 'required|min_length[5]',
        'gender' => 'required',
        'naturalness' => 'required|min_length[5]',
        'ddPhone' => 'required|max_length[2]|min_length[2]',
        'numberPhone' => 'required|max_length[9]|min_length[9]',
    ];
}