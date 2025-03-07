<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class LoansFee
 *
 * @property $id
 * @property $loanId
 * @property $processingFees
 * @property $processingFeesStatus
 * @property $fedFees
 * @property $fedFeesStatus
 * @property $legalFeesCompanyId
 * @property $legalFees
 * @property $legalFeesStatus
 * @property $valuationCompanyId
 * @property $valuationFees
 * @property $valuationFeesStatus
 * @property $incomeEstCompanyId
 * @property $incomeEstFees
 * @property $incomeEstFeesStatus
 * @property $stampPaperFees
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class LoansFee extends Model {

    protected $table = "loan_fees";
    static $rules = [
        'loanId' => 'required',
    ];
    protected $perPage = 10;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['loanId', 'processingFees', 'processingFeesStatus', 'fedFees', 'fedFeesStatus', 'legalFeesCompanyId', 'legalFees', 'legalFeesStatus', 'valuationCompanyId', 'valuationFees', 'valuationFeesStatus', 'incomeEstCompanyId', 'incomeEstFees', 'incomeEstFeesStatus', 'lienFees', 'lienFeesStatus', 'stampPaperFees', 'stampPaperFeesStatus'];

}
