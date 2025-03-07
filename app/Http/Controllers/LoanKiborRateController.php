<?php

namespace App\Http\Controllers;

use App\Models\LoanKiborRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class LoanKiborRateController
 * @package App\Http\Controllers
 */
class LoanKiborRateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        dd("ok");
        $loanKiborRates = LoanKiborRate::orderBy('status','desc')->get();

        return view('loan-kibor-rate.index', compact('loanKiborRates'))->with('i');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $loanKiborRate = new LoanKiborRate();
        return view('loan-kibor-rate.create', compact('loanKiborRate'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(LoanKiborRate::$rules);

        if($request->get("status")){
//            LoanKiborRate::update(['status'=>0]);
            DB::table('loan_kibor_rates')->update(['status'=>0]);
            
        }
//        dd($request->all());
        $loanKiborRate = LoanKiborRate::create($request->all());

        return redirect()->route('kibor.all')
            ->with('flash_success', 'LoanKiborRate created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $loanKiborRate = LoanKiborRate::find($id);

        return view('loan-kibor-rate.show', compact('loanKiborRate'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $loanKiborRate = LoanKiborRate::find($id);

        return view('loan-kibor-rate.edit', compact('loanKiborRate'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  LoanKiborRate $loanKiborRate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
//        dd($request->all());
        request()->validate(LoanKiborRate::$rules);

        if($request->get("status")){
//            LoanKiborRate::update(['status'=>0]);
            DB::table('loan_kibor_rates')->update(['status'=>0]);
        }
        $loanKiborRate = LoanKiborRate::find($id);
//        print_r((array) $loanKiborRate);
//        dd($request->all());
        $loanKiborRate->update($request->all());

        return redirect()->route('kibor.all')
            ->with('flash_success', 'LoanKiborRate updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $loanKiborRate = LoanKiborRate::find($id)->delete();

        return redirect()->route('kibor.all')
            ->with('flash_success', 'LoanKiborRate deleted successfully');
    }
}
