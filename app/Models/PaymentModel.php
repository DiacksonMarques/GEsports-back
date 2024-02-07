<?php namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model{
    protected $table = 'payment';
    protected $primaryKey = 'id';
    protected $allowedFields = ['payment-date','athlete_id','form-payment_id','monthly-fee_id', 'monthly-payment_id'];
    protected $validationRules = [
        'payment-date' => 'required',
        'athlete_id' => 'required',
        'form-payment_id' => 'required',
        'monthly-fee_id' => 'required',
        'monthly-payment_id' => 'required',
    ];
}