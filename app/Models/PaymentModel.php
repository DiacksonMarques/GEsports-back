<?php namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model{
    protected $table = 'payment';
    protected $primaryKey = 'id';
    protected $allowedFields = ['paymentDate','athlete_id','formPayment_id','monthlyFee_id', 'monthlyPayment_id'];
    protected $validationRules = [
        'paymentDate' => 'required',
        'athlete_id' => 'required',
        'formPayment_id' => 'required',
        'monthlyFee_id' => 'required',
        'monthlyPayment_id' => 'required',
    ];
}