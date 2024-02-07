<?php namespace App\Models;

use CodeIgniter\Model;

class FormPaymentModel extends Model{
    protected $table = 'form-payment';
    protected $primaryKey = 'id';
    protected $allowedFields = ['description'];
    protected $validationRules = [
        'description' => 'required',
    ];
}