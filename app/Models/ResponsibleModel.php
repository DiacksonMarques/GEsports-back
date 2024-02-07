<?php namespace App\Models;

use CodeIgniter\Model;

class ResponsibleModel extends Model{
    protected $table = 'responsible';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'dd-phone', 'number-phone', 'cep', 'adress', 'neighborhood', 'city'];
    protected $validationRules = [
        'name' => 'required|min_length[5]',
        'dd-phone' => 'required|max_length[2]|min_length[2]',
        'number-phone' => 'required|max_length[9]|min_length[9]',
        'cep' => 'required|max_length[8]|min_length[8]',
        'adress' => 'required|min_length[5]',
        'neighborhood' => 'required|min_length[5]',
        'city' => 'required|min_length[5]',
    ];
}