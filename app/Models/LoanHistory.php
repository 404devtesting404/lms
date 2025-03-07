<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */


namespace App\Models;

use Eloquent;

class LoanHistory extends Eloquent
{
    protected $table = 'loan_history';
    protected $fillable = [
        'account_no',
        'sanction_number',
        'borrower_id',
        'group_id',
        'office_id',
        'total_amount',
        'total_amount_pr',
        'total_amount_mu',
        'takaful',
        'loan_type_id',
        'loan_period',
        'kibor_rate',
        'spread_rate',
        'markup_rate',
        'loan_frequency',
        'property_amount',
        'loan_status_id',
        'disb_date',
        'rep_start_date',
        'musharakah_date',
        'musharakah_status',
        'closed_date',
        'kibor_revision_cycle',
        'kibor_revision_date'
    ];
    //

    public function loan_borrower()
    {
        return $this->belongsTo(LoanBorrower::class, 'borrower_id');
    }
    public function loan_status()
    {
        return $this->belongsTo(LoanStatus::class, 'loan_status_id');
    }
    public function loan_group()
    {
        return $this->belongsTo(LoanGroup::class, 'group_id');
    }
    public function loan_office()
    {
        return $this->belongsTo(GeneralOffice::class, 'office_id');
    }
    public function loantype()
    {
        return $this->belongsTo(LoanType::class, 'loan_type_id');
    }

    public function loan_payment()
    {
        return $this->hasMany(LoanPaymentDue::class, 'loan_id');
    }

    public function loan_payment_recovery()
    {
        return $this->hasMany(LoanPaymentRecovered::class, 'loan_id');
    }

    public function loan_modifications()
    {
        return $this->hasMany(LoanModification::class, 'loan_id');
    }

    public function getTotalModificationAmountAttribute()
    {
        return $this->loan_modifications()->sum('amount');
    }

    // add kia
    // public function loan_payment_due()
    // {
    //     return $this->hasMany(LoanPaymentDue::class, 'loan_id'); // 'loan_id' foreign key hai
    // }
    public function latest_loan_payment_due()
    {
        return $this->hasOne(LoanPaymentDue::class, 'loan_id', 'id')
            ->where('payment_status', 1)
            ->latest('id'); // ✅ Yeh sirf last entry lega (ORDER BY id DESC LIMIT 1)
    }
}
