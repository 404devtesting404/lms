<?php
namespace App\Http\Controllers;

use App\Models\LoanBorrower;
use DateTime;
use App\Helpers\Qs;
use App\Helpers\Pay;
use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\PaymentCreate;
use App\Http\Requests\Payment\PaymentUpdate;
use App\Models\Setting;
use App\Repositories\MyClassRepo;
use App\Repositories\LoanBorrowerRepo;
use App\Repositories\LoanHistoryRepo;
use App\Repositories\PaymentRepo;
use App\Repositories\StudentRepo;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PDF;
use File;

class LoansfunctionController extends Controller
{



    function GenerateRepaymentScheduleDecline($LoanId, $LoanFees, $KiborNew)
    {

        //echo "<pre>";
        //print_r($LoanFees);
        //print_r($KiborNew);
        //die;
        $takaful_amount = 1;
        $fed_amount = $LoanFees->fedFees;
        $kibor_rate = $KiborNew->kibor_rate;
        $spread_rate = $KiborNew->spread_rate;

        $data = $this->my_loan_history->find($LoanId);
        $amount_pr = $data['total_amount_pr'];
        $disb_date = $data['disb_date'];
        $loan_type_id = $data['loan_type_id'];
        $disb_day = date("d", strtotime($disb_date));
        if ($disb_day >= 2 && $disb_day <= 5) {
            $rep_start_date = date("Y-m", strtotime($disb_date . "+1 month")) . "-05";
        }
        if ($disb_day >= 6 && $disb_day <= 10) {
            $rep_start_date = date("Y-m", strtotime($disb_date . "+1 month")) . "-10";
        }
        if ($disb_day >= 11 && $disb_day <= 15) {
            $rep_start_date = date("Y-m", strtotime($disb_date . "+1 month")) . "-15";
        }
        if ($disb_day >= 16 && $disb_day <= 20) {
            $rep_start_date = date("Y-m", strtotime($disb_date . "+1 month")) . "-20";
        }
        if ($disb_day >= 21 && $disb_day <= 25) {
            $rep_start_date = date("Y-m", strtotime($disb_date . "+1 month")) . "-25";
        }
        if ($disb_day >= 26 && $disb_day <= 1) {
            $rep_start_date = date("Y-m", strtotime($disb_date . "+1 month")) . "-01";
        }
//        echo $disb_date."<br>" ;
//        echo $rep_start_date ;die;
        $loan_freq = $data['loan_frequency'];
        $markup_rate = $data['markup_rate'];
        $loan_period = $data['loan_period'];

        $ChequeDate = $disb_date;
        $ApprovedLoanAmount = $amount_pr;
        $SrChargeRate = $markup_rate;
        $SrChargeRate = $kibor_rate + $spread_rate;
        $LoanFrequency = $loan_freq;
        $LoanTerm = $loan_period;
        $RepStartDate = $rep_start_date;

        $DueData = \App\Models\LoanPaymentDue::where(array('loan_id' => $LoanId))->exists();

        if ($DueData) {
            return 0;
        }
        $DayRepStart = date("d", strtotime($RepStartDate));
        //if ($DayRepStart < 15 || $DayRepStart > 25) {
        //  $DayRepStart = 15;
        $RepStartDate = date("Y-m", strtotime($RepStartDate)) . "-" . $DayRepStart;
        //}

        $GrandPrinc = $GrandServ = $GrandTotal = $GrandDays = $GrandTakaful = 0;

        $iLoanFrequency = $LoanFrequency;
        $Return = "<table width='100%' border='1' bordercolor='#999999' cellspacing='0' cellpadding='4'>";
        $Return .= "<tr bgcolor='#CCCCCC'>"
            . "<td  align='center' rowspan='2'>Sr#</td>"
            . "<td align='center' rowspan='2'>Schedule Date</td>"
            . "<td align='center' rowspan='2'>Days</td>"
            . "<td  colspan='3' align='center'>Dues</td><td rowspan='2'  align='center'>Balance</td></tr>"
            . "<tr bgcolor='#CCCCCC'><td align='center'>Principle</td><td align='center'>Srv Charge</td><td align='center'>Takaful</td><td align='center'>Total</td></tr>";

        $Return .= "<tr><td colspan='6' align='right'></td><td align='right'>$ApprovedLoanAmount</td></tr>";

        $arryModeOfPayment = array(
            1 => 1,
            3 => 3,
            7 => 4,
            6 => 6,
            4 => $LoanTerm,
            8 => 12
        );
        $LoanFrequency = $arryModeOfPayment[$iLoanFrequency];
        $arryModeOfPaymentCalc = array(
            1 => 12,
            3 => 4,
            7 => 3,
            6 => 2,
            4 => 1,
            8 => 1);

        $SchedLoanTerm = $LoanTerm;
        $FormulaloanTerm = $LoanTerm / 12;
        $modeOfP = $arryModeOfPaymentCalc[$iLoanFrequency];

        //$rate = interest rate
        //$nper = number of periods
        //$fv is future value
        //$pv is present value
        //$type is type
        $fv = 0;
        $pv = $ApprovedLoanAmount;
        $rate = ($SrChargeRate / 100) / 360 * 30;
        $nper = $LoanTerm;
        $type = 0;

//        $PMT = (-$fv - $pv * pow(1 + $rate, $nper)) /
//        (1 + $rate * $type) /
//        ((pow(1 + $rate, $nper) - 1) / $rate);
        $PMT = ((0 - $pv * pow(1 + $rate, $nper)) /
                (1 + $rate) /
                ((pow(1 + $rate, $nper) - 1) / $rate)) * -1;

        $rate = 16;
        $rate = $kibor_rate + $spread_rate;
        $Fst = ($SrChargeRate / $modeOfP) / 100;
        $Snd = pow((1 + $Fst), ($modeOfP * $FormulaloanTerm));
        $Trd = 1 / $Snd;
        $Fth = 1 - $Trd;
        $Ffth = $Fth / $Fst;
        $Final = round($ApprovedLoanAmount / $Ffth);

        //echo "Final1: " . $Final . "<br>";
        $TotalSrCharge = $ApprovedLoanAmount * ($SrChargeRate / 100);
        //echo "Final2: " . $TotalSrCharge . "<br>";
        $DailySrcCharge = $TotalSrCharge / 365;
        $TotalDaysLoanTerms = $SchedLoanTerm * (365 / 12);

        //echo "TotalDaysLoanTerms: " . $TotalDaysLoanTerms . "<br>";
        //echo "DailySrcCharge: " . $DailySrcCharge . "<br>";
        //Loop Here
        for ($i = 1; $i <= ($SchedLoanTerm / $LoanFrequency); $i++) {

            $AdditionalServiceCharges = 0;
            if ($i == 1) {
                $Dev_ScheduleDate = $RepStartDate;
                $datetime_chq = new DateTime(date("Y-m-d", strtotime($ChequeDate)));
                $datetime_repstart = new DateTime(date("Y-m-d", strtotime($RepStartDate)));
                $difference = $datetime_chq->diff($datetime_repstart);
                //

                /*
                  $TotalDifference = 0;
                  $Dev_ScheduleDate_R = $Dev_ScheduleDate;
                  for ($raja = 1; $raja <= ($SchedLoanTerm / $LoanFrequency); $raja++) {
                  if ($raja == 1) {
                  $TotalDifference += $difference->days;
                  } else {
                  $PreviousRepaymentDate = new DateTime(date("Y-m-d", strtotime($Dev_ScheduleDate_R)));
                  $date_R = new DateTime(date("Y-m-d", strtotime($Dev_ScheduleDate_R)));
                  $date_R->modify('+' . $LoanFrequency . ' month');
                  $Dev_ScheduleDate_R = $date_R->format('Y-m-d');
                  $NextRepaymentDate_R = new DateTime(date("Y-m-d", strtotime($Dev_ScheduleDate_R)));
                  $difference_R = $PreviousRepaymentDate->diff($NextRepaymentDate_R);
                  $TotalDifference += $difference_R->days;
                  }
                  }
                  //echo "Total Days Diff: " . $TotalDifference . "<br>";
                  if ($TotalDifference > $TotalDaysLoanTerms) {
                  //echo "1<br>";
                  //echo "DailySrcCharge: " . $DailySrcCharge . "<br>";

                  $RemainingDays = $TotalDifference - $TotalDaysLoanTerms;
                  //echo "RemainingDays: " . $RemainingDays . "<br>";
                  $AdditionalServiceCharges += ($DailySrcCharge * $RemainingDays);
                  } else if ($TotalDifference < $TotalDaysLoanTerms && $iLoanFrequency != 4) {
                  //echo "2<br>";
                  $RemainingDays = $TotalDaysLoanTerms - $TotalDifference;
                  $AdditionalServiceCharges -= ($DailySrcCharge * $RemainingDays);
                  }
                  //echo "AdditionalServiceCharges: " . $AdditionalServiceCharges . "<br>";
                 *
                 */
            } else {
                $PreviousRepaymentDate = new DateTime(date("Y-m-d", strtotime($Dev_ScheduleDate)));
                $date = new DateTime(date("Y-m-d", strtotime($Dev_ScheduleDate)));
                $date->modify('+' . $LoanFrequency . ' month');
                $Dev_ScheduleDate = $date->format('Y-m-d');
                $NextRepaymentDate = new DateTime(date("Y-m-d", strtotime($Dev_ScheduleDate)));
                $difference = $PreviousRepaymentDate->diff($NextRepaymentDate);
            }
            $MonthlyServiceCharge = $ApprovedLoanAmount * $Fst;
            //$MonthlyServiceCharge = round($MonthlyServiceCharge);


            if ($loan_type_id != 1) {
                if ($i > 12) {
                    if ($i == 13) {
                        $Snd = pow((1 + $Fst), ($modeOfP * ($FormulaloanTerm - 1)));
                        $Trd = 1 / $Snd;
                        $Fth = 1 - $Trd;
                        $Ffth = $Fth / $Fst;
                        $Final = round($ApprovedLoanAmount / $Ffth);
                    }
                    $MonthlyPrinciple = $Final - $MonthlyServiceCharge;
                    $MonthlyPrinciple = round($MonthlyPrinciple);
                } else {
                    $MonthlyPrinciple = 0;
                }
            } else {
                $MonthlyPrinciple = $Final - $MonthlyServiceCharge;
                $MonthlyPrinciple = round($MonthlyPrinciple);
            }

            $MonthlyServiceCharge += $AdditionalServiceCharges;
            if ($takaful_amount) {
                //echo $LoanTerm."/".$i."<br>";
                $lastTakaful = \App\Models\LoanTakaful::where(['loan_id' => $LoanId, 'type' => 0])->orderBy('id', 'desc')->first();
                $lastTakafulLife = \App\Models\LoanTakaful::where(['loan_id' => $LoanId, 'type' => 1])->orderBy('id', 'desc')->first();
                if (($i == 1 || $i % 13 == 0) && $ApprovedLoanAmount >= 500) {
                    $startDate = $Dev_ScheduleDate;
                    $endDate = date('Y-m-d', strtotime($Dev_ScheduleDate . "+11 month "));
                    $renewalDate = date("Y-m-d", strtotime($endDate . "+1 day"));
                    $property_array = [
                        'loan_id' => $LoanId,
                        'type' => '0',
                        'covered_amount' => $ApprovedLoanAmount,
                        'start_date' => $Dev_ScheduleDate,
                        'end_date' => $endDate,
                        'renewal_date' => $renewalDate
                    ];
//                    print_r($property_array);
//                    echo "<br>";
                    \App\Models\LoanTakaful::create($property_array);
                    $life_array = [
                        'loan_id' => $LoanId,
                        'type' => '1',
                        'covered_amount' => $ApprovedLoanAmount,
                        'start_date' => $Dev_ScheduleDate,
                        'end_date' => $endDate,
                        'renewal_date' => $renewalDate
                    ];
                    \App\Models\LoanTakaful::create($life_array);
                }
            }
            $ApprovedLoanAmount -= $MonthlyPrinciple;

            $DaysDiff = $difference->days;

            $MonthlyPrinciple = round($MonthlyPrinciple);
            $MonthlyServiceCharge = round($MonthlyServiceCharge);
//            if($takaful_amount){
//            $MonthlyTakaful = (($ApprovedLoanAmount*0.8/100)/365)*$DaysDiff;
//            if($MonthlyTakaful<0){
//                $MonthlyTakaful = 0;
//            }
//            $MonthlyTakaful = ceil($MonthlyTakaful);
//            } else {
//
//            }
            $MonthlyTakaful = 0;

            if ($i == ($SchedLoanTerm / $LoanFrequency)) {
                if ($ApprovedLoanAmount <> 0) {
                    $MonthlyPrinciple += $ApprovedLoanAmount;
                    $ApprovedLoanAmount = 0;
                }
            }

            $Total = $MonthlyPrinciple + $MonthlyServiceCharge + $MonthlyTakaful;

            $sScheduledRepaymentDate = date("M j, Y", strtotime($Dev_ScheduleDate));
            $sScheduledDay = date('D', strtotime($sScheduledRepaymentDate));
            $sScheduledDate = date('d', strtotime($sScheduledRepaymentDate));
            if ($sScheduledDay == "Sun") {
                $dateScheduledRepaymentDate = new DateTime(date("Y-m-d", strtotime($sScheduledRepaymentDate)));
                if ($sScheduledDate == 25) {
                    //$dateScheduledRepaymentDate->modify('-1 day');
                } else {
                    //$dateScheduledRepaymentDate->modify('+1 day');
                }
                $sScheduledRepaymentDate = $dateScheduledRepaymentDate->format('Y-m-d');
                $sScheduledRepaymentDate = date("M j, Y", strtotime($sScheduledRepaymentDate));
            }
            $MysqlScheduleDate = date("Y-m-d", strtotime($sScheduledRepaymentDate));

            \App\Models\LoanPaymentDue::create([
                'loan_id' => $LoanId,
                'installment_no' => $i,
                'due_date' => $MysqlScheduleDate,
                'amount_total' => $Total,
                'amount_pr' => $MonthlyPrinciple,
                'outstanding' => $ApprovedLoanAmount,
                'amount_mu' => $MonthlyServiceCharge,
                'amount_takaful' => $MonthlyTakaful
            ]);

            $Return .= "<tr>"
                . "<td>$i</td>"
                . "<td>$MysqlScheduleDate</td>"
                . "<td align='right'>$DaysDiff</td>"
                . "<td align='right'>$MonthlyPrinciple</td>"
                . "<td align='right'>$MonthlyServiceCharge</td>"
                . "<td align='right'>$MonthlyTakaful</td>"
                . "<td align='right'>" . ($Total) . "</td>"
                . "<td align='right'>" . ($ApprovedLoanAmount) . "</td>"
                . "</tr>";

            $GrandPrinc += $MonthlyPrinciple;
            $GrandServ += $MonthlyServiceCharge;
            $GrandTotal += $Total;
            $GrandDays += $DaysDiff;
            $GrandTakaful += $MonthlyTakaful;
        }

        /*
         * Sanction Number
          branch id
          BorrowerId
          Cycle number
          Product Code
          LoanId
         */
        $BranchId = $data->office_id;
        $BranchId = $this->GetDigits($BranchId, 2);
        $BorrowerId = $data->borrower_id;
        $LoanTypeId = $data->loan_type_id;
        $LoanType = \App\Models\LoanType::where('id', $LoanTypeId)->first();
        $LoanTypeCode = $LoanType->code;
        $SanctionNumber = $LoanTypeCode . "-" . $this->GetDigits($BranchId, 2) . "-" . $this->GetDigits($LoanId, 4);
        /*
          Account Number
         */
        $AccountNumber = "AGFL" . $this->GetDigits($LoanId, 6);

        \App\Models\LoanHistory::where("id", $LoanId)->update(

            [
                'loan_status_id' => 10,
                'sanction_number' => $SanctionNumber,
                'account_no' => $AccountNumber,
                'kibor_rate' => $kibor_rate,
                'spread_rate' => $spread_rate,
                'takaful' => $takaful_amount,
                'total_amount' => $GrandTotal,
                'total_amount_pr' => $GrandPrinc,
                'total_amount_mu' => $GrandServ
            ]);

        /*
                $LastSeries = \App\Models\FinGeneralLedger::orderBy("id", "desc")->first();
                if (!isset($LastSeries->txn_series)) {
                    $LastSeries = 0;
                } else {
                    $LastSeries = $LastSeries->txn_series;
                }
                $NextSeries = $LastSeries + 1;

                $ProcessingFees = $GrandPrinc * 1.5 / 100;
                if ($fed_amount) {
                    $FED = $ProcessingFees * 13 / 100;
                } else {
                    $FED = 0;
                }
                $TakafulFees = $GrandPrinc * 0.8 / 100;
                $BankPayment = $GrandPrinc - ($ProcessingFees + $TakafulFees);

        //        Loan Processing fee income and FED payable
                //First Entry ()
                $Model_GL = \App\Models\FinGeneralLedger::create([
                    'slip_id' => 1, 'loan_id' => $LoanId, 'user_id' => 1, 'details' => 'Disbursement Voucher - Loan Processing fee income and FED payable', 'debit' => $GrandPrinc, 'credit' => $GrandPrinc, 'txn_date' => date('Y-m-d'), 'txn_type' => 1, 'txn_series' => $NextSeries++, 'office_id' => 1
                ]);
                $FinGL_Id = $Model_GL->id;
                //JS Bank
                \App\Models\FinGeneralLedgerDetail::create(['fin_gen_id' => $FinGL_Id, 'coa_id' => '172', 'debit' => ($ProcessingFees + $FED), 'credit' => 0]);

                //Processing Fees
                \App\Models\FinGeneralLedgerDetail::create(['fin_gen_id' => $FinGL_Id, 'coa_id' => '336', 'debit' => 0, 'credit' => $ProcessingFees]);
                if ($FED) {
                    //FED Fees
                    \App\Models\FinGeneralLedgerDetail::create(['fin_gen_id' => $FinGL_Id, 'coa_id' => '216', 'debit' => 0, 'credit' => $FED]);

                    $Model_GL = \App\Models\FinGeneralLedger::create([
                        'slip_id' => 1, 'loan_id' => $LoanId, 'user_id' => 1, 'details' => 'payment of FED on loan processing fee', 'debit' => $FED, 'credit' => $FED, 'txn_date' => date('Y-m-d'), 'txn_type' => 1, 'txn_series' => $NextSeries++, 'office_id' => 1
                    ]);
                    $FinGL_Id = $Model_GL->id;

                    //JS Bank
                    \App\Models\FinGeneralLedgerDetail::create(['fin_gen_id' => $FinGL_Id, 'coa_id' => '172', 'debit' => 0, 'credit' => $FED]);
                    //FED Fees
                    \App\Models\FinGeneralLedgerDetail::create(['fin_gen_id' => $FinGL_Id, 'coa_id' => '216', 'debit' => $FED, 'credit' => 0]);
                }





                //First Entry ()
                $Model_GL = \App\Models\FinGeneralLedger::create([
                    'slip_id' => 1, 'loan_id' => $LoanId, 'user_id' => 1, 'details' => 'Disbursement Voucher', 'debit' => $GrandPrinc, 'credit' => $GrandPrinc, 'txn_date' => date('Y-m-d'), 'txn_type' => 1, 'txn_series' => $NextSeries++, 'office_id' => 1
                ]);
                $FinGL_Id = $Model_GL->id;
                //Lendings To Financial Institutions
                \App\Models\FinGeneralLedgerDetail::create(['fin_gen_id' => $FinGL_Id, 'coa_id' => '147', 'debit' => $GrandPrinc, 'credit' => '0']);
                //JS Bank
                \App\Models\FinGeneralLedgerDetail::create(['fin_gen_id' => $FinGL_Id, 'coa_id' => '172', 'debit' => 0, 'credit' => $GrandPrinc]);

                //Second Entry (Takaful)
                if ($TakafulFees) {
                    //Takaful first
                    $Model_GL = \App\Models\FinGeneralLedger::create([
                        'slip_id' => 1, 'loan_id' => $LoanId, 'user_id' => 1, 'details' => 'Takaful Voucher - Payment Received From Borrower', 'debit' => $GrandPrinc, 'credit' => $GrandPrinc, 'txn_date' => date('Y-m-d'), 'txn_type' => 1, 'txn_series' => $NextSeries++, 'office_id' => 1
                    ]);
                    $FinGL_Id = $Model_GL->id;
                    \App\Models\FinGeneralLedgerDetail::create(['fin_gen_id' => $FinGL_Id, 'coa_id' => '187', 'debit' => 0, 'credit' => $TakafulFees]);
                    //JS Bank
                    \App\Models\FinGeneralLedgerDetail::create(['fin_gen_id' => $FinGL_Id, 'coa_id' => '172', 'debit' => $TakafulFees, 'credit' => 0]);

                    //Takaful second
                    $Model_GL = \App\Models\FinGeneralLedger::create([
                        'slip_id' => 1, 'loan_id' => $LoanId, 'user_id' => 1, 'details' => 'Takaful Voucher - Payment to Takaful Company', 'debit' => $GrandPrinc, 'credit' => $GrandPrinc, 'txn_date' => date('Y-m-d'), 'txn_type' => 1, 'txn_series' => $NextSeries++, 'office_id' => 1
                    ]);
                    $FinGL_Id = $Model_GL->id;
                    \App\Models\FinGeneralLedgerDetail::create(['fin_gen_id' => $FinGL_Id, 'coa_id' => '187', 'debit' => $TakafulFees, 'credit' => 0]);
                    //JS Bank
                    \App\Models\FinGeneralLedgerDetail::create(['fin_gen_id' => $FinGL_Id, 'coa_id' => '172', 'debit' => 0, 'credit' => $TakafulFees]);
                }
                //Processing
                \App\Models\FinGeneralLedgerDetail::create(['fin_gen_id' => $FinGL_Id, 'coa_id' => '337', 'debit' => 0, 'credit' => $ProcessingFees]);
        */
        $dLoanBalance = $GrandServ + $GrandPrinc;

        $Return .= "<tr>"
            . "<td colspan='3'>Total ($GrandDays Days)</td>"
            . "<td align='right'>$GrandPrinc</td>"
            . "<td align='right'>$GrandServ</td>"
            . "<td align='right'>$GrandTakaful</td>"
            . "<td align='right'>$GrandTotal</td>"
            . "<td align='right'>$ApprovedLoanAmount</td>"
            . "</tr>";
        $Return .= "</table>";
        return 1;
    }
}