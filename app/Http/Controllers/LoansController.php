<?php

namespace App\Http\Controllers;

use App\Models\LoanBorrower;
use Carbon\Carbon;
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
use GuzzleHttp\Client;

class LoansController extends Controller
{

    protected $tt, $my_loan_history, $exam, $year;
    protected $clsPR;

    public function __construct(LoanBorrowerRepo $tt, LoanHistoryRepo $lt, PaymentRepo $PR)
    {

        $this->tt = $tt;
        $this->my_loan_history = $lt;
        $this->clsPR = $PR;
    }

    public function authorizeuser()
    {
        //dd("hello");
        return view('lms_loans.authorize');
    }
    public function rev_payment($id)
    {
        //echo $id;

        $LoanRepayment = \App\Models\LoanPaymentRecovered::where('loan_id', $id)->orderBy("id", "desc")->first();
        if ($LoanRepayment) {
            //dd($LoanRepayment);
            $rec_id = $LoanRepayment->id;
            $rep_id = $LoanRepayment->due_id;
            \App\Models\LoanPaymentRecovered::where('id', $rec_id)->delete();
            \App\Models\LoanPaymentDue::where('id', $rep_id)->update(['payment_status' => '0']);

            return back()->with('flash_success', 'Payment has been reversed');
        } else {
            return back()->with('flash_error', 'Something went wrong, all are unpaid and nothing to reverse');
        }
    }

    function AddLoan($application_id = null)
    {
        $client = new Client();
        $loan_user = $client->get(env('LOS_URL') . 'user-applications/' . $application_id);
        $data = json_decode($loan_user->getBody(), true);
        return view('lms_loans.addloan', compact('data'));
    }

    function SaveNewLoan(Request $request)
    {
        $data = $request->all();

        $fname = $data["fname"];
        $mname = $data["mname"];
        $lname = $data["lname"];
        $cnic = $data["cnic"];
        $gender = $data["gender"];
        $dob = $data["dob"];
        $disb_amount = $data["disb_amount"];
        $ltype = $data['loan_type'];
        $loan_period = $data["loan_period"];
        $musharkah_date = $data["musharkah_date"];
        $fin_amount = $data["fin_amount"];
        $kibor_revision_cycle = $data["kibor_revision_cycle"];
        $kibor_revision_date = $data["kibor_revision_date"];

        $LoanBorrower = LoanBorrower::create([
            'fname' => $fname,
            'mname' => $mname,
            'lname' => $lname,
            'cnic' => $cnic,
            'gender' => $gender,
            'dob' => $dob
        ]);
        $BorrowerId = $LoanBorrower->id;

        $BranchId = 1;
        $BranchId = $this->GetDigits($BranchId, 2);
        $LoanTypeId = $ltype;
        $LoanType = \App\Models\LoanType::where('id', $LoanTypeId)->first();
        $LoanTypeCode = $LoanType->code;

        $office_id = $BranchId;
        $total_amount = "NULL";
        $total_amount_pr = $disb_amount;
        $total_amount_mu = "NULL";
        $takaful = 1;
        $loan_type_id = $ltype;
        $loan_period = $loan_period;
        $loan_frequency = 1;
        $loan_status_id = 2;
        $musharakah_date = $musharkah_date;
        $musharakah_status = 1;
        $property_amount = $fin_amount;
        $kibor_revision_cycle = $kibor_revision_cycle;
        $kibor_revision_date = $kibor_revision_date;

        $LoanData = \App\Models\LoanHistory::create([
            'account_no' => "AccountNumber",
            'sanction_number' => "SanctionNumber",
            'borrower_id' => $BorrowerId,
            'office_id' => 1,
            'total_amount' => null,
            'total_amount_pr' => $total_amount_pr,
            'total_amount_mu' => null,
            'takaful' => 1,
            'loan_type_id' => $loan_type_id,
            'loan_period' => $loan_period,
            'loan_frequency' => 1,
            'loan_status_id' => 2,
            'musharakah_date' => $musharakah_date,
            'musharakah_status' => 1,
            'property_amount' => $property_amount,
            'kibor_revision_cycle' => $kibor_revision_cycle,
            'kibor_revision_date' => $kibor_revision_date,
        ]);
        $LoanId = $LoanData->id;
        $SanctionNumber = $LoanTypeCode . "-" . $this->GetDigits($BranchId, 2) . "-" . $this->GetDigits($LoanId, 4);
        //        $los_app_id = $LoanTypeCode . "-" . $this->GetDigits($BranchId, 2) . "-" . $this->GetDigits($data['los_app_id'], 4);
        $AccountNumber = "AGFL" . $this->GetDigits($LoanId, 6);
        $LoanData = \App\Models\LoanHistory::where('id', $LoanId)->update([
            'account_no' => $AccountNumber,
            'sanction_number' => $SanctionNumber,
            'los_app_id' => $data['los_app_id'],
        ]);

        return $this->loandetails();
    }

    function showloan($loanId)
    {
        $d['data'] = \App\Models\LoanHistory::find($loanId)->with('loan_borrower')->first();
        $d['due'] = \App\Models\LoanPaymentDue::where(['loan_id' => $loanId, 'payment_status' => 0])->select(DB::raw("sum(amount_pr) as 'due_pr', sum(amount_mu) as 'due_mu'"))->first();
        $d['paid'] = \App\Models\LoanPaymentRecovered::where(['loan_id' => $loanId])->select(DB::raw("sum(amount_pr) as 'paid_pr', sum(amount_mu) as 'paid_mu'"))->first();
        return view('lms_loans.showloan', $d);
    }

    function Index()
    {
        $d['tt_records'] = $this->tt->getAll();

        return view('lms_loans.borrowers', $d);
    }

    function borrowers()
    {
        $d['tt_records'] = $this->tt->getAll();

        return view('lms_loans.borrowers', $d);
    }

    function AcknowledgementLetter(Request $request)
    {
        $loanId = $request->get("loanId");
        $LoanHistory = \App\Models\LoanHistory::where('id', $loanId)->first();
        $ackletter = "Acknowledgment Letter";

        $fullname = $LoanHistory->loan_borrower->fname . " " . $LoanHistory->loan_borrower->lname;
        $cnic = $LoanHistory->loan_borrower->cnic;
        return response()->download(public_path('global_assets/legal/' . $ackletter . '.docx'), $fullname . " [" . $cnic . "]" . " (" . $ackletter . ").docx");
    }

    function WelcomeLetter(Request $request)
    {
        $loanId = $request->get("loanId");
        $LoanHistory = \App\Models\LoanHistory::where('id', $loanId)->first();
        $welcomeletter = "Well Come Letter";

        $fullname = $LoanHistory->loan_borrower->fname . " " . $LoanHistory->loan_borrower->lname;
        $cnic = $LoanHistory->loan_borrower->cnic;
        return response()->download(public_path('global_assets/legal/' . $welcomeletter . '.docx'), $fullname . " [" . $cnic . "]" . " (" . $welcomeletter . ").docx");
    }

    function LegalDocuments(Request $request)
    {
        $loanId = $request->get("loanId");
        $DocId = $request->get("doc_id");
        $loantypeid = $request->get("loantypeid");

        $LoanHistory = \App\Models\LoanHistory::where('id', $loanId)->with('loan_borrower')->first();
        $client = new Client();
        $loan_user = $client->get(env('LOS_URL') . 'get-user-applications/' . $LoanHistory->los_app_id);
        $data = json_decode($loan_user->getBody(), true);
        //        dd($LoanHistory);
        //        dd($data);


        /*
         * Home Purchase 01
          Agreement to Mortgage.
          Memorandum of Title Deposit Deeds.
          Undertaking and Indemnity.
          Undertaking for the First Time Home Owner. (If Req)
          Musharaka Agreement.
          Promise to Lease.
          Payment Agreement.
          Purchase Undertaking Letter.
         */

        /*
         * Home Land and Construction 04
          Agreement to Mortgage.
          Memorandum of Title Deposit Deeds.
          Undertaking and Indemnity.
          Undertaking for the First Time Home Owner. (If Req)
          Asset Purchase Agreement. (Before 1st Trench)
          Musharaka Agreement.
          Promise to Lease.
          Payment Agreement.
          Purchase Undertaking Letter.
         */

        /*
         * Home Renovation 02
          Memorandum of Title Deposit Deeds.
          Undertaking and Indemnity.
          Asset Purchase Agreement.
          Musharaka Agreement.
          Promise to Lease.
          Payment Agreement.
          Purchase Undertaking Letter.
         */

        /*
         * Home Construction 03
          Memorandum of Title Deposit Deeds.
          Undertaking and Indemnity.
          Asset Purchase Agreement.
          Musharaka Agreement.
          Promise to Lease.
          Payment Agreement.
          Purchase Undertaking Letter.
         */

        /*
         * Balance Transfer 05
          Agreement to Mortgage.
          Memorandum of Title Deposit Deeds.
          Undertaking and Indemnity.
          Asset Purchase Agreement.
          Musharaka Agreement.
          Promise to Lease.
          Payment Agreement.
          Purchase Undertaking Letter.
         */
        $docs = [
            "Agreement to Mortgage Final",
            "Asset Purchase Agreement Finalized",
            "Memorandum of Title Deposit Deeds _Final",
            "Musharakah Agreement Finalized",
            "Payment Agreement Final",
            "Promise to Lease _Final",
            "Purchase Undertaking Finalized",
            "Undertaking and Indeminity Final",
            "Undertaking for First Time Home Owner_Final",
            "Personal Guarantee Applicant"
        ];
        $otherdocs = ["wel" => "Well Come Letter", "tax" => "Tax Letter", "ack" => "Acknowledgment Letter"];
        $welcomeletter = "Well Come Letter";

        if (\App\Models\LoanHistory::where('id', $loanId)->first()->musharakah_status == 0) {
            \App\Models\LoanHistory::where('id', $loanId)->update(['musharakah_status' => 1, 'musharakah_date' => date("Y-m-d H:i:s")]);
        }
        if (isset($docs[$DocId])) {
            $filename = $docs[$DocId];
        } else {
            $filename = $otherdocs[$DocId];
        }
        $file = public_path('global_assets/legal/' . $filename . '.docx');

        $phpword = new \PhpOffice\PhpWord\TemplateProcessor($file);
        $fullname = $LoanHistory->loan_borrower->fname . " " . $LoanHistory->loan_borrower->mname . " " . $LoanHistory->loan_borrower->lname;
        $gender = $LoanHistory->loan_borrower->gender;
        $cnic = $LoanHistory->loan_borrower->cnic;
        $address = $LoanHistory->loan_borrower->address;
        $mobile = $LoanHistory->loan_borrower->mobile;
        $guardian = $LoanHistory->loan_borrower->guardian_fname . " " . $LoanHistory->loan_borrower->guardian_mname . " " . $LoanHistory->loan_borrower->guardian_lname;
        $account_no = $LoanHistory->account_no;
        $finance_amount = $LoanHistory->total_amount_pr;
        $loan_period = $LoanHistory->loan_period;
        $disb_date = $LoanHistory->disb_date;

        $musharakah_date = $LoanHistory->musharakah_date;
        $kibor_rate = $LoanHistory->kibor_rate;
        $spread_rate = $LoanHistory->spread_rate;
        $rep_start_date = $LoanHistory->rep_start_date;
        $property_amount = $LoanHistory->property_amount;

        $DueData = \App\Models\LoanPaymentDue::where('loan_id', $loanId)->select(DB::raw("max(due_date) as 'maturity_date'"))->first();
        $maturity_date = "";
        if ($DueData && $DueData->maturity_date) {
            $maturity_date = $DueData->maturity_date;
        }

        $loan_status = \App\Models\LoanStatus::find($LoanHistory->loan_status_id, "title")->title;
        $f = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);


        $month = date("m");
        $year = date("Y");
        if ($month > 6) {
            $dateF = $year . "-07-01";
            $dateT = ($year + 1) . "-06-30";
        } else {
            $dateF = ($year - 1) . "-07-01";
            $dateT = ($year) . "-06-30";
        }
        $LoanDueDetails = \App\Models\LoanPaymentDue::where("loan_id", $loanId)
            ->whereBetween("due_date", [$dateF, $dateT])
            ->get();
        foreach ($LoanDueDetails as $row) {
        }

        //        $users = DB::connection('mysql2')->select('SELECT id,name,email,cell,isCellVerfied,isPostSignupCompleted,createdAt from users');

        $CurDate = date("d M Y");
        $LoanArray = [
            '${current_date}' => $this->nameregex($CurDate),
            '${name}' => $this->nameregex($fullname),
            '${title}' => $gender == 1 ? 'Mr' : 'Ms',
            '${offspring}' => $gender == 1 ? 'S/o' : 'D/o',
            '${guardian}' => $this->nameregex($guardian),
            '${cnic}' => $cnic,
            '${musharakh_date}' => date("d M Y", strtotime($musharakah_date)),
            '${address_cnic}' => $this->nameregex($address),
            '${address}' => $this->nameregex($address),
            '${finance_amount}' => number_format($finance_amount, 0),
            '${account_no}' => $account_no,
            '${loan_period}' => $loan_period,
            '${disb_date}' => date("d M Y", strtotime($disb_date)),
            '${mobile}' => $mobile,
            '${loan_status}' => $loan_status,
            '${co_name}' => $data['data']['application_co_borrowers'][0]['name'],
            '${co_guardian}' => $data['data']['application_co_borrowers'][0]['father_name'],
            '${co_cnic}' => $data['data']['application_co_borrowers'][0]['cnic'],
            '${co_title}' => $data['data']['application_co_borrowers'][0]['gender_id'] == 1 ? 'Mr' : 'Ms',
            '${co_offspring}' => $data['data']['application_co_borrowers'][0]['gender_id'] == 1 ? 'S/o' : 'D/o',
            '${co_address}' => '',
            '${maturity_date}' => $maturity_date ? date("d M Y", strtotime($maturity_date)) : "",
            '${address_property}' => '',
            '${finance_amount_word}' => $f->format($finance_amount),
            '${seller_name}' => '',
            '${seller_cnic}' => '',
            '${60_instalment_finance_amount}' => '',
            '${60_instalment_finance_amount_figure}' => '',
            '${list_of_documents}' => '',
            '${north}' => '',
            '${south}' => '',
            '${east}' => '',
            '${west}' => '',
            '${current_month}' => '',
            '${customer_equity}' => '',
            '${total_home_amount}' => '',
            '${customer_equity_figure}' => '',
            '${total_home_amount_figure}' => '',
            '${agfl_ratio}' => '',
            '${customer_ratio}' => '',
            '${installment_day}' => date("d", strtotime($rep_start_date)),
            '${36_months_principal}' => '',
            '${36_months_profit}' => '',
            '${36_months_principal_word}' => '',
            '${36_months_profit_word}' => '',
            '${36_months_installment}' => '',
            '${36_months_installment_word}' => '',
            '${kibor}' => $kibor_rate . "%",
            '${spread}' => $spread_rate . "%",
        ];

        foreach ($LoanArray as $key => $val) {
            //            $round = new \PhpOffice\PhpWord\Element\TextRun();
            //            $round->addText($val, ['underline'=>'single']);
            //            $phpword->setComplexValue($key, $round);
            $phpword->setValue($key, $val);
        }
        //        dd($LoanArray);

        //        $phpword->setValues($LoanArray);
        $path = public_path() . '/global_assets/generated/' . $cnic;
        \Illuminate\Support\Facades\File::isDirectory($path) or \Illuminate\Support\Facades\File::makeDirectory($path, 0777, true, true);
        $phpword->saveAs($path . '/' . $filename . '.docx');
        return response()->download(public_path('global_assets/generated/' . $cnic . '/' . $filename . '.docx'), "[" . $cnic . "] " . $filename . ".docx");
    }
    function nameregex($input)
    {
        return preg_replace('!\s+!', ' ', $input);
    }

    private function handleDownloads($file, $filepath)
    {

        header('Content-Disposition: attachment; filename=' . $file . '.docx');
        readfile($filepath);
        //        return response()->download(public_path('global_assets/legal/' . $file . '.docx'));
    }

    function menuoptions($loanId)
    {
        $d['loanId'] = $loanId;
        $docs = [
            "AgreementtoMortgageFinal",
            "Asset Purchase Agreement Finalized",
            "Memorandum of Title Deposit Deeds _Final",
            "Musharakah Agreement Finalized",
            "Payment Agreement Final",
            "Promise to Lease _Final",
            "Purchase Undertaking Finalized",
            "Undertaking and Indeminity Final",
            "Undertaking for First Time Home Owner_Final",
            "Personal Guarantee Applicant"
        ];

        $d['legal'] = \App\Models\CompanyLegal::get();
        $d['files'] = $docs;
        $d['valuation'] = \App\Models\CompanyValuation::get();
        $d['income'] = \App\Models\CompanyIncomeEst::get();
        $LoanData = \App\Models\LoanHistory::where('id', $loanId)->with('loan_borrower')->first();
        // DD($LoanData);

        $LoanModification = \App\Models\LoanModification::where('loan_id', $loanId)->where('modification', "enhancement")->select(DB::raw("sum(amount) as amount"))->first();
        $d['LoanModification'] = \App\Models\LoanModification::where('loan_id', $loanId)->where('modification', "enhancement")->get();
        $modAmount = "";
        if ($LoanModification && $LoanModification->amount) {
            $modAmount = $LoanModification->amount;
        }
        $d['modAmount'] = $modAmount;

        $DueData = \App\Models\LoanPaymentDue::where(['loan_id' => $loanId, 'installment_no' => ($LoanData->kibor_revision_cycle * 12)])->first();
        $RevisionDate = "";
        if ($DueData) {
            $RevisionDate = $DueData->due_date;
            $LoanData['kibor_revision_date'] = $RevisionDate;
        }
        $d['data'] = $LoanData;
        $d['data']['due'] = \App\Models\LoanPaymentDue::where('loan_id', $loanId)->select(DB::raw("MAX(due_date) as 'mat_date'"), DB::raw("MIN(due_date) as 'ins_date'"))->first();
        $d['loanfees'] = \App\Models\LoansFee::where("loanId", $loanId)->first();
        $d['loanfeescomp'] = \App\Models\LoansFeeCompany::where("loanId", $loanId)->get();

        //        echo "<pre>";
        //        print_r($d['loanfees']);
        //        die; 

        return view('lms_loans.menuoptions', $d);
    }

    function FeesCollection(Request $request)
    {

        //        dd($request->all());
        $loan_id = $request->get("loan_id");
        $legalFeesExists = \App\Models\LoansFee::where("loanId", $loan_id)->first();

        $fieldkey = $request->get("fieldkey");
        $fieldvalue = $request->get("fieldvalue");
        $update_array['loanId'] = $loan_id;
        switch ($fieldkey) {
            case "processingfees":
                //                echo 1;
                $reverse = $request->get("rev");

                if ($reverse == "true") {
                    $update_array['processingFees'] = ($legalFeesExists->processingFees - $fieldvalue);
                    $update_array['processingFeesStatus'] = $legalFeesExists->processingFees == $fieldvalue ? 0 : 1;
                } else {
                    $update_array['processingFees'] = $fieldvalue;
                    $update_array['processingFeesStatus'] = 1;
                }
                //                print_r($update_array);
                //                die;
                break;
            case "fedfees":
                //                echo 2;
                $reverse = $request->get("rev");

                if ($reverse == "true") {

                    $update_array['fedFees'] = ($legalFeesExists->fedFees - $fieldvalue);
                    $update_array['fedFeesStatus'] = $legalFeesExists->fedFees == $fieldvalue ? 0 : 1;
                } else {
                    $update_array['fedFees'] = $fieldvalue;
                    $update_array['fedFeesStatus'] = 1;
                }
                //                print_r($update_array);
                //                die;
                break;
            case "legalfees":
                //                echo 3;

                $update_array['legalCompanyId'] = json_encode($request->get("company_id"));
                $update_array['legalFees'] = $fieldvalue;
                $update_array['legalFeesStatus'] = 1;
                break;
            case "valuationfees":
                //                echo 4;
                $update_array['valuationCompanyId'] = json_encode($request->get("company_id"));
                $update_array['valuationFees'] = $fieldvalue;
                $update_array['valuationFeesStatus'] = 1;

                break;
            case "incomefees":
                //                echo 5;
                $update_array['incomeEstCompanyId'] = json_encode($request->get("company_id"));
                $update_array['incomeEstFees'] = $fieldvalue;
                $update_array['incomeEstFeesStatus'] = 1;
                break;
            case "stampfees":
                //                echo 6;
                $update_array['stampPaperFees'] = $fieldvalue;
                $update_array['stampPaperFeesStatus'] = 1;
                break;
            case "lienfees":
                //                echo 7;
                $update_array['lienFees'] = $fieldvalue;
                $update_array['lienFeesStatus'] = 1;
                break;
        }

        $LoanHistory = \App\Models\LoanHistory::where('id', $loan_id)->first();
        if ($LoanHistory->loan_status_id == 9) {
            return response()->json(array("success" => false));
        } else {


            if (in_array($fieldkey, array('legalfees', 'valuationfees', 'incomefees'))) {

                $LoanFeeComp = \App\Models\LoansFeeCompany::where(['loanId' => $loan_id, 'FeesType' => $fieldkey, 'FeesCompanyId' => $request->get("company_id")])->first();
                if ($LoanFeeComp) {
                    \App\Models\LoansFeeCompany::where('id', $LoanFeeComp->id)->update([
                        'Fees' => $fieldvalue
                    ]);
                } else {
                    \App\Models\LoansFeeCompany::create([
                        'Fees' => $fieldvalue,
                        'loanId' => $loan_id,
                        'FeesType' => $fieldkey,
                        'FeesStatus' => 1,
                        'FeesCompanyId' => $request->get("company_id")
                    ]);
                }
                /*
                  legalFeesStatus
                  valuationFeesStatus
                  incomeEstFeesStatus
                 */
                if ($fieldkey == 'legalfees')
                    $feesupdatear = ['legalFeesStatus' => 1];
                else if ($fieldkey == 'valuationfees')
                    $feesupdatear = ['valuationFeesStatus' => 1];
                else
                    $feesupdatear = ['incomeEstFeesStatus' => 1];


                if ($legalFeesExists) {
                    \App\Models\LoansFee::where('loanId', $loan_id)->update($feesupdatear);
                } else {
                    \App\Models\LoansFee::create($feesupdatear);
                }
                return response()->json(array("success" => true));

                //                echo "<pre>";
                //                print_r($LoanFeeComp);
                //                dd($update_array);
                //                die;
            } else {
                $legalFeesExists = \App\Models\LoansFee::where("loanId", $loan_id)->first();
                if ($legalFeesExists) {
                    $update = \App\Models\LoansFee::find($legalFeesExists->id)->update($update_array);
                    if ($update) {
                        return response()->json(array("success" => true));
                    } else {
                        return response()->json(array("success" => false));
                    }
                } else {
                    $create = \App\Models\LoansFee::create($update_array);
                    if ($create) {
                        return response()->json(array("success" => true));
                    } else {
                        return response()->json(array("success" => false));
                    }
                }
            }
        }
        //        return response()->json(array("payload" => $request->all(), "updates" => $update_array));
    }

    function taxcertificate($loanId)
    {
        $d['loanId'] = $loanId;

        $d['recovered'] = \App\Models\LoanPaymentRecovered::where('loan_id', $loanId)->select(DB::raw('sum(amount_mu) as profit'))->first();

        $d['dueData'] = \App\Models\LoanPaymentDue::where(['loan_id' => $loanId, 'payment_status' => 1])->get();
        $outstanding = \App\Models\LoanPaymentDue::where(['loan_id' => $loanId, 'payment_status' => 1])->orderBy('id', 'desc')->first();
        if ($outstanding) {
            $outstanding = $outstanding->outstanding;
        } else {
            $outstanding = \App\Models\LoanHistory::where('id', $loanId)->first()->total_amount_pr;
        }


        $loanData = \App\Models\LoanHistory::select(
            'loan_borrowers.cnic as cnic',
            DB::raw('concat(loan_borrowers.fname," ",loan_borrowers.lname) as name'),
            'loan_history.id as loan_id',
            'loan_history.disb_date as disb_date',
            'loan_history.total_amount_pr as finance_amount',
            'loan_status.title as loan_status'
        );

        $loanData = $loanData
            ->join('loan_borrowers', 'loan_borrowers.id', '=', 'loan_history.borrower_id')
            ->join('loan_status', 'loan_status.id', '=', 'loan_history.loan_status_id')
            ->where('loan_history.id', $loanId)
            ->first();

        $d['loanData'] = $loanData;
        $d['loanData']['outstanding'] = $outstanding;
        return view('lms_loans.taxcert', $d);
    }

    function loandetails()
    {

        // $d['tt_records'] = \App\Models\LoanHistory::with(['loan_borrower', 'loan_office', 'loantype', 'loan_modifications'])
        //     ->orderBy('disb_date', 'desc')
        //     ->get()
        //     ->each(function ($loan) {
        //         $loan->total_modification_amount = $loan->total_modification_amount;
        //     });

        $d['tt_records'] = \App\Models\LoanHistory::with([
            'loan_borrower',
            'loan_office',
            'loantype',
            'loan_modifications'
        ])
            ->select([
                'loan_history.*',
                DB::raw('(SELECT outstanding FROM loan_payment_due 
                      WHERE loan_payment_due.loan_id = loan_history.id 
                      AND payment_status = 1 
                      ORDER BY id DESC LIMIT 1) AS last_outstanding')
            ])
            ->orderBy('disb_date', 'desc')
            ->get()
            ->each(function ($loan) {
                $loan->total_modification_amount = $loan->total_modification_amount;
            });
        return view('lms_loans.loandetails', $d);
    }
    // function loandetails() {
    //     $tt_records = DB::table('loan_history')
    //         ->join('loan_borrowers', 'loan_history.borrower_id', '=', 'loan_borrowers.id')
    //         ->join('loan_types', 'loan_history.loan_type_id', '=', 'loan_types.id')
    //         ->leftJoin('loan_modifications', 'loan_history.id', '=', 'loan_modifications.loan_id')
    //         ->select(
    //             'loan_history.*',
    //             // 'loan_borrowers.*',
    //             // 'loan_types.*',
    //             DB::raw('SUM(loan_modifications.amount) as total_modification_amount')
    //         )
    //         ->groupBy('loan_history.id')
    //         ->orderBy('loan_history.disb_date', 'desc')
    //         ->get();
    //         // $outstanding = \App\Models\LoanPaymentDue::where(['loan_id' => $loanId, 'payment_status' => 1])->orderBy('id', 'desc')->first();
    //             dd($tt_records);
    //     $d['tt_records'] = $tt_records;

    //     return view('lms_loans.loandetails', $d);
    // }

    function show_schedule($tt_id)
    {
        $d['ttr_id'] = $tt_id;
        $d['loaninfo'] = \App\Models\LoanHistory::where(['id' => $tt_id])->with("loan_borrower")->first();

        // $d['dueinfo'] = \App\Models\LoanPaymentDue::where(['loan_payment_due.loan_id' => $tt_id, 'loan_payment_due.due_status' => 0])
        $d['dueinfo'] = \App\Models\LoanPaymentDue::where(['loan_payment_due.loan_id' => $tt_id])
            ->select('loan_payment_due.*', DB::raw("(select `loan_payment_recovered`.`amount_pr` from `loan_payment_recovered` where `loan_payment_recovered`.`due_id`=`loan_payment_due`.`id` and `loan_payment_recovered`.`payment_type`=2) as partial"))
            ->with('loan_history')
            ->orderBy("loan_payment_due.due_date")
            ->get();

        // $d['dueinfo'] = \App\Models\LoanPaymentDue::where([
        //     'loan_payment_due.loan_id' => $tt_id, 
        //     'loan_payment_due.due_status' => 0
        // ])
        // ->select(
        //     'loan_payment_due.*', 
        //     DB::raw("COALESCE(SUM(loan_payment_recovered.amount_pr), 0) as total_partial") // Multiple partial payments ka sum
        // )
        // ->leftJoin('loan_payment_recovered', function ($join) {
        //     $join->on('loan_payment_recovered.due_id', '=', 'loan_payment_due.id')
        //          ->where('loan_payment_recovered.payment_type', 2);
        // })
        // ->with(['loan_history', 'loan_payment_recovered']) // Eager loading
        // ->groupBy('loan_payment_due.id') // Ensure har due_id ka sum ho
        // ->orderBy("loan_payment_due.due_date") 
        // ->get();


        // $d['dueinfo'] = \App\Models\LoanPaymentDue::where([
        //     'loan_payment_due.loan_id' => $tt_id, 
        //     'loan_payment_due.due_status' => 0
        // ])
        // ->select(
        //     'loan_payment_due.*', 
        //     DB::raw("COALESCE(SUM(loan_payment_recovered.amount_pr), 0) as partial") // Multiple partial payments ko sum karenge
        // )
        // ->leftJoin('loan_payment_recovered', function ($join) {
        //     $join->on('loan_payment_recovered.due_id', '=', 'loan_payment_due.id')
        //          ->where('loan_payment_recovered.payment_type', 2);
        // })
        // ->with(['loan_history', 'loan_payment_recovered']) // Eager loading to avoid extra queries
        // ->groupBy('loan_payment_due.id') // Grouping taake sum work kare
        // ->orderBy("loan_payment_due.due_date")
        // ->get(); 

        // $d['dueinfo'] = \App\Models\LoanPaymentDue::where(['loan_payment_due.loan_id' => $tt_id, 'loan_payment_due.due_status' => 0])
        // ->select('loan_payment_due.*')
        // ->with(['loan_history', 'loan_payment_recovered' => function ($query) {
        //     $query->where('payment_type', 2);
        // }])
        // ->orderBy("loan_payment_due.due_date")
        // ->get();

        $paidinfo = \App\Models\LoanPaymentRecovered::select(DB::raw('loan_payment_due.*'), 'loan_payment_recovered.*')
            ->leftJoin('loan_payment_due', 'loan_payment_due.id', '=', 'loan_payment_recovered.due_id')
            ->where(['loan_payment_recovered.loan_id' => $tt_id])
            ->get();

        $d['duepaidinfo'] = [];
        foreach ($paidinfo as $row) {
            $d['duepaidinfo'][$row->payment_type][$row->due_id] = $row;

            // Calculate overdue days
            $due_date = !empty($row->due_date) ? $row->due_date : $row->recovered_date;
            $recovered_date = $row->recovered_date;
            $rec_time = strtotime($recovered_date);
            $due_time = strtotime($due_date);
            $datediff = $rec_time - $due_time;
            $od_days = round($datediff / (60 * 60 * 24));
            $row['od_days'] = $od_days;
            // Adjust Outstanding Principle for partial payments
            if (isset($d['duepaidinfo'][2][$row->due_id])) {
                $partialAmount = $d['duepaidinfo'][2][$row->due_id]->amount_pr ?? 0;
                $row->outstanding -= $partialAmount;
            }
        }

        $d['paidinfo'] = $paidinfo;
        // dd($dueinfo);
        return view('lms_loans.loanschedule', $d);
    }




    public function kiborrenew()
    {
        return view('lms_loans.kiborrenew');
    }

    public function setrenewkibor(Request $request)
    {
        //dd($request->all());
        $loanData = \App\Models\LoanHistory::select(
            'loan_borrowers.fname',
            'loan_borrowers.mname',
            'loan_borrowers.lname',
            'loan_kibor_history.loan_id',
            'loan_kibor_history.id',
            'loan_kibor_history.start_date as kibor_date',
            'loan_kibor_history.installment_no'
        )
            ->join('loan_borrowers', 'loan_borrowers.id', '=', 'loan_history.borrower_id')
            ->join('loan_kibor_history', 'loan_kibor_history.loan_id', '=', 'loan_history.id')
            ->whereBetween('loan_kibor_history.start_date', [$request->datefrom, $request->dateto])
            ->get();
        $d['datef'] = $request->datefrom;
        $d['datet'] = $request->dateto;
        $d['i'] = 0;

        if ($loanData) {
            $myAr = [];
            foreach ($loanData as $row) {
                $kibor_date = $row->kibor_date;
                //echo $kibor_date;
                $KR = \App\Models\LoanKiborRate::whereRaw('? between start_date and end_date', [$kibor_date])->first('kibor_rate');
                //dd($KR);
                if ($KR && isset($KR['kibor_rate'])) {
                    $row['new_kibor_rate'] = $KR['kibor_rate'];
                } else {
                    $row['new_kibor_rate'] = 0;
                }
                $myAr[] = $row;
            }
            $loanData = $myAr;
        }
        //echo "<pre>";
        //print_r($loanData);
        //die;
        $d['loanData'] = $loanData;
        return view('lms_loans.kiborrenew', $d);
    }

    function postrenewkibor(Request $request)
    {
        //dd($request->all());
        $tt = new LoanHistoryRepo;
        $d['tt_records'] = $tt->getAll();

        $data = $request->all();

        if (isset($data['set']) && $data['set']) {
            $records = $data['set'];
            foreach ($records as $json) {
                $row = json_decode($json, true);
                //print_r($row);
                $loan_id = $row['loan_id'];
                //$hist_id = $row['id'];
                $inst_no = $row['installment_no'];
                $due_date = $row['kibor_date'];
                //echo $due_date;
                $LoanKiborRate = \App\Models\LoanKiborRate::whereRaw('? between start_date and end_date', [$due_date])->first('kibor_rate');
                //dd($LoanKiborRate);
                if ($LoanKiborRate && isset($LoanKiborRate['kibor_rate'])) {
                    $KiborRate = $LoanKiborRate['kibor_rate'];
                    $ScheduleGen = new \App\Helpers\ScheduleGenerator($loan_id);
                    $Res = $ScheduleGen->GenerateSchedule_KiborRenewal($KiborRate, $row);
                    print($Res);
                } else {
                    return view('lms_loans.kiborrenew')->with('flash_error', 'Kibor rate against given tenure does not exists');
                }
            }
        } else {
            return view('lms_loans.kiborrenew')->with('flash_error', 'Nothing found ticked for processing');
        }
    }

    public function loanstep($loan_id)
    {
        $d['loaninfo'] = \App\Models\LoanHistory::where(['id' => $loan_id])->first();
        $d['kibor'] = \App\Models\LoanKiborRate::get();
        $d['takaful'] = \App\Models\LoanTakaful::get();

        return view('lms_loans.loanstep', $d);
    }

    public function loanstepStoreKibor(Request $request)
    {


        $loanId = $request->get("loanId");
        $kibor_rev = $request->get("kibor_rev");

        $set = \App\Models\LoanHistory::where("id", $loanId)->update(['kibor_revision_cycle' => $kibor_rev]);
        if ($set) {
            return response()->json(array("success" => true));
        } else {
            return response()->json(array("success" => false));
        }
    }

    public function loanstepStore(Request $request)
    {
        $loanId = $request->get("loanId");
        $disb_date = $request->get("disb_date");
        $rep_start_date = $request->get("rep_start_date");
        $kibor_rate = $request->get("kibor_rate");
        $spread_rate = $request->get("spread_rate");
        $is_islamic = $request->get('is_islamic') ?? 2;
        $report_type = $request->get('report_type');  // Capture report type (UMI or EMI)


        return $this->gen_schedule($loanId, $disb_date, $rep_start_date, $kibor_rate, $spread_rate, $is_islamic, $report_type);
    }

    public function takafulreport(Request $request)
    {
        $loanId = $request->get("loanId");
        $type = $request->get("type");
        $takaful = \App\Models\LoanTakaful::where(['loan_id' => $loanId, 'type' => $type])->orderBy('id')->get();
        $property = array();
        $life = array();
        if ($takaful && count($takaful) > 0) {

            foreach ($takaful as $row) {

                if ($row->type == 0) {
                    $d['property'][] = $row;
                } else {
                    $d['life'][] = $row;
                }
            }
            $d['takaful'] = $takaful;
            $d['loan_id'] = $loanId;
            $d['i'] = 1;
            $d['j'] = 1;
            // dd($d);
            return view('lms_loans.takaful', $d);
        } else {
            //            dd("1");
            return back()->with('flash_error', 'Case is not disbursed yet');
        }
    }

    public function gen_schedule($loan_id, $disb_date, $rep_start_date, $kibor_rate, $spread_rate, $is_islamic, $report_type)
    {
        $debug = false;

        \App\Models\LoanHistory::where('id', $loan_id)->update(['disb_date' => $disb_date, 'rep_start_date' => $rep_start_date, 'kibor_rate' => $kibor_rate, 'spread_rate' => $spread_rate]);

        $LoanData = \App\Models\LoanHistory::where("id", $loan_id)->first();
        if (!$LoanData->kibor_revision_cycle) {
            return back()->with('flash_error', 'Kibor rate is not set');
        }

        $LoanFeesArray = [
            "processingFeesStatus" => "Processing Fees",
            "fedFeesStatus" => "FED Fees",
            "legalFeesStatus" => "Legal Fees",
            "valuationFeesStatus" => "Valuation Fees",
            "stampPaperFeesStatus" => "Stamp Paper Fees",
        ];

        $LoanFess = \App\Models\LoansFee::where('loanId', $loan_id)->first();
        if (!$debug) {
            if (!$LoanFess) {
                return back()->with('flash_error', 'Please enter all the fees structure');
            } else {
                foreach ($LoanFeesArray as $FeesStatus => $value) {
                    if ($LoanFess->$FeesStatus == 1) {
                        continue;
                    } else {
                        return back()->with('flash_error', 'Please enter ' . $value);
                    }
                }
            }
        }

        $KiborNew = ['kibor_rate' => $kibor_rate, 'spread_rate' => $spread_rate];
        $Schedulehelper = new \App\Helpers\ScheduleGenerator($loan_id);

        if ($report_type === 'UMI') {

            $schResp = $Schedulehelper->GenerateSchedule_UMI($loan_id, $LoanFess, $KiborNew, $debug, $is_islamic);
        } else {

            $schResp = $Schedulehelper->GenerateSchedule_Disburse($loan_id, $LoanFess, $KiborNew, $debug, $is_islamic);
        }

        if ($debug) {
            echo $schResp;
            die;
        }

        if ($schResp) {
            return back()->with('flash_success', 'Schedule Generated Successfully');
        } else {
            return back()->with('flash_error', 'Unable to Generate Schedule, its already generated');
        }
    }
    function calculate_pmt($markup_rate, $loan_term, $amount_pr)
    {
        echo $markup_rate . " - " . $loan_term . " - " . $amount_pr . "<br>";
        $ir = (($markup_rate / 100) / 12);
        $np = $loan_term * 12;
        $pv = $amount_pr;
        $pvif = pow(1 + $ir, $np);
        $pmt = $ir * ($pv * $pvif) / ($pvif - 1);
        return $pmt;
    }

    function GenerateRepaymentScheduleDecline($LoanId, $LoanFees, $KiborNew, $debug = false)
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

        if ($DueData && !$debug) {
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

        //$rate = interest rate
        //$nper = number of periods
        //$fv is future value
        //$pv is present value
        //$type is type
        //        $amount_pr = \App\Models\LoanPaymentDue::where('loan_id', $LoanId)->sum('amount_pr');
        //        dd($amount_pr);
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
        $Final = $ApprovedLoanAmount / $Ffth;
        //        $Final = round($Final);
        //$Final2 = $this->calculate_pmt($rate, $LoanTerm, $ApprovedLoanAmount);
        //echo "Final1: " . $Final1 ." - ".$Final2. "<br>";
        //die;
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
            if ($takaful_amount) {
                //echo $LoanTerm."/".$i."<br>";
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
                    //                    print_r($property_array);
                    //                    echo "<br>";
                    //                    if (!$debug) {
                    \App\Models\LoanTakaful::create($property_array);
                    //                    }
                    $life_array = [
                        'loan_id' => $LoanId,
                        'type' => '1',
                        'covered_amount' => $covered_amount,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'renewal_date' => $renewalDate
                    ];
                    //                    if (!$debug) {
                    \App\Models\LoanTakaful::create($life_array);
                    //                    }
                }
            }

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


                $tak_zero = \App\Models\LoanTakaful::where(['loan_id' => $LoanId, 'type' => 0])->orderBy('id', 'desc')->first();
                $tak_one = \App\Models\LoanTakaful::where(['loan_id' => $LoanId, 'type' => 1])->orderBy('id', 'desc')->first();
                \App\Models\LoanTakaful::whereIn('id', array($tak_one->id, $tak_zero->id))->update(['end_date' => date('Y-m-d', strtotime($Dev_ScheduleDate))]);
                if ($debug) {

                    echo "id1: " . $tak_one->id . " - id2: " . $tak_zero->id . "<br><br>";
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

    public function kiborrenewalschedule($id)
    {

        $KiborRevData = \App\Models\LoanKiborHistory::where('loan_id', $id)->get();
        $i = 1;
        return view('lms_loans.kiborrenewals', compact('i', 'id', 'KiborRevData'));
    }

    public function losdata()
    {
        //        $users = DB::connection('mysql2')->select('SELECT id,name,email,cell,isCellVerfied,isPostSignupCompleted,createdAt from users');

        $client = new Client();
        $response = $client->get(env('LOS_URL') . 'user-applications/approved');
        $users = json_decode($response->getBody(), true);
        return view('lms_loans.losData', compact('users'));
    }

    public function losdataByUser(Request $request)
    {

        $customer = DB::connection('mysql2')->select('SELECT * from customers where userId="' . $request->id . '"');
        $applications = DB::connection('mysql2')->select('SELECT * from applications where userId="' . $request->id . '"');
        $user_attachments = DB::connection('mysql2')->select('SELECT * from user_attachments where userId="' . $request->id . '"');

        return view('lms_loans.losdataByUser', compact('customer', 'applications', 'user_attachments'));
    }

    public function Rescheduling($id)
    {
        $LoanData = \App\Models\LoanHistory::where("id", $id)->first();
        return view('lms_loans.resched', compact('LoanData'));
    }

    public function Enhancement($id)
    {
        $LoanData = \App\Models\LoanHistory::where("id", $id)->first();
        return view('lms_loans.enhancement', compact('LoanData'));
    }

    function PayLoan()
    {
        // Installmnet, pr, markup, duedate
        $loanData = \App\Models\LoanHistory::select(
            DB::raw('concat(loan_borrowers.fname," ",loan_borrowers.mname," ",loan_borrowers.lname) as name'),
            'loan_payment_due.loan_id',
            'loan_payment_due.due_date',
            'loan_payment_due.installment_no',
            'loan_payment_due.amount_pr',
            'loan_payment_due.amount_mu',
            'loan_payment_due.id as loan_payment_id'
        )
            ->join('loan_borrowers', 'loan_borrowers.id', '=', 'loan_history.borrower_id')
            ->join('loan_status', 'loan_status.id', '=', 'loan_history.loan_status_id')
            ->join('loan_payment_due', 'loan_payment_due.loan_id', '=', 'loan_history.id')
            //->where('loan_payment_due.due_date', date('Y-m-d'))
            ->where('loan_payment_due.due_date', "<=", date("Y-m-d"))
            ->where('loan_payment_due.payment_status', "0")
            ->where('loan_payment_due.due_status', "0")
            ->where('loan_history.loan_status_id', '<>', "7")
            ->where('loan_history.loan_status_id', '<>', "4")
            ->where('loan_history.loan_status_id', '<>', "3")
            ->orderBy('loan_payment_due.due_date')
            ->get();
        // dd($loanData);
        $i = 0;
        return view('lms_loans.payloans', compact('loanData', 'i'));
    }

    function RunScript()
    {


        $LoanHistory = \App\Models\LoanHistory::where('loan_status_id', '10')->get();
        if ($LoanHistory) {
            foreach ($LoanHistory as $LoanHistory) {
                $kibor_revision_cycle = $LoanHistory->kibor_revision_cycle;
                $LoanId = $LoanHistory->id;
                $kibor_rate = $LoanHistory->kibor_rate;
                $musharakah_date = $LoanHistory->musharakah_date;
                $DueData = \App\Models\LoanPaymentDue::where('loan_id', $LoanHistory->id)->get();
                $KiborRevData = array();
                if ($DueData) {
                    $i = 1;
                    $exactRenewalDate = $kibor_revision_cycle * 12;
                    foreach ($DueData as $row) {
                        $MysqlScheduleDate = $row->due_date;
                        if (($i == 1 || $i == ($exactRenewalDate)) && ($LoanHistory->loan_period > $i)) {
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
                        $i++;
                    }
                }
            }
        }
    }
}
