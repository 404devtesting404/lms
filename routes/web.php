<?php

Auth::routes(['register' => false]);
Route::get( '/logout', function() {

    Auth::logout();
    return view('auth.login');
});

//Route::get('/test', 'TestController@index')->name('test');
Route::get('/privacy-policy', 'HomeController@privacy_policy')->name('privacy_policy');
Route::get('/RunScript', 'LoansController@RunScript')->name('RunScript');
Route::get('/terms-of-use', 'HomeController@terms_of_use')->name('terms_of_use');
Route::post('/savefeesdata', 'LoansController@FeesCollection')->name('savefeesdata');

Route::get('/due-installment', 'SendSmsController@dueInstallment')->name('due-installment');


Route::group(['middleware' => 'auth'], function () {

    Route::get('/', 'HomeController@dashboard')->name('home');
    Route::get('/home', 'HomeController@-')->name('home');
    Route::get('/dashboard', 'HomeController@dashboard')->name('dashboard');

    Route::group(['prefix' => 'my_account'], function () {
        Route::get('/', 'MyAccountController@edit_profile')->name('my_account');
        Route::put('/', 'MyAccountController@update_profile')->name('my_account.update');
        Route::put('/change_password', 'MyAccountController@change_pass')->name('my_account.change_pass');
    });
    /* Administrations */
    Route::group(['prefix' => 'general'], function () {
        Route::group(['prefix' => 'office'], function () {
            Route::get('/', 'GeneralOfficeController@index');
            Route::get('index', 'GeneralOfficeController@index')->name('general-offices.index');
            Route::get('create', 'GeneralOfficeController@create')->name('general-offices.create');
            Route::get('destroy/{ttr}', 'GeneralOfficeController@destroy')->name('general-offices.destroy');
            Route::get('show/{ttr}', 'GeneralOfficeController@show')->name('general-offices.show');
            Route::get('edit/{ttr}', 'GeneralOfficeController@edit')->name('general-offices.edit');
            Route::patch('update/{ttr}', 'GeneralOfficeController@update')->name('general-offices.update');
            Route::post('store', 'GeneralOfficeController@store')->name('general-offices.store');
        });
        Route::group(['prefix' => 'users'], function () {
            Route::get('/', 'GeneralUserController@index');
            Route::get('index', 'GeneralUserController@index')->name('general-users.index');
            Route::get('create', 'GeneralUserController@create')->name('general-users.create');
            Route::get('destroy/{ttr}', 'GeneralUserController@destroy')->name('general-users.destroy');
            Route::get('show/{ttr}', 'GeneralUserController@show')->name('users.show');
            Route::get('show/{ttr}', 'GeneralUserController@show')->name('general-users.show');
            Route::get('edit/{ttr}', 'GeneralUserController@edit')->name('general-users.edit');
            Route::patch('update/{ttr}', 'GeneralUserController@update')->name('general-users.update');
            Route::post('store', 'GeneralUserController@store')->name('general-users.store');
        });


        Route::group(['prefix' => 'valuations'], function () {
            Route::get('/', 'CompanyValuationController@index')->name('valuations');
            Route::get('index', 'CompanyValuationController@index')->name('company-valuations.index');
            Route::get('create', 'CompanyValuationController@create')->name('company-valuations.create');
            Route::get('destroy/{ttr}', 'CompanyValuationController@destroy')->name('company-valuations.destroy');
            Route::get('/show/{ttr}', 'CompanyValuationController@show')->name('company-valuations.show');
            Route::get('edit/{ttr}', 'CompanyValuationController@edit')->name('company-valuations.edit');
            Route::patch('update/{ttr}', 'CompanyValuationController@update')->name('company-valuations.update');
            Route::post('store', 'CompanyValuationController@store')->name('company-valuations.store');
        });
        Route::group(['prefix' => 'legal'], function () {
            Route::get('/', 'CompanyLegalController@index')->name('legal');
            Route::get('index', 'CompanyLegalController@index')->name('company-legals.index');
            Route::get('create', 'CompanyLegalController@create')->name('company-legals.create');

            Route::get('destroy/{ttr}', 'CompanyLegalController@destroy')->name('company-legals.destroy');
            Route::get('/show/{ttr}', 'CompanyLegalController@show')->name('company-legals.show');
            Route::get('edit/{ttr}', 'CompanyLegalController@edit')->name('company-legals.edit');
            Route::patch('update/{ttr}', 'CompanyLegalController@update')->name('company-legals.update');
            Route::post('store', 'CompanyLegalController@store')->name('company-legals.store');
        });
        Route::group(['prefix' => 'incomeest'], function () {
            Route::get('/', 'CompanyIncomeEstController@index')->name('company-income-ests.index');
            Route::get('index', 'CompanyIncomeEstController@index')->name('company-income-ests.index');
            Route::get('create', 'CompanyIncomeEstController@create')->name('company-income-ests.create');

            Route::get('destroy/{ttr}', 'CompanyIncomeEstController@destroy')->name('company-income-ests.destroy');
            Route::get('/show/{ttr}', 'CompanyIncomeEstController@show')->name('company-income-ests.show');
            Route::get('edit/{ttr}', 'CompanyIncomeEstController@edit')->name('company-income-ests.edit');
            Route::patch('update/{ttr}', 'CompanyIncomeEstController@update')->name('company-income-ests.update');
            Route::post('store', 'CompanyIncomeEstController@store')->name('company-income-ests.store');
        });


    });
    /*     * ************* Reports **************** */
    Route::group(['prefix' => 'hr'], function () {
        Route::group(['prefix' => 'employees'], function () {
            Route::get('/', 'HrEmployeeController@index');
            Route::get('index', 'HrEmployeeController@index')->name('hr-employees.index');
            Route::get('create', 'HrEmployeeController@create')->name('hr-employees.create');
            Route::get('destroy/{ttr}', 'HrEmployeeController@destroy')->name('hr-employees.destroy');
            Route::get('show/{ttr}', 'HrEmployeeController@show')->name('hr-employees.show');
            Route::get('edit/{ttr}', 'HrEmployeeController@edit')->name('hr-employees.edit');
            Route::patch('update/{ttr}', 'HrEmployeeController@update')->name('hr-employees.update');
            Route::post('store', 'HrEmployeeController@store')->name('hr-employees.store');
        });
        Route::group(['prefix' => 'jobs'], function () {
            Route::get('/', 'HrJobController@index');
            Route::get('index', 'HrJobController@index')->name('hr-jobs.index');
            Route::get('create', 'HrJobController@create')->name('hr-jobs.create');
            Route::get('destroy/{ttr}', 'HrJobController@destroy')->name('hr-jobs.destroy');
            Route::get('show/{ttr}', 'HrJobController@show')->name('hr-jobs.show');
            Route::get('edit/{ttr}', 'HrJobController@edit')->name('hr-jobs.edit');
            Route::patch('update/{ttr}', 'HrJobController@update')->name('hr-jobs.update');
            Route::post('store', 'HrJobController@store')->name('hr-jobs.store');
        });
    });
    /*     * ************* Reports **************** */
    Route::group(['prefix' => 'reports'], function () {

        Route::get('financial', 'ReportsController@financial')->name('reports.financial');
        Route::post('financialreport', 'ReportsController@financialreport')->name('reports.financialreport');

        Route::get('trial', 'ReportsController@trialbalance')->name('reports.trial');
        Route::post('trialreport', 'ReportsController@trialbalancereport')->name('reports.trialreport');


        Route::get('trialreportacc', 'ReportsController@trialreportacc')->name('reports.trialreportacc');
        Route::post('trialreportaccdetail', 'ReportsController@trialreportaccdetail')->name('reports.trialreportaccdetail');


        Route::get('general', 'ReportsController@general')->name('reports.general');
        Route::post('generalreport', 'ReportsController@generalreport')->name('reports.generalreport');

        Route::get('disb', 'ReportsController@disb')->name('reports.disb');
        Route::post('disbreport', 'ReportsController@disbreport')->name('reports.disbreport');

        Route::get('dues', 'ReportsController@dues')->name('reports.dues');
        Route::post('duesreport', 'ReportsController@duesreport')->name('reports.duesreport');

        Route::get('agingamount', 'ReportsController@agingamount')->name('reports.agingamount');
        Route::post('agingamountreport', 'ReportsController@agingamountreport')->name('reports.agingamountreport');

        Route::get('overdues', 'ReportsController@overdues')->name('reports.overdues');
        Route::post('overduesreport', 'ReportsController@overduesreport')->name('reports.overduesreport');

        Route::get('payments', 'ReportsController@payments')->name('reports.payments');
        Route::post('paymentsreport', 'ReportsController@paymentsreport')->name('reports.paymentsreport');

        Route::get('odues', 'ReportsController@odues')->name('reports.odues');
        Route::post('par', 'ReportsController@par')->name('reports.par');
        Route::post('accrued', 'ReportsController@accrued')->name('reports.accrued');
    });

    Route::group(['prefix' => 'finance'], function () {
        Route::group(['prefix' => 'ledger'], function () {
            Route::get('/', 'FinGeneralLedgerController@index');
            Route::get('/', 'FinGeneralLedgerController@index')->name('fin-general-ledgers.index');
            Route::get('create', 'FinGeneralLedgerController@create')->name('fin-general-ledgers.create');
            Route::get('filemanager', 'FinGeneralLedgerController@filemanager')->name('fin-general-ledgers.filemanager');
            Route::post('upload', 'FinGeneralLedgerController@upload')->name('fin-general-ledgers.upload');
            Route::get('destroy/{ttr}', 'FinGeneralLedgerController@destroy')->name('fin-general-ledgers.destroy');
            Route::get('/show/{ttr}', 'FinGeneralLedgerController@show')->name('fin-general-ledgers.show');
            Route::get('/process/{ttr}', 'FinGeneralLedgerController@process')->name('fin-general-ledgers.process');
            Route::get('/vouchers', 'VoucherController@index')->name('fin-general-ledgers.vouchers');
            Route::get('edit/{ttr}', 'FinGeneralLedgerController@edit')->name('fin-general-ledgers.edit');
            Route::patch('update/{ttr}', 'FinGeneralLedgerController@update')->name('fin-general-ledgers.update');
            Route::post('store', 'FinGeneralLedgerController@store')->name('fin-general-ledgers.store');
            Route::post('/submitvouchers', 'FinGeneralLedgerController@submitvouchers');
            Route::post('/bulkApproveLedger', 'VoucherController@bulkApproveLedger')->name('fin-general-ledgers.bulkApprove');


        });
        Route::group(['prefix' => 'checkbook'], function () {
            Route::get('/', 'FinCheckbookController@index');
            Route::get('index', 'FinCheckbookController@index')->name('fin-checkbooks.index');
            Route::get('create', 'FinCheckbookController@create')->name('fin-checkbooks.create');
            Route::get('destroy/{ttr}', 'FinCheckbookController@destroy')->name('fin-checkbooks.destroy');
            Route::get('show/{ttr}', 'FinCheckbookController@show')->name('fin-checkbooks.show');
            Route::get('edit/{ttr}', 'FinCheckbookController@edit')->name('fin-checkbooks.edit');
            Route::patch('update/{ttr}', 'FinCheckbookController@update')->name('fin-checkbooks.update');
            Route::post('store', 'FinCheckbookController@store')->name('fin-checkbooks.store');
        });
        Route::group(['prefix' => 'accounts'], function () {
            Route::get('/', 'FinChartOfAccountController@index');
            Route::get('index', 'FinChartOfAccountController@index')->name('fin-chart-of-accounts.index');
            Route::get('create', 'FinChartOfAccountController@create')->name('fin-chart-of-accounts.create');
            Route::get('destroy/{ttr}', 'FinChartOfAccountController@destroy')->name('fin-chart-of-accounts.destroy');
            Route::get('show/{ttr}', 'FinChartOfAccountController@show')->name('fin-chart-of-accounts.show');
            Route::get('edit/{ttr}', 'FinChartOfAccountController@edit')->name('fin-chart-of-accounts.edit');
            Route::patch('update/{ttr}', 'FinChartOfAccountController@update')->name('fin-chart-of-accounts.update');
            Route::post('store', 'FinChartOfAccountController@store')->name('fin-chart-of-accounts.store');
        });
        Route::group(['prefix' => 'banks'], function () {
            Route::get('/', 'FinBanksAccountController@index');
            Route::get('index', 'FinBanksAccountController@index')->name('fin-banks-accounts.index');
            Route::get('create', 'FinBanksAccountController@create')->name('fin-banks-accounts.create');
            Route::get('destroy/{ttr}', 'FinBanksAccountController@destroy')->name('fin-banks-accounts.destroy');
            Route::get('show/{trr}', 'FinBanksAccountController@show')->name('fin-banks-accounts.show');
            Route::get('edit/{ttr}', 'FinBanksAccountController@edit')->name('fin-banks-accounts.edit');
            Route::patch('update/{ttr}', 'FinBanksAccountController@update')->name('fin-banks-accounts.update');
            Route::post('store', 'FinBanksAccountController@store')->name('fin-banks-accounts.store');
        });
    });
    //Loans
    Route::group(['prefix' => 'aml'], function () {
        Route::get('/', 'AmlBlacklistController@index');
        //Route::get('scan_aml', 'AmlBlacklistController@index')->name('aml-blacklists.scan_aml');
        Route::get('create', 'AmlBlacklistController@create')->name('aml-blacklists.create');
        Route::get('destroy/{ttr}', 'AmlBlacklistController@destroy')->name('aml-blacklists.destroy');
        Route::get('show/{ttr}', 'AmlBlacklistController@show')->name('aml-blacklists.show');
        Route::get('edit/{ttr}', 'AmlBlacklistController@edit')->name('aml-blacklists.edit');
        Route::patch('update/{ttr}', 'AmlBlacklistController@update')->name('aml-blacklists.update');
        Route::post('store', 'AmlBlacklistController@store')->name('aml-blacklists.store');

        Route::get('/scan_aml', 'AmlBlacklistController@index')->name('scan_aml');
        Route::get('/upload_aml', 'AmlBlacklistController@index')->name('upload_aml');
    });
    //Loans
    Route::group(['prefix' => 'loans'], function () {
        //Borrowers
        Route::group(['prefix' => 'borrowers'], function () {
            Route::get('/', 'LoanBorrowerController@index');
            Route::get('index', 'LoanBorrowerController@index')->name('loan-borrowers.index');
            Route::get('create', 'LoanBorrowerController@create')->name('loan-borrowers.create');
            Route::get('destroy/{ttr}', 'LoanBorrower@destroy')->name('loan-borrowers.destroy');
            Route::get('show/{ttr}', 'LoanBorrowerController@show')->name('loan-borrowers.show');
            Route::get('edit/{ttr}', 'LoanBorrowerController@edit')->name('loan-borrowers.edit');
            Route::patch('update/{ttr}', 'LoanBorrowerController@update')->name('loan-borrowers.update');
            Route::post('store', 'LoanBorrowerController@store')->name('loan-borrowers.store');
        });
        //Kibor
        Route::group(['prefix' => 'kibor'], function () {
         //   Route::get('/', 'LoanKiborRateController@index');
            Route::get('index', 'LoanKiborRateController@index')->name('loan-kibor-rates.index');
            Route::get('create', 'LoanKiborRateController@create')->name('loan-kibor-rates.create');
            Route::get('destroy/{ttr}', 'LoanKiborRateController@destroy')->name('loan-kibor-rates.destroy');
            Route::get('show/{ttr}', 'LoanKiborRateController@show')->name('loan-kibor-rates.show');
            Route::get('edit/{ttr}', 'LoanKiborRateController@edit')->name('loan-kibor-rates.edit');
            Route::patch('update/{ttr}', 'LoanKiborRateController@update')->name('loan-kibor-rates.update');
            Route::post('store', 'LoanKiborRateController@store')->name('loan-kibor-rates.store');

        });
        //Kibor Hisotry
        Route::group(['prefix' => 'kiborhistory'], function () {
            Route::get('/', 'LoanKiborHistoryController@index');
            Route::get('index', 'LoanKiborHistoryController@index')->name('loan-kibor-histories.index');
            Route::get('create/{ttr}', 'LoanKiborHistoryController@create')->name('loan-kibor-histories.create');
            Route::get('destroy/{ttr}', 'LoanKiborHistoryController@destroy')->name('loan-kibor-histories.destroy');
            Route::get('show/{ttr}', 'LoanKiborHistoryController@show')->name('loan-kibor-histories.show');
            Route::get('edit/{ttr}', 'LoanKiborHistoryController@edit')->name('loan-kibor-histories.edit');
            Route::patch('update/{ttr}', 'LoanKiborHistoryController@update')->name('loan-kibor-histories.update');
            Route::post('store', 'LoanKiborHistoryController@store')->name('loan-kibor-histories.store');
        });
        //takaful
        Route::group(['prefix' => 'takaful'], function () {
            Route::get('/', 'LoanTakafulController@index');
            Route::post('/takafulreport', 'LoanTakafulController@takaful')->name('loan-takaful.takaful.report');
            Route::get('index', 'LoanTakafulController@index')->name('loan-takaful.index');
            Route::get('create', 'LoanTakafulController@create')->name('loan-takaful.create');
            Route::get('destroy/{ttr}', 'LoanTakafulController@destroy')->name('loan-takaful.destroy');
            Route::get('show/{ttr}', 'LoanTakafulController@show')->name('loan-takaful.show');
            Route::get('edit/{ttr}', 'LoanTakafulController@edit')->name('loan-takaful.edit');
            Route::patch('update/{ttr}', 'LoanTakafulController@update')->name('loan-takaful.update');
            Route::post('store', 'LoanTakafulController@store')->name('loan-takaful.store');
            Route::post('storetakafulpolicy', 'LoanTakafulController@storetakafulpolicy')->name('storetakafulpolicy');
        });
        //Loans

        Route::get('/', 'LoansController@borrowers')->name('tt.borrowers');
        Route::get('/kiborrenewalschedule/{ttr}', 'LoansController@kiborrenewalschedule')->name('kiborrenewalschedule');
        Route::get('/kibor', 'LoanKiborRateController@index')->name('kibor.all');
        Route::get('/kiborhistory', 'LoanKiborHistoryController@index')->name('kibor.history');

        Route::get('/loandetails', 'LoansController@loandetails')->name('tt.loandetails');
        Route::get('/', 'LoansController@index')->name('tt.borrowers');
        Route::get('show_schedule/{ttr}', 'LoansController@show_schedule')->name('ttr.show_schedule');

        Route::get('/authorize', 'LoansController@authorizeuser')->name('tt.authorize');

        Route::get('legal_docs', 'LoansController@LegalDocuments')->name('ttr.legal_docs');
        Route::get('welcome_letter', 'LoansController@WelcomeLetter')->name('ttr.welcome_letter');
        Route::get('acknowledgement_letter', 'LoansController@AcknowledgementLetter')->name('ttr.acknowledgement_letter');

        Route::get('showloan/{ttr}', 'LoansController@showloan')->name('ttr.showloan');
        Route::get('gen_schedule/{ttr}', 'LoansController@gen_schedule')->name('ttr.gen_schedule');
        Route::get('loanstep/{ttr}', 'LoansController@loanstep')->name('ttr.loanstep');
        Route::get('taxcertificate/{ttr}', 'LoansController@taxcertificate')->name('loans.taxcertificate');
        Route::get('menu/{ttr}', 'LoansController@menuoptions')->name('loans.menu');
//        Route::get('storestep/{ttr}', 'LoansController@loanstepStore')->name('storestep');

        Route::get('renewkibor', 'LoansController@renewkibor')->name('loans.renewkibor');
        Route::post('setrenewkibor', 'LoansController@setrenewkibor')->name('loans.setrenewkibor');
        Route::post('postrenewkibor', 'LoansController@postrenewkibor')->name('loans.postrenewkibor');


        Route::post('rescheduling', 'LoanPaymentRecoveredController@rescheduling')->name('rescheduling');
        Route::post('enhancement', 'LoanPaymentRecoveredController@enhancement')->name('enhancement');

        Route::post('storestepkibor', 'LoansController@loanstepStoreKibor')->name('storestepkibor');
        Route::get('losdata', 'LoansController@losdata')->name('loans.losdata');
        Route::get('losdataByUser', 'LoansController@losdataByUser')->name('loans.losdataby_user');
        Route::post('uploadAcknowledgementDocs/{ttr}', 'LoanDocController@uploadAcknowledgementDocs')->name('loans.upload_acknowledgement_docs');
        Route::get('uploadAcknowledgementDocForm/{ttr}', 'LoanDocController@uploadAcknowledgementDocForm')->name('loans.upload_acknowledgement_form');
        Route::post('storestep', 'LoansController@loanstepStore')->name('storestep');

        Route::get('pay_installment/{ttr}', 'LoanPaymentRecoveredController@pay_installment')->name('loans.pay');
        Route::get('early_settlement/{ttr}', 'LoanPaymentRecoveredController@pay_ealrysettlement')->name('loans.early');
        Route::get('partial/{ttr}', 'LoanPaymentRecoveredController@pay_partial')->name('loans.partial');
        Route::post('showpartial', 'LoanPaymentRecoveredController@showpartial')->name('loan-payment-recovereds.showpartial');
        Route::post('store_partial', 'LoanPaymentRecoveredController@store_partial')->name('loan-payment-recovereds.store_partial');
        Route::get('runkibor/{ttr}', 'LoanPaymentRecoveredController@runKiborRevision')->name('runkibor');

        Route::get('takafulreport', 'LoansController@takafulreport')->name('loans.takafulreport');

        Route::get('reverse_pay/{ttr}', 'LoansController@rev_payment')->name('loans.reverse_pay');

        Route::get('addloan/{application_id}', 'LoansController@AddLoan')->name('loans.addloan');
        Route::get('payloans', 'LoansController@PayLoan')->name('loans.payloans');
        Route::post('postbulkpayment', 'LoanPaymentRecoveredController@PostBulkPayment')->name('loans.postbulkpayment');
        Route::get('kiborrenew', 'LoansController@kiborrenew')->name('loans.kiborrenew');
        Route::post('storenewloan', 'LoansController@SaveNewLoan')->name('loans.storenewloan');

        Route::get('generateWelcomeLetter/{ttr}', 'LoanDocController@generateWelcomeLetter')->name('loans.generate_welcome_letter');
        Route::get('generateAcknowledgementLetter/{ttr}', 'LoanDocController@generateAcknowledgementLetter')->name('loans.generate_acknowledgement_letter');
        Route::get('AgreetoMortgage/{ttr}', 'LoanDocController@AgreetoMortgage')->name('loans.agree_to_mortgage');
        Route::get('depositeDeeds/{ttr}', 'LoanDocController@depositeDeeds')->name('loans.deposite_deeds');
        Route::get('UndertakingIndemnity/{ttr}', 'LoanDocController@UndertakingIndemnity')->name('loans.undertaking_indemnity');
        Route::get('UnderTakingFirstTime/{ttr}', 'LoanDocController@UnderTakingFirstTime')->name('loans.undertaking_firsttime');
        Route::get('musharakaAgree/{ttr}', 'LoanDocController@musharakaAgree')->name('loans.musharaka_agree');
        Route::get('promiseLease/{ttr}', 'LoanDocController@promiseLease')->name('loans.promise_lease');
        Route::get('paymentAgree/{ttr}', 'LoanDocController@paymentAgree')->name('loans.payment_agree');
        Route::get('purchaseUnderTakingLetters/{ttr}', 'LoanDocController@purchaseUnderTakingLetters')->name('loans.purchase_underTaking_letters');


    });
    //Payments
    Route::group(['prefix' => 'payment'], function () {
        Route::group(['prefix' => 'slips'], function () {
            Route::get('/', 'LoanBankSlipController@index');
            Route::get('/', 'LoanBankSlipController@index')->name('loan-bankslips.index');
            Route::get('create', 'LoanBankSlipController@create')->name('loan-bankslips.create');
            Route::get('destroy/{ttr}', 'LoanBankSlipController@destroy')->name('loan-bankslips.destroy');
            Route::get('show/{ttr}', 'LoanBankSlipController@show')->name('loan-bankslips.show');
            Route::get('edit/{ttr}', 'LoanBankSlipController@edit')->name('loan-bankslips.edit');
            Route::patch('update/{ttr}', 'LoanBankSlipController@update')->name('loan-bankslips.update');
            Route::post('store', 'LoanBankSlipController@store')->name('loan-bankslips.store');
        });
        Route::group(['prefix' => 'due'], function () {
            Route::get('/', 'LoanPaymentDueController@index');
            Route::get('/', 'LoanPaymentDueController@index')->name('loan-payment-dues.index');
            Route::get('create', 'LoanPaymentDueController@create')->name('loan-payment-dues.create');
            Route::get('destroy/{ttr}', 'LoanPaymentDueController@destroy')->name('loan-payment-dues.destroy');
            Route::get('show/{ttr}', 'LoanPaymentDueController@show')->name('loan-payment-dues.show');
            Route::get('edit/{ttr}', 'LoanPaymentDueController@edit')->name('loan-payment-dues.edit');
            Route::patch('update/{ttr}', 'LoanPaymentDueController@update')->name('loan-payment-dues.update');
            Route::post('store', 'LoanPaymentDueController@store')->name('loan-payment-dues.store');
        });
        Route::group(['prefix' => 'recovered'], function () {
            Route::get('/', 'LoanPaymentRecoveredController@index');
            Route::get('/', 'LoanPaymentRecoveredController@index')->name('loan-payment-recovereds.index');
            Route::get('create', 'LoanPaymentRecoveredController@create')->name('loan-payment-recovereds.create');
            Route::post('store', 'LoanPaymentRecoveredController@store')->name('loan-payment-recovereds.store');
            Route::post('storepay', 'LoanPaymentRecoveredController@storepay')->name('loan-payment-recovereds.storepay');
            Route::post('earlypay', 'LoanPaymentRecoveredController@earlypay')->name('loan-payment-recovereds.earlypay');
            Route::get('destroy/{ttr}', 'LoanPaymentRecoveredController@destroy')->name('loan-payment-recovereds.destroy');
            Route::get('show/{ttr}', 'LoanPaymentRecoveredController@show')->name('loan-payment-recovereds.show');
            Route::get('edit/{ttr}', 'LoanPaymentRecoveredController@edit')->name('loan-payment-recovereds.edit');
            Route::patch('update/{ttr}', 'LoanPaymentRecoveredController@update')->name('loan-payment-recovereds.update');
        });



    });
    
    //Bank Accounts
    Route::group(['prefix' => 'accounts'], function (){
      Route::get('bank', 'BankAccountsController@index')->name('bank');
      Route::post('bank', 'BankAccountsController@store')->name('bank.post');
             Route::post('bank/details', 'BankAccountsController@bankDetails')->name('bank.details');
    });
    
    //Fund Transfer
    Route::group(['prefix' => 'funds'], function (){
        Route::get('transfer', 'FundsTransferController@index')->name('transfer');
        Route::post('transfer', 'FundsTransferController@store')->name('transfer.post');
        Route::post('loan/users', 'FundsTransferController@loanUsers')->name('loan.users');
    });

    /*     * ************* Support Team **************** */
    Route::group(['namespace' => 'SupportTeam',], function () {

        /*         * ************* Students **************** */
        Route::group(['prefix' => 'students'], function () {
            Route::get('reset_pass/{st_id}', 'StudentRecordController@reset_pass')->name('st.reset_pass');
            Route::get('graduated', 'StudentRecordController@graduated')->name('students.graduated');
            Route::put('not_graduated/{id}', 'StudentRecordController@not_graduated')->name('st.not_graduated');
            Route::get('list/{class_id}', 'StudentRecordController@listByClass')->name('students.list');

            /* Promotions */
            Route::post('promote_selector', 'PromotionController@selector')->name('students.promote_selector');
            Route::get('promotion/manage', 'PromotionController@manage')->name('students.promotion_manage');
            Route::get('promotion/reset/{pid}', 'PromotionController@reset')->name('students.promotion_reset');
            Route::get('promotion/reset_all', 'PromotionController@reset_all')->name('students.promotion_reset_all');
            Route::get('promotion/{fc?}/{fs?}/{tc?}/{ts?}', 'PromotionController@promotion')->name('students.promotion');
            Route::post('promote/{fc}/{fs}/{tc}/{ts}', 'PromotionController@promote')->name('students.promote');
        });

        /*         * ************* Users **************** */
        Route::group(['prefix' => 'users'], function () {
            Route::get('reset_pass/{id}', 'UserController@reset_pass')->name('users.reset_pass');
        });
        /*         * ************* Payments **************** */
        Route::group(['prefix' => 'payments'], function () {

            Route::get('manage/{class_id?}', 'PaymentController@manage')->name('payments.manage');
            Route::get('invoice/{id}/{year?}', 'PaymentController@invoice')->name('payments.invoice');
            Route::get('receipts/{id}', 'PaymentController@receipts')->name('payments.receipts');
            Route::get('pdf_receipts/{id}', 'PaymentController@pdf_receipts')->name('payments.pdf_receipts');
            Route::post('select_year', 'PaymentController@select_year')->name('payments.select_year');
            Route::post('select_class', 'PaymentController@select_class')->name('payments.select_class');
            Route::get('reset_record/{id}', 'PaymentController@reset_record')->name('payments.reset_record');
            Route::post('pay_now/{id}', 'PaymentController@pay_now')->name('payments.pay_now');
        });

        /*         * ************* Pins **************** */
        Route::group(['prefix' => 'pins'], function () {
            Route::get('create', 'PinController@create')->name('pins.create');
            Route::get('/', 'PinController@index')->name('pins.index');
            Route::post('/', 'PinController@store')->name('pins.store');
            Route::get('enter/{id}', 'PinController@enter_pin')->name('pins.enter');
            Route::post('verify/{id}', 'PinController@verify')->name('pins.verify');
            Route::get('/', 'PinController@destroy')->name('pins.destroy');
        });
    });



    Route::group(['prefix' => 'roles'], function (){

        Route::get('/create', 'RolesController@create');
        Route::post('/store', 'RolesController@store');
        Route::get('/viewRoles', 'RolesController@viewRoles');
        Route::get('/viewRolesDetail', 'RolesController@viewRolesDetail');
        Route::get('/delete', 'RolesController@delete');


    });

    Route::group(['prefix' => 'users'], function (){


        Route::get('/create', 'UserController@create');
        Route::post('/store', 'UserController@store');
        Route::get('/viewUsers', 'UserController@viewUsers');
        Route::get('/edit', 'UserController@edit');
        Route::get('/delete', 'UserController@delete');
        Route::post('/update', 'UserController@update');
        Route::post('/getUsers', 'UserController@getUsers');


    });


    Route::group(['prefix' => 'audit'], function (){

        Route::get('/PageActivity', 'MyAccountController@PageActivity');
        Route::get('/userPageActivity', 'MyAccountController@userPageActivity');

    });


    Route::get('/addMainMenu', 'MyAccountController@addMainMenu');
    Route::post('/storeMainMenu', 'MyAccountController@storeMainMenu');

    Route::get('/addSubMenu', 'MyAccountController@addSubMenu');
    Route::post('/storeSubMenu', 'MyAccountController@storeSubMenu');

    Route::post('/storeLogs', 'MyAccountController@storeLogs');


});
Route::get('/test', 'HomeController@test');
/* * ********************** SUPER ADMIN *************************** */
Route::group(['namespace' => 'SuperAdmin', 'middleware' => 'super_admin', 'prefix' => 'super_admin'], function () {

    Route::get('/settings', 'SettingController@index')->name('settings');
    Route::put('/settings', 'SettingController@update')->name('settings.update');
});

