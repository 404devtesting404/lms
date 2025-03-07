<?php

namespace App\Http\Controllers;

use App\Models\CompanyLegal;
use Illuminate\Http\Request;

/**
 * Class CompanyLegalController
 * @package App\Http\Controllers
 */
class CompanyLegalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $companyLegals = CompanyLegal::get();

        return view('company-legal.index', compact('companyLegals'))->with('i');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $companyLegal = new CompanyLegal();
        return view('company-legal.create', compact('companyLegal'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(CompanyLegal::$rules);

        $companyLegal = CompanyLegal::create($request->all());

        return redirect()->route('company-legals.index')
            ->with('flash_success', 'CompanyLegal created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $companyLegal = CompanyLegal::find($id);

        return view('company-legal.show', compact('companyLegal'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $companyLegal = CompanyLegal::find($id);

        return view('company-legal.edit', compact('companyLegal'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  CompanyLegal $companyLegal
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CompanyLegal $companyLegal)
    {
        request()->validate(CompanyLegal::$rules);

        $companyLegal->update($request->all());

        return redirect()->route('company-legals.index')
            ->with('flash_success', 'CompanyLegal updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $companyLegal = CompanyLegal::find($id)->delete();

        return redirect()->route('company-legals.index')
            ->with('flash_success', 'CompanyLegal deleted successfully');
    }
}
