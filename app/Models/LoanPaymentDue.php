<?php

namespace App\Models;

use Carbon\Carbon;
use Eloquent;

class LoanPaymentDue extends Eloquent
{
    protected $table = 'loan_payment_due';
    protected $appends = ['charity_amount'];
    protected $fillable = ['loan_id', 'amount_total', 'amount_pr', 'amount_mu', 'amount_takaful', 'outstanding', 'enhancement_amount', 'due_date', 'installment_no', 'due_status', 'payment_status', 'is_settle', 'payment_at'];

    //
    public function loan_history()
    {
        return $this->belongsTo(LoanHistory::class, 'loan_id');
    }

    public function loan_payment_recovery()
    {

        return $this->belongsTo(LoanPaymentRecovered::class, 'loan_id');
    }

    public function loan_payment_recovered()
    {
        return $this->hasMany(LoanPaymentRecovered::class, 'due_id', 'id');
    }


    public function getCharityAmountAttribute()
    {
        //        $is_loan_settle = LoanPaymentRecovered::where(['loan_id'=> $this->attributes['loan_id'], 'is_settle'=> 1])->first();
        //        $currentDate = $is_loan_settle ? Carbon::parse($is_loan_settle->recovered_date) : Carbon::now();
        //        $dueDate = Carbon::parse($this->attributes['due_date']);
        //        $dueDateMonth = date('n', strtotime($this->attributes['due_date']));
        //
        //        // Get the number of days in the current month up to the current date
        //        $daysInCurrentMonth = $currentDate->day;
        //        // Calculate the number of days between due_date and the current date
        //        $numberOfDays = $dueDate->diffInDays($currentDate);
        //
        //        // Calculate charity amount based on 100rs per day if payment_status is 0
        ////        return $this->attributes['payment_status'] === 0 && $this->attributes['due_date'] < $currentDate ? 100 * $numberOfDays : 0;
        //        if($this->attributes['payment_status'] === 0 && $this->attributes['due_date'] < $currentDate ){
        //            if($currentDate->month == $dueDateMonth && $daysInCurrentMonth < 7){
        //                return 0;
        //            }
        //            return 100 * $numberOfDays;
        //        } else{
        //            return 0;
        //        }

        $is_loan_settle = LoanPaymentRecovered::where(['loan_id' => $this->attributes['loan_id'], 'is_settle' => 1])->first();
        //        $currentDate = $is_loan_settle ? Carbon::parse($is_loan_settle->recovered_date) : Carbon::parse($this->attributes['payment_at']);
        $currentDate = !empty($this->attributes['payment_at']) ? Carbon::parse($this->attributes['payment_at']) : ($is_loan_settle ? Carbon::parse($is_loan_settle->recovered_date) : Carbon::now());

        $dueDate = Carbon::parse($this->attributes['due_date']);

        // Calculate the number of days between due_date and the current date
        $numberOfDays = $dueDate->diffInDays($currentDate, false);

        // Calculate charity amount based on 100rs per day if payment_status is 0
        if ($dueDate->lessThan($currentDate)) {
            if ($numberOfDays > 7) {
                // Apply charity charge for each day from the due date to the payment date
                return 100 * $numberOfDays;
            }
            return 0; // No charge if payment is made within 7 days of the due date
        } else {
            return 0; // No charge if payment status is not 0
        }
    }
}
