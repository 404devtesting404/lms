<?php

namespace App\Http\Controllers;

use App\Models\CompanyValuation;
use Illuminate\Http\Request;

/**
 * Class CompanyValuationController
 * @package App\Http\Controllers
 */
class CompanyValuationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $companyValuations = CompanyValuation::get();

        return view('company-valuation.index', compact('companyValuations'))->with('i');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $companyValuation = new CompanyValuation();
        return view('company-valuation.create', compact('companyValuation'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(CompanyValuation::$rules);

        $companyValuation = CompanyValuation::create($request->all());

        return redirect()->route('company-valuations.index')
            ->with('flash_success', 'CompanyValuation created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $companyValuation = CompanyValuation::find($id);

        return view('company-valuation.show', compact('companyValuation'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $companyValuation = CompanyValuation::find($id);

        return view('company-valuation.edit', compact('companyValuation'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  CompanyValuation $companyValuation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CompanyValuation $companyValuation)
    {
        request()->validate(CompanyValuation::$rules);

        $companyValuation->update($request->all());

        return redirect()->route('company-valuations.index')
            ->with('flash_success', 'CompanyValuation updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $companyValuation = CompanyValuation::find($id)->delete();

        return redirect()->route('company-valuations.index')
            ->with('flash_success', 'CompanyValuation deleted successfully');
    }
}
