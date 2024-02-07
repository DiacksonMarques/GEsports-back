<?php namespace App\Models;

use CodeIgniter\Model;

class MonthlyPaymentModel extends Model{
    protected $table = 'monthly-payment';
    protected $primaryKey = 'id';
    protected $allowedFields = ['maturity'];
    protected $validationRules = [
        'maturity' => 'required',
    ];
}