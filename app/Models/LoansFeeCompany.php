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
class LoansFeeCompany extends Model {

    protected $table = "loan_fees_company";
    static $rules = [
        'loanId' => 'required',
    ];
    protected $perPage = 10;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['loanId', 'FeesCompanyId', 'Fees', 'FeesStatus', 'FeesType'];
    public function legalcompany()
    {
        return $this->belongsTo(CompanyLegal::class, 'FeesCompanyId');
    }
    public function incomecompany()
    {
        return $this->belongsTo(CompanyIncomeEst::class, 'FeesCompanyId');
    }
    public function valcompany()
    {
        return $this->belongsTo(CompanyValuation::class, 'FeesCompanyId');
    }

}
