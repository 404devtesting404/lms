<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class LoanModification
 *
 * @property $id
 * @property $loan_id
 * @property $modification
 * @property $amount
 * @property $modify_by
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class LoanModification extends Model
{
    
    static $rules = [
		'loan_id' => 'required',
		'modification' => 'required',
		'amount' => 'required',
		'modify_by' => 'required',
    ];

    protected $perPage = 10;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['loan_id','modification','amount','modify_by','due_date'];



}
