<?php

namespace App\Helpers;
use DateTime;
class ScheduleGenerator
{

    private $LoanId;

    public function __constructor($LoanId)
    {
        $this->LoanId = $LoanId;
    }
    private function GetDigits($i, $zeroes)
    {
        // 1 - 5
        $ret = '';
        $num = mb_strlen($i);
        $Net = $zeroes - $num;
        for ($j = 0; $j < $Net; $j++) {
            $ret .= "0";
        }
        return $ret . $i;
    }

    function calPMT($apr, $term, $loan)
    {
        $term = $term * 12;
        $apr = $apr / 1200;
        $amount = $apr * -$loan * pow((1 + $apr), $term) / (1 - pow((1 + $apr), $term));
        return round($amount);
    }

    function GenerateType($type)
    {
        switch (type) {
            case 'disburse':

                break;
            case 'kibor':
                break;
            case 'partial':
                break;
            case 'reschedule':
                break;
            case 'enhance':
                break;
            case 'payoff':
                break;
            default:
                die('Incorrect Operation');
        }
    }

    //Disbusement
    function GenerateSchedule_Disburse($LoanId, $LoanFees, $KiborNew, $debug = false, $is_islamic)
    {

        $takaful_amount = 1;
        $fed_amount = $LoanFees->fedFees;
        $kibor_rate = $KiborNew['kibor_rate'];
        $spread_rate = $KiborNew['spread_rate'];

        $data = \App\Models\LoanHistory::find($LoanId);
        $amount_pr = $data['total_amount_pr'];
        $disb_date = $data['disb_date'];
        $rep_start_date = $data['rep_start_date'];
        $loan_type_id = $data['loan_type_id'];
        $disb_day = date("d", strtotime($disb_date));
        $kibor_rate = $data['kibor_rate'];
        $spread_rate = $data['spread_rate'];
        $musharakah_date = $data['musharakah_date'];
        $kibor_revision_cycle = $data['kibor_revision_cycle'];
        $exactRenewalDate = $kibor_revision_cycle * 12;

        if ($disb_day >= 2 && $disb_day <= 5) {
            //            $rep_start_date = date("Y-m", strtotime($rep_start_date . "+1 month")) . "-05";
            $rep_start_date = date("Y-m", strtotime($rep_start_date)) . "-05";
        }
        if ($disb_day >= 6 && $disb_day <= 10) {
            //            $rep_start_date = date("Y-m", strtotime($rep_start_date . "+1 month")) . "-10";
            $rep_start_date = date("Y-m", strtotime($rep_start_date)) . "-10";
        }
        if ($disb_day >= 11 && $disb_day <= 15) {
            //            $rep_start_date = date("Y-m", strtotime($rep_start_date . "+1 month")) . "-15";
            $rep_start_date = date("Y-m", strtotime($rep_start_date)) . "-15";
        }
        if ($disb_day >= 16 && $disb_day <= 20) {
            //            $rep_start_date = date("Y-m", strtotime($rep_start_date . "+1 month")) . "-20";
            $rep_start_date = date("Y-m", strtotime($rep_start_date)) . "-20";
        }
        if ($disb_day >= 21 && $disb_day <= 25) {
            //            $rep_start_date = date("Y-m", strtotime($rep_start_date . "+1 month")) . "-25";
            $rep_start_date = date("Y-m", strtotime($rep_start_date)) . "-25";
        }
        if ($disb_day >= 26 && $disb_day <= 1) {
            //            $rep_start_date = date("Y-m", strtotime($rep_start_date . "+1 month")) . "-01";
            $rep_start_date = date("Y-m", strtotime($rep_start_date)) . "-01";
        }
        //   
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

        $DueData = \App\Models\LoanPaymentDue::where(['loan_id' => $LoanId])->exists();

        if ($DueData && !$debug) {
            return 0;
        }

        $DayRepStart = date("d", strtotime($RepStartDate));

        $RepStartDate = date("Y-m", strtotime($RepStartDate)) . "-" . $DayRepStart;

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
            8 => 1
        );

        $SchedLoanTerm = $LoanTerm;
        $FormulaloanTerm = $LoanTerm / 12;
        $modeOfP = $arryModeOfPaymentCalc[$iLoanFrequency];

        $fv = 0;
        $pv = $ApprovedLoanAmount;
        $rate = ($SrChargeRate / 100) / 360 * 30;
        $nper = $LoanTerm;
        $type = 0;

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
        $Final = $ApprovedLoanAmount / $Ffth;

        $TotalSrCharge = $ApprovedLoanAmount * ($SrChargeRate / 100);
        $DailySrcCharge = $TotalSrCharge / 365;
        $TotalDaysLoanTerms = $SchedLoanTerm * (365 / 12);

        $laststartdate = 0;
        for ($i = 1; $i <= ($SchedLoanTerm / $LoanFrequency); $i++) {

            $AdditionalServiceCharges = 0;
            if ($i == 1) {
                $Dev_ScheduleDate = $RepStartDate;
            } else {
                $date = new DateTime(date("Y-m-d", strtotime($Dev_ScheduleDate)));
                $date->modify('+' . $LoanFrequency . ' month');
                $Dev_ScheduleDate = $date->format('Y-m-d');
            }
            $MonthlyServiceCharge = $ApprovedLoanAmount * ($rate / 100 / 12);

            if ($loan_type_id != 1) {
                if ($i > 12) {
                    if ($i == 13) {
                        $Snd = pow((1 + $Fst), ($modeOfP * ($FormulaloanTerm - 1)));
                        $Trd = 1 / $Snd;
                        $Fth = 1 - $Trd;
                        $Ffth = $Fth / $Fst;
                        $Final = $ApprovedLoanAmount / $Ffth;
                    }
                    $MonthlyPrinciple = $Final - $MonthlyServiceCharge;
                } else {
                    if ($is_islamic == 1) {
                        $MonthlyPrinciple = $Final - $MonthlyServiceCharge;
                    } else {
                        $MonthlyPrinciple = 0;
                    }
                }
            } else {
                $MonthlyPrinciple = $Final - $MonthlyServiceCharge;
            }

            $MonthlyServiceCharge += $AdditionalServiceCharges;
            $ApprovedLoanAmount -= $MonthlyPrinciple;
            if ($takaful_amount) {
                $lastTakaful = \App\Models\LoanTakaful::where(['loan_id' => $LoanId, 'type' => 0])->orderBy('id', 'desc')->first();
                $lastTakafulLife = \App\Models\LoanTakaful::where(['loan_id' => $LoanId, 'type' => 1])->orderBy('id', 'desc')->first();

                if ($i == 1) {
                    $covered_amount = $amount_pr;
                } else {
                    $covered_amount = $ApprovedLoanAmount + $MonthlyPrinciple;
                }
                if (($i == 1 || $i % 12 == 0 /* || ( (($SchedLoanTerm / $LoanFrequency)==$i) ) */) && $ApprovedLoanAmount >= 500) {


                    if ($laststartdate) {
                        $startDate = $laststartdate;
                    } else {
                        $startDate = $disb_date;
                    }
                    $laststartdate = date('Y-m-d', strtotime($startDate . "+12 month "));

                    $endDate = date('Y-m-d', strtotime($startDate . "+12 month "));
                    $endDate = date('Y-m-d', strtotime($endDate . "-1 day"));

                    if ($debug) {
                        echo ($i + 13) . "==" . ($SchedLoanTerm / $LoanFrequency) . "<br>";
                        echo "i is: " . $i . " remainder: " . ($i % 13) . " - Amount: " . $ApprovedLoanAmount . " - enddate: " . $endDate . "<br><br>";
                    }
                    $renewalDate = date("Y-m-d", strtotime($endDate . "+1 day"));
                    $property_array = [
                        'loan_id' => $LoanId,
                        'type' => '0',
                        'covered_amount' => $covered_amount,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'renewal_date' => $renewalDate
                    ];
                    \App\Models\LoanTakaful::create($property_array);
                    $life_array = [
                        'loan_id' => $LoanId,
                        'type' => '1',
                        'covered_amount' => $covered_amount,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'renewal_date' => $renewalDate
                    ];
                    \App\Models\LoanTakaful::create($life_array);
                }
            }

            $MonthlyTakaful = 0;

            if ($i == ($SchedLoanTerm / $LoanFrequency)) {


                $tak_zero = \App\Models\LoanTakaful::where(['loan_id' => $LoanId, 'type' => 0])->orderBy('id', 'desc')->first();
                $tak_one = \App\Models\LoanTakaful::where(['loan_id' => $LoanId, 'type' => 1])->orderBy('id', 'desc')->first();
                \App\Models\LoanTakaful::whereIn('id', array($tak_one->id, $tak_zero->id))->update(['end_date' => date('Y-m-d', strtotime($Dev_ScheduleDate))]);
                if ($debug) {

                    echo "id1: " . $tak_one->id . " - id2: " . $tak_zero->id . "<br><br>";
                }
                if ($ApprovedLoanAmount <> 0) {
                    $MonthlyPrinciple += $ApprovedLoanAmount;

                }

            }


            $Total = $MonthlyPrinciple + $MonthlyServiceCharge + $MonthlyTakaful;

            $GrandPrinc += $MonthlyPrinciple;
            $GrandServ += $MonthlyServiceCharge;
            $GrandTotal += $Total;
            $GrandTakaful += $MonthlyTakaful;

            $sScheduledRepaymentDate = date("M j, Y", strtotime($Dev_ScheduleDate));
            $sScheduledDay = date('D', strtotime($sScheduledRepaymentDate));
            $sScheduledDate = date('d', strtotime($sScheduledRepaymentDate));

            $MysqlScheduleDate = date("Y-m-d", strtotime($sScheduledRepaymentDate));

            if (!$debug) {
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

                if (($i == 1 || $i == ($exactRenewalDate)) && ($LoanTerm > $i)) {
                    if ($i == 1) {
                        \App\Models\LoanKiborHistory::create([
                            'loan_id' => $LoanId,
                            'installment_no' => $i,
                            'kibor_rate' => $kibor_rate,
                            'start_date' => $musharakah_date,
                            'status' => 1
                        ]);
                    } else {
                        $exactRenewalDate += $kibor_revision_cycle * 12;
                        \App\Models\LoanKiborHistory::create([
                            'loan_id' => $LoanId,
                            'installment_no' => $i,
                            'kibor_rate' => 0,
                            'start_date' => $MysqlScheduleDate,
                            'status' => 0
                        ]);
                    }
                }
            }
            $Return .= "<tr>"
                . "<td>$i</td>"
                . "<td>$MysqlScheduleDate</td>"
                . "<td align='right'>0</td>"
                . "<td align='right'>" . (number_format($MonthlyPrinciple, 0)) . "</td>"
                . "<td align='right'>" . (number_format($MonthlyServiceCharge, 0)) . "</td>"
                . "<td align='right'>" . (number_format($MonthlyTakaful, 0)) . "</td>"
                . "<td align='right'>" . (number_format($Total, 0)) . "</td>"
                . "<td align='right'>" . (number_format($ApprovedLoanAmount, 0)) . "</td>"
                . "</tr>";
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

        if (!$debug) {
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
                ]
            );
        }

        $dLoanBalance = $GrandServ + $GrandPrinc;

        $Return .= "<tr>"
            . "<td colspan='3'>Total ($GrandDays Days)</td>"
            . "<td align='right'>" . (number_format($GrandPrinc, 0)) . " </td>"
            . "<td align='right'>" . (number_format($GrandServ, 0)) . "</td>"
            . "<td align='right'>" . (number_format($GrandTakaful, 0)) . "</td>"
            . "<td align='right'>" . (number_format($GrandTotal, 0)) . "</td>"
            . "<td align='right'>" . (number_format($ApprovedLoanAmount, 0)) . "</td>"
            . "</tr>";
        $Return .= "</table>";
        if ($debug) {

            return $Return;
        } else {
            return 1;

        }

    }

    //UMI Shadule 
    public function GenerateSchedule_UMI($LoanId, $LoanFees, $KiborNew, $debug = false, $is_islamic) {
        $kibor_rate = $KiborNew['kibor_rate'];
        $spread_rate = $KiborNew['spread_rate'];
    
        // Fetch loan data
        $data = \App\Models\LoanHistory::find($LoanId);
        $amount_pr = $data['total_amount_pr']; // Principal loan amount
        $tenure = $data['loan_period']; // Loan period in months
        $unit_amount = floor($amount_pr / $tenure); // Fixed unit amount, rounded down
        $outstanding_amount = $amount_pr; // Initial outstanding amount
        $total_rate = $kibor_rate + $spread_rate; // Total rate (KIBOR + spread)
    
        $schedule_date = $data['rep_start_date']; // Start date for the schedule
        $loan_frequency = 1; // Assuming a monthly schedule, adjust as needed
        $total_takaful = 0; // Assuming takaful is 0 unless specified
    
        // Check if there are already existing schedules for this loan
        $DueData = \App\Models\LoanPaymentDue::where('loan_id', $LoanId)->exists();
        if ($DueData && !$debug) {
            return 0; // Schedule already exists, so return or handle accordingly
        }
    
        // Initialize variables for summary totals
        $GrandPrinc = $GrandServ = $GrandTotal = $GrandTakaful = 0;
        $exactRenewalDate = $data['kibor_revision_cycle'] * 12; // Calculate exact renewal date for Kibor
    
        // Calculate total principal sum to adjust in the last installment
        $total_principal_allocated = 0;
    
        for ($month = 1; $month <= $tenure; $month++) {
            // Calculate monthly profit based on outstanding amount
            $profit_amount = floor(($outstanding_amount * $total_rate) / 100 / 12); // Monthly profit, rounded down
    
            // Calculate the total installment amount
            $installment_amount = $unit_amount + $profit_amount + $total_takaful;
    
            // If this is the last installment, adjust the principal and outstanding amount
            if ($month == $tenure) {
                $unit_amount = $amount_pr - $total_principal_allocated; // Adjust principal in the last installment
                $installment_amount = $unit_amount + $profit_amount + $total_takaful; // Adjust total installment
                $outstanding_amount = 0; // Set outstanding amount to zero in the last installment
            } else {
                // Reduce the outstanding amount by the unit amount for other installments
                $outstanding_amount -= $unit_amount;
            }
    
            // Format schedule date for the installment
            $schedule_date = date("Y-m-d", strtotime("+$loan_frequency month", strtotime($schedule_date)));
    
            // Store the UMI schedule data into the LoanPaymentDue table
            if (!$debug) {
                \App\Models\LoanPaymentDue::create([
                    'loan_id' => $LoanId,
                    'installment_no' => $month,
                    'due_date' => $schedule_date,
                    'amount_total' => $installment_amount,
                    'amount_pr' => $unit_amount,
                    'outstanding' => $outstanding_amount,
                    'amount_mu' => $profit_amount,
                    'amount_takaful' => $total_takaful
                ]);
    
                // Handle Kibor revision and renewal cycle
                if (($month == 1 || $month == $exactRenewalDate) && ($tenure > $month)) {
                    if ($month == 1) {
                        \App\Models\LoanKiborHistory::create([
                            'loan_id' => $LoanId,
                            'installment_no' => $month,
                            'kibor_rate' => $kibor_rate,
                            'start_date' => $data['musharakah_date'],
                            'status' => 1
                        ]);
                    } else {
                        $exactRenewalDate += $data['kibor_revision_cycle'] * 12;
                        \App\Models\LoanKiborHistory::create([
                            'loan_id' => $LoanId,
                            'installment_no' => $month,
                            'kibor_rate' => 0,
                            'start_date' => $schedule_date,
                            'status' => 0
                        ]);
                    }
                }
            }
    
            // Update total summary fields
            $GrandPrinc += $unit_amount;
            $GrandServ += $profit_amount;
            $GrandTotal += $installment_amount;
            $GrandTakaful += $total_takaful;
    
            // Update the total principal allocated
            $total_principal_allocated += $unit_amount;
        }
    
        // Update LoanHistory table with total amounts and sanction number (if not in debug mode)
        if (!$debug) {
            $BranchId = $this->GetDigits($data->office_id, 2);
            $BorrowerId = $data->borrower_id;
            $LoanTypeId = $data->loan_type_id;
            $LoanType = \App\Models\LoanType::where('id', $LoanTypeId)->first();
            $LoanTypeCode = $LoanType->code;
            $SanctionNumber = $LoanTypeCode . "-" . $this->GetDigits($BranchId, 2) . "-" . $this->GetDigits($LoanId, 4);
            $AccountNumber = "AGFL" . $this->GetDigits($LoanId, 6);
    
            \App\Models\LoanHistory::where("id", $LoanId)->update([
                'loan_status_id' => 10,
                'sanction_number' => $SanctionNumber,
                'account_no' => $AccountNumber,
                'kibor_rate' => $kibor_rate,
                'spread_rate' => $spread_rate,
                'takaful' => $total_takaful,
                'total_amount' => $GrandTotal,
                'total_amount_pr' => $GrandPrinc,
                'total_amount_mu' => $GrandServ
            ]);
        }
    
        // Return success message or result
        return 1;
    }
    



    //Partial Payment
    function GenerateSchedule_Partial($LoanId, $oustanding, $disb_date, $loan_period, $data, $debug = false)
    {
        //        
//        print_r($fetchdata);
//        die;

        $takaful_amount = 0;
        $fed_amount = 0;
        $kibor_rate = 0;
        $spread_rate = 0;

        //dd($data);
        $data = \App\Models\LoanHistory::find($LoanId);

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
        //        $loan_period = $data['loan_period'];

        $ChequeDate = $disb_date;
        $ApprovedLoanAmount = round($oustanding); //$amount_pr;
//        $SrChargeRate = $markup_rate;
        $SrChargeRate = $data['kibor_rate'] + $data['spread_rate'];
        $LoanFrequency = $loan_freq;
        $LoanTerm = $loan_period;
        $RepStartDate = $rep_start_date;

        $DueData = 0; //\App\Models\LoanPaymentDue::where(array('loan_id' => $LoanId))->exists();

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
            8 => 1
        );

        $SchedLoanTerm = $LoanTerm;
        $FormulaloanTerm = $LoanTerm / 12;
        $modeOfP = $arryModeOfPaymentCalc[$iLoanFrequency];

        $fv = 0;
        $pv = $ApprovedLoanAmount;
        $rate = ($SrChargeRate / 100) / 360 * 30;
        $nper = $LoanTerm;
        $type = 0;

        $PMT = ((0 - $pv * pow(1 + $rate, $nper)) /
            (1 + $rate) /
            ((pow(1 + $rate, $nper) - 1) / $rate)) * -1;

        $rate = 16;
        $rate = $kibor_rate + $spread_rate;
        //echo $this->calPMT($rate, 7, $ApprovedLoanAmount);
        //dd();        
        //dd(round($PMT,6));
        //dd($PMT);
        $Fst = ($SrChargeRate / $modeOfP) / 100;
        $Snd = pow((1 + $Fst), ($modeOfP * $FormulaloanTerm));
        $Trd = 1 / $Snd;
        $Fth = 1 - $Trd;
        //$Ffth = round($Fth / $Fst, 2);
        //$Final = round($ApprovedLoanAmount / $Ffth);
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


            $MonthlyPrinciple = $Final - $MonthlyServiceCharge;
            $MonthlyPrinciple = round($MonthlyPrinciple);

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
//                    \App\Models\LoanTakaful::create($property_array);
                    $life_array = [
                        'loan_id' => $LoanId,
                        'type' => '1',
                        'covered_amount' => $ApprovedLoanAmount,
                        'start_date' => $Dev_ScheduleDate,
                        'end_date' => $endDate,
                        'renewal_date' => $renewalDate
                    ];
                    //\App\Models\LoanTakaful::create($life_array);
                }
            }
            $ApprovedLoanAmount -= $MonthlyPrinciple;

            $DaysDiff = $difference->days;

            $MonthlyPrinciple = round($MonthlyPrinciple);
            $MonthlyServiceCharge = round($MonthlyServiceCharge);
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

            $DueArray = [
                'loan_id' => $LoanId,
                'installment_no' => $i,
                'due_date' => $MysqlScheduleDate,
                'amount_total' => $Total,
                'amount_pr' => $MonthlyPrinciple,
                'outstanding' => $ApprovedLoanAmount,
                'amount_mu' => $MonthlyServiceCharge,
                'amount_takaful' => $MonthlyTakaful
            ];

            if (!$debug)
                \App\Models\LoanPaymentDue::create($DueArray);

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
        //        \App\Models\LoanHistory::find($LoanId)->update(
//                [
//                    'loan_status_id' => 10,
//                    'kibor_rate' => $kibor_rate,
//                    'spread_rate' => $spread_rate,
//                    'takaful' => $takaful_amount,
//                    'total_amount' => $GrandTotal,
//                    'total_amount_pr' => $GrandPrinc,
//                    'total_amount_mu' => $GrandServ
//        ]);

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

        /*
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
        return $Return;
    }

    //Rescheduling / Enhancement
    function GenerateSchedule_Reschedule($LoanId, $params = [], $id, $debug = false)
    {

        $data = \App\Models\LoanHistory::find($LoanId);
        $loan_period = $data['loan_period'];
        if ($params) {
            //Rescheduling
            $method = $params['method'];
            $maturity_date = $params['maturity_date'];
            $DueData = \App\Models\LoanPaymentDue::where(['loan_id' => $LoanId, 'payment_status' => 1])->orderBy('id', 'desc')->first();
            if ($DueData) {
                $oustanding = $DueData['outstanding'];
                $disb_date = $DueData['due_date'];
                $rep_start_date = $this->getRepStartDate($disb_date);
                $loan_period = $this->getDateDiff($disb_date, $maturity_date);
            } else {
                $oustanding = $data['total_amount_pr'];
                $disb_date = $data['disb_date'];
                $rep_start_date = $data['rep_start_date'];
                $loan_period = $this->getDateDiff($disb_date, $maturity_date);
            }
            //            \App\Models\LoanPaymentDue::where(['loan_id' => $LoanId, 'payment_status' => 0])->delete();
        } else {
            //Enhancement
            $DueData = \App\Models\LoanPaymentDue::where('id', $id)->first();
            $oustanding = ($DueData['outstanding'] + $DueData['enhancement_amount']);
            $installment_no = $DueData['installment_no'];
            $loan_period = $loan_period - $installment_no;
            $disb_date = $DueData['due_date'];
            $rep_start_date = $this->getRepStartDate($disb_date);
            //            \App\Models\LoanPaymentDue::where('id','>',$id)->delete();
        }

        //        dd($oustanding);
        $loan_freq = $data['loan_frequency'];
        $ChequeDate = $disb_date;

        $ApprovedLoanAmount = round($oustanding); //$amount_pr;
        $SrChargeRate = $data['kibor_rate'] + $data['spread_rate'];
        $LoanFrequency = $loan_freq;
        $LoanTerm = $loan_period;
        $RepStartDate = $rep_start_date;
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
            8 => 1
        );

        $SchedLoanTerm = $LoanTerm;
        $FormulaloanTerm = $LoanTerm / 12;
        $modeOfP = $arryModeOfPaymentCalc[$iLoanFrequency];

        $fv = 0;
        $pv = $ApprovedLoanAmount;
        $rate = ($SrChargeRate / 100) / 360 * 30;
        $nper = $LoanTerm;
        $type = 0;

        $PMT = ((0 - $pv * pow(1 + $rate, $nper)) /
            (1 + $rate) /
            ((pow(1 + $rate, $nper) - 1) / $rate)) * -1;

        $Fst = ($SrChargeRate / $modeOfP) / 100;
        $Snd = pow((1 + $Fst), ($modeOfP * $FormulaloanTerm));
        $Trd = 1 / $Snd;
        $Fth = 1 - $Trd;
        $Ffth = $Fth / $Fst;
        $Final = round($ApprovedLoanAmount / $Ffth);

        $TotalSrCharge = $ApprovedLoanAmount * ($SrChargeRate / 100);
        $DailySrcCharge = $TotalSrCharge / 365;
        $TotalDaysLoanTerms = $SchedLoanTerm * (365 / 12);

        for ($i = 1; $i <= ($SchedLoanTerm / $LoanFrequency); $i++) {

            $AdditionalServiceCharges = 0;
            if ($i == 1) {
                $Dev_ScheduleDate = $RepStartDate;
                $datetime_chq = new DateTime(date("Y-m-d", strtotime($ChequeDate)));
                $datetime_repstart = new DateTime(date("Y-m-d", strtotime($RepStartDate)));
                $difference = $datetime_chq->diff($datetime_repstart);
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


            $MonthlyPrinciple = $Final - $MonthlyServiceCharge;
            $MonthlyPrinciple = round($MonthlyPrinciple);

            $MonthlyServiceCharge += $AdditionalServiceCharges;

            if (isset($takaful_amount) && $takaful_amount) {
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
                    $life_array = [
                        'loan_id' => $LoanId,
                        'type' => '1',
                        'covered_amount' => $ApprovedLoanAmount,
                        'start_date' => $Dev_ScheduleDate,
                        'end_date' => $endDate,
                        'renewal_date' => $renewalDate
                    ];
                    //\App\Models\LoanTakaful::create($life_array);
                }
            }
            $ApprovedLoanAmount -= $MonthlyPrinciple;

            $DaysDiff = $difference->days;

            $MonthlyPrinciple = round($MonthlyPrinciple);
            $MonthlyServiceCharge = round($MonthlyServiceCharge);
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

            $DueArray = [
                'loan_id' => $LoanId,
                'installment_no' => $i,
                'due_date' => $MysqlScheduleDate,
                'amount_total' => $Total,
                'amount_pr' => $MonthlyPrinciple,
                'outstanding' => $ApprovedLoanAmount,
                'amount_mu' => $MonthlyServiceCharge,
                'amount_takaful' => $MonthlyTakaful
            ];

            if (!$debug)
                \App\Models\LoanPaymentDue::create($DueArray);

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
        //        \App\Models\LoanHistory::find($LoanId)->update(
//                [
//                    'loan_status_id' => 10,
//                    'kibor_rate' => $kibor_rate,
//                    'spread_rate' => $spread_rate,
//                    'takaful' => $takaful_amount,
//                    'total_amount' => $GrandTotal,
//                    'total_amount_pr' => $GrandPrinc,
//                    'total_amount_mu' => $GrandServ
//        ]);

        //        $LastSeries = \App\Models\FinGeneralLedger::orderBy("id", "desc")->first();
//        if (!isset($LastSeries->txn_series)) {
//            $LastSeries = 0;
//        } else {
//            $LastSeries = $LastSeries->txn_series;
//        }
//        $NextSeries = $LastSeries + 1;
//
//        $ProcessingFees = $GrandPrinc * 1.5 / 100;
//        if ($fed_amount) {
//            $FED = $ProcessingFees * 13 / 100;
//        } else {
//            $FED = 0;
//        }
//        $TakafulFees = $GrandPrinc * 0.8 / 100;
//        $BankPayment = $GrandPrinc - ($ProcessingFees + $TakafulFees);

        /*
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
        return $Return;
    }

    //Early Settlement / Payoff
    function GenerateSchedule_Payoff($loanId)
    {
        $LoansInfo = \App\Models\LoanHistory::where('id', $loanId)->first();

        $OutstandingData = \App\Models\LoanPaymentDue::where(['loan_id' => $loanId, 'payment_status' => 1])->orderBy('id', 'desc')->first();
        if (!$OutstandingData) {
            $OutstandingData = \App\Models\LoanPaymentDue::where(['loan_id' => $loanId, 'payment_status' => 0])->first();
            $outstanding = $LoansInfo->total_amount_pr;
        } else {
            $outstanding = $OutstandingData->outstanding;
        }

        if ($OutstandingData) {
            $due_date = $OutstandingData->due_date;
            $installment_no = $OutstandingData->installment_no;
            //$outstanding = $OutstandingData->outstanding;
            if ($installment_no <= 12) {
                $charges = 4.5;
            } else if ($installment_no <= 24) {
                $charges = 3;
            } else if ($installment_no <= 36) {
                $charges = 1.5;
            } else {
                $charges = 0;
            }
            $date = date("Y-m-d"); //"2023-09-13";
            $now = strtotime($date); //time(); // or your date as well
            $your_date = strtotime($due_date);
            $datediff = $now - $your_date;
            if ($datediff < 0) {
                $datediff = $datediff * -1;
            }


            $days_diff = round($datediff / (60 * 60 * 24));
            //$days_diff=14;
            //echo "Settlement Outstanding: ".$outstanding."<br>";
            $Profit = $outstanding * (($LoansInfo->kibor_rate + $LoansInfo->spread_rate) / 100) / 360 * $days_diff;
            //echo "Profit for ".$days_diff." days: ".($Profit)."<br>";
            $SettlementCharges = $outstanding * ($charges / 100);
            //echo "Settlement Charges: ".($SettlementCharges)."<br>";
            $FED = $SettlementCharges * (13 / 100);
            //echo "FED on Settlement Charges: ".($FED)."<br>";
            $TotalSettlement = $outstanding + $Profit + $SettlementCharges + $FED;
            $TotalSettlement = round($TotalSettlement);
            //echo "Total Settlement Amount: ".($TotalSettlement)."<br>";

            $d['loanId'] = $loanId;
            $d['outstanding'] = $outstanding;
            $d['days_diff'] = $days_diff;
            $d['Profit'] = round($Profit);
            $d['SettlementCharges'] = round($SettlementCharges);
            $d['FED'] = round($FED);
            $d['TotalSettlement'] = round($TotalSettlement);

            return view('loan-payment-recovered.earlysettle', $d);
        }
        /*
          1st Year	4.50%
          2nd Year	3%
          3rd Year	1.50%
          4th Year onwards	0%
          Pay off date	01-Mar-23

         */
        /*
          Settlement Amount
          Outstanding Principal	 9,957,847
          Profit for 14 days (Feb 16 - Marc 1)	 65,832
          Settlement Charges	 448,103 	(because of 1st year)
          FED on Settlement Charges	 58,253
          Total Settlement Amount	 10,530,036

         */
    }

    //Kibor Renewal
    function GenerateSchedule_KiborRenewal($kibor_rate, $row, $debug = false)
    {
        $debug = true;
        //        $LoanId = $this->LoanId;
        //$kibor_hist_id = $row['id'];
        $inst_no = $row['installment_no'];
        $LoanId = $row['loan_id'];
        $due_kiborrenew_date = $row['kibor_date'];
        $data = \App\Models\LoanHistory::find($LoanId);
        $spread_rate = $data['spread_rate'];
        $amount_pr = $data['total_amount_pr'];
        $disb_date = $data['disb_date'];
        $rep_start_date = $data['rep_start_date'];
        $loan_type_id = $data['loan_type_id'];
        $disb_day = date("d", strtotime($disb_date));
        $musharakah_date = $data['musharakah_date'];
        $kibor_revision_cycle = $data['kibor_revision_cycle'];
        $exactRenewalDate = $kibor_revision_cycle * 12;



        $rep_start_date = $due_kiborrenew_date;

        $loan_freq = $data['loan_frequency'];
        $markup_rate = $data['markup_rate'];
        $loan_period = $data['loan_period'];


        $ChequeDate = $disb_date;
        $ApprovedLoanAmount = $amount_pr;
        $SrChargeRate = $markup_rate;
        $SrChargeRate = $kibor_rate + $spread_rate;
        $LoanFrequency = $loan_freq;
        $LoanTerm = $loan_period;
        $LoanTerm = $LoanTerm - $exactRenewalDate;

        $DueData = \App\Models\LoanPaymentDue::where('loan_id', $LoanId)
            ->where('due_date', '>', $due_kiborrenew_date)
            ->first();

        //        dd($DueData);
        if (!$DueData) {
            return 0;
        } else {
            $RepStartDate = $DueData['due_date'];
            $ApprovedLoanAmount = (int) ($DueData['amount_pr'] + $DueData['outstanding']);
            if (!$debug)
                \App\Models\LoanPaymentDue::where([['loan_id', '=', $LoanId], ['due_date', '>', $RepStartDate]])->delete();
        }
        $DayRepStart = date("d", strtotime($RepStartDate));
        $RepStartDate = date("Y-m", strtotime($RepStartDate)) . "-" . $DayRepStart;
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
            8 => 1
        );

        $SchedLoanTerm = $LoanTerm;
        $FormulaloanTerm = $LoanTerm / 12;
        $modeOfP = $arryModeOfPaymentCalc[$iLoanFrequency];
        $fv = 0;
        $pv = $ApprovedLoanAmount;
        $rate = ($SrChargeRate / 100) / 360 * 30;
        $nper = $LoanTerm;
        $type = 0;
        $PMT = ((0 - $pv * pow(1 + $rate, $nper)) /
            (1 + $rate) /
            ((pow(1 + $rate, $nper) - 1) / $rate)) * -1;
        $rate = $kibor_rate + $spread_rate;
        $Fst = ($SrChargeRate / $modeOfP) / 100;
        $Snd = pow((1 + $Fst), ($modeOfP * $FormulaloanTerm));
        $Trd = 1 / $Snd;
        $Fth = 1 - $Trd;
        $Ffth = $Fth / $Fst;
        $Final = $ApprovedLoanAmount / $Ffth;
        $TotalSrCharge = $ApprovedLoanAmount * ($SrChargeRate / 100);
        //echo "Final2: " . $TotalSrCharge . "<br>";
        $DailySrcCharge = $TotalSrCharge / 365;
        $TotalDaysLoanTerms = $SchedLoanTerm * (365 / 12);

        //echo "TotalDaysLoanTerms: " . $TotalDaysLoanTerms . "<br>";
        //echo "DailySrcCharge: " . $DailySrcCharge . "<br>";
        //Loop Here
        $laststartdate = 0;
        for ($i = 1; $i <= ($SchedLoanTerm / $LoanFrequency); $i++) {

            $AdditionalServiceCharges = 0;
            if ($i == 1) {
                $Dev_ScheduleDate = $RepStartDate;
            } else {
                $date = new DateTime(date("Y-m-d", strtotime($Dev_ScheduleDate)));
                $date->modify('+' . $LoanFrequency . ' month');
                $Dev_ScheduleDate = $date->format('Y-m-d');
            }
            $MonthlyServiceCharge = $ApprovedLoanAmount * ($rate / 100 / 12);
            //            $MonthlyServiceCharge = round($MonthlyServiceCharge);

            if ($loan_type_id != 1) {
                if ($i > 12) {
                    if ($i == 13) {
                        $Snd = pow((1 + $Fst), ($modeOfP * ($FormulaloanTerm - 1)));
                        $Trd = 1 / $Snd;
                        $Fth = 1 - $Trd;
                        $Ffth = $Fth / $Fst;
                        $Final = $ApprovedLoanAmount / $Ffth;
                        //$Final = ceil($Final);
                    }
                    $MonthlyPrinciple = $Final - $MonthlyServiceCharge;
                } else {
                    $MonthlyPrinciple = 0;
                }
            } else {
                $MonthlyPrinciple = $Final - $MonthlyServiceCharge;
            }

            $MonthlyServiceCharge += $AdditionalServiceCharges;
            $ApprovedLoanAmount -= $MonthlyPrinciple;
            //            if ($takaful_amount) {
//                //echo $LoanTerm."/".$i."<br>";
//                $lastTakaful = \App\Models\LoanTakaful::where(['loan_id' => $LoanId, 'type' => 0])->orderBy('id', 'desc')->first();
//                $lastTakafulLife = \App\Models\LoanTakaful::where(['loan_id' => $LoanId, 'type' => 1])->orderBy('id', 'desc')->first();
//
//                if ($i == 1) {
//                    $covered_amount = $amount_pr;
//                } else {
//                    $covered_amount = $ApprovedLoanAmount + $MonthlyPrinciple;
//                }
//                if (($i == 1 || $i % 12 == 0 /* || ( (($SchedLoanTerm / $LoanFrequency)==$i) ) */) && $ApprovedLoanAmount >= 500) {
//
//
//                    if ($laststartdate) {
//                        $startDate = $laststartdate;
//                    } else {
//                        $startDate = $disb_date;
//                    }
//                    $laststartdate = date('Y-m-d', strtotime($startDate . "+12 month "));
//
//                    $endDate = date('Y-m-d', strtotime($startDate . "+12 month "));
//                    $endDate = date('Y-m-d', strtotime($endDate . "-1 day"));
//
//                    if ($debug) {
//                        echo ($i + 13) . "==" . ($SchedLoanTerm / $LoanFrequency) . "<br>";
//                        echo "i is: " . $i . " remainder: " . ($i % 13) . " - Amount: " . $ApprovedLoanAmount . " - enddate: " . $endDate . "<br><br>";
//                    }
//                    $renewalDate = date("Y-m-d", strtotime($endDate . "+1 day"));
//                    $property_array = [
//                        'loan_id' => $LoanId,
//                        'type' => '0',
//                        'covered_amount' => $covered_amount,
//                        'start_date' => $startDate,
//                        'end_date' => $endDate,
//                        'renewal_date' => $renewalDate
//                    ];
////                    print_r($property_array);
////                    echo "<br>";
////                    if (!$debug) {
//                    \App\Models\LoanTakaful::create($property_array);
////                    }
//                    $life_array = [
//                        'loan_id' => $LoanId,
//                        'type' => '1',
//                        'covered_amount' => $covered_amount,
//                        'start_date' => $startDate,
//                        'end_date' => $endDate,
//                        'renewal_date' => $renewalDate
//                    ];
////                    if (!$debug) {
//                    \App\Models\LoanTakaful::create($life_array);
////                    }
//                }
//            }

            //            $MonthlyPrinciple = round($MonthlyPrinciple);
//            $MonthlyServiceCharge = round($MonthlyServiceCharge);
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


                if (!$debug) {
                    $tak_zero = \App\Models\LoanTakaful::where(['loan_id' => $LoanId, 'type' => 0])->orderBy('id', 'desc')->first();
                    $tak_one = \App\Models\LoanTakaful::where(['loan_id' => $LoanId, 'type' => 1])->orderBy('id', 'desc')->first();
                    \App\Models\LoanTakaful::whereIn('id', array($tak_one->id, $tak_zero->id))->update(['end_date' => date('Y-m-d', strtotime($Dev_ScheduleDate))]);
                }
                if ($debug) {

                    //echo "id1: " . $tak_one->id . " - id2: " . $tak_zero->id . "<br><br>";
                }
                if ($ApprovedLoanAmount <> 0) {
                    $MonthlyPrinciple += $ApprovedLoanAmount;
                    //$MonthlyPrinciple = ceil($MonthlyPrinciple);
//                    $ApprovedLoanAmount = 0;
//                    if ($Final < ($MonthlyPrinciple + $MonthlyServiceCharge)) {
//                        $rem = ($MonthlyPrinciple + $MonthlyServiceCharge) - $Final;
//                        $MonthlyServiceCharge -= $rem;
//                    } else if ($Final > ($MonthlyPrinciple + $MonthlyServiceCharge)) {
//                        $rem = $Final - ($MonthlyPrinciple + $MonthlyServiceCharge);
//                        $MonthlyServiceCharge += $rem;
//                    }
                }
                //                $amount_pr = \App\Models\LoanPaymentDue::where('loan_id', $LoanId)->sum('amount_pr');
//                if($pv<>($amount_pr+$MonthlyPrinciple)){
////                    echo $pv;
////                    echo "<br>";
////                    echo $amount_pr;
////                    echo "<br>";
////                    echo $MonthlyPrinciple;
////                    echo "<br>";
////                    die;
//                }
//                echo $amount_pr;
//                echo "<br>";
//                echo $MonthlyPrinciple;
//                echo "<br>";
//                die;
            }


            $Total = $MonthlyPrinciple + $MonthlyServiceCharge + $MonthlyTakaful;

            $GrandPrinc += $MonthlyPrinciple;
            $GrandServ += $MonthlyServiceCharge;
            $GrandTotal += $Total;
            $GrandTakaful += $MonthlyTakaful;

            $sScheduledRepaymentDate = date("M j, Y", strtotime($Dev_ScheduleDate));
            $sScheduledDay = date('D', strtotime($sScheduledRepaymentDate));
            $sScheduledDate = date('d', strtotime($sScheduledRepaymentDate));
            //            if ($sScheduledDay == "Sun") {
//                $dateScheduledRepaymentDate = new DateTime(date("Y-m-d", strtotime($sScheduledRepaymentDate)));
//                if ($sScheduledDate == 25) {
//                    //$dateScheduledRepaymentDate->modify('-1 day');
//                } else {
//                    //$dateScheduledRepaymentDate->modify('+1 day');
//                }
//                $sScheduledRepaymentDate = $dateScheduledRepaymentDate->format('Y-m-d');
//                $sScheduledRepaymentDate = date("M j, Y", strtotime($sScheduledRepaymentDate));
//            }
            $MysqlScheduleDate = date("Y-m-d", strtotime($sScheduledRepaymentDate));

            if (!$debug) {
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

            }
            $Return .= "<tr>"
                . "<td>$i</td>"
                . "<td>$MysqlScheduleDate</td>"
                . "<td align='right'>0</td>"
                . "<td align='right'>" . (number_format($MonthlyPrinciple, 0)) . "</td>"
                . "<td align='right'>" . (number_format($MonthlyServiceCharge, 0)) . "</td>"
                . "<td align='right'>" . (number_format($MonthlyTakaful, 0)) . "</td>"
                . "<td align='right'>" . (number_format($Total, 0)) . "</td>"
                . "<td align='right'>" . (number_format($ApprovedLoanAmount, 0)) . "</td>"
                . "</tr>";
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

        if (!$debug) {
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
                ]
            );
        }
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
            . "<td align='right'>" . (number_format($GrandPrinc, 0)) . " </td>"
            . "<td align='right'>" . (number_format($GrandServ, 0)) . "</td>"
            . "<td align='right'>" . (number_format($GrandTakaful, 0)) . "</td>"
            . "<td align='right'>" . (number_format($GrandTotal, 0)) . "</td>"
            . "<td align='right'>" . (number_format($ApprovedLoanAmount, 0)) . "</td>"
            . "</tr>";
        $Return .= "</table>";
        if ($debug) {
            return $Return;
        } else {
            return 1;
        }
    }

}
