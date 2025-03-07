<?php

namespace App\Http\Controllers;

use App\Models\LoansFee;
use Illuminate\Http\Request;

/**
 * Class LoansFeeController
 * @package App\Http\Controllers
 */
class LoansFeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $loansFees = LoansFee::get();

        return view('loans-fee.index', compact('loansFees'))->with('i');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $loansFee = new LoansFee();
        return view('loans-fee.create', compact('loansFee'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(LoansFee::$rules);

        $loansFee = LoansFee::create($request->all());

        return redirect()->route('loans-fees.index')
            ->with('flash_success', 'LoansFee created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $loansFee = LoansFee::find($id);

        return view('loans-fee.show', compact('loansFee'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $loansFee = LoansFee::find($id);

        return view('loans-fee.edit', compact('loansFee'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  LoansFee $loansFee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LoansFee $loansFee)
    {
        request()->validate(LoansFee::$rules);

        $loansFee->update($request->all());

        return redirect()->route('loans-fees.index')
            ->with('flash_success', 'LoansFee updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $loansFee = LoansFee::find($id)->delete();

        return redirect()->route('loans-fees.index')
            ->with('flash_success', 'LoansFee deleted successfully');
    }
}
