<?php

namespace App\Http\Controllers;

use App\Models\CompanyIncomeEst;
use Illuminate\Http\Request;

/**
 * Class CompanyIncomeEstController
 * @package App\Http\Controllers
 */
class CompanyIncomeEstController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $companyIncomeEsts = CompanyIncomeEst::get();

        return view('company-income-est.index', compact('companyIncomeEsts'))->with('i');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $companyIncomeEst = new CompanyIncomeEst();
        return view('company-income-est.create', compact('companyIncomeEst'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(CompanyIncomeEst::$rules);

        $companyIncomeEst = CompanyIncomeEst::create($request->all());

        return redirect()->route('company-income-ests.index')
            ->with('flash_success', 'CompanyIncomeEst created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $companyIncomeEst = CompanyIncomeEst::find($id);

        return view('company-income-est.show', compact('companyIncomeEst'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $companyIncomeEst = CompanyIncomeEst::find($id);

        return view('company-income-est.edit', compact('companyIncomeEst'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  CompanyIncomeEst $companyIncomeEst
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CompanyIncomeEst $companyIncomeEst)
    {
        request()->validate(CompanyIncomeEst::$rules);

        $companyIncomeEst->update($request->all());

        return redirect()->route('company-income-ests.index')
            ->with('flash_success', 'CompanyIncomeEst updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $companyIncomeEst = CompanyIncomeEst::find($id)->delete();

        return redirect()->route('company-income-ests.index')
            ->with('flash_success', 'CompanyIncomeEst deleted successfully');
    }
}
