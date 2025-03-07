<?php

namespace App\Http\Controllers;

use App\Models\LoanModification;
use Illuminate\Http\Request;

/**
 * Class LoanModificationController
 * @package App\Http\Controllers
 */
class LoanModificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $loanModifications = LoanModification::get();

        return view('loan-modification.index', compact('loanModifications'))->with('i');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $loanModification = new LoanModification();
        return view('loan-modification.create', compact('loanModification'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(LoanModification::$rules);

        $loanModification = LoanModification::create($request->all());

        return redirect()->route('loan-modifications.index')
            ->with('flash_success', 'LoanModification created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $loanModification = LoanModification::find($id);

        return view('loan-modification.show', compact('loanModification'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $loanModification = LoanModification::find($id);

        return view('loan-modification.edit', compact('loanModification'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  LoanModification $loanModification
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LoanModification $loanModification)
    {
        request()->validate(LoanModification::$rules);

        $loanModification->update($request->all());

        return redirect()->route('loan-modifications.index')
            ->with('flash_success', 'LoanModification updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $loanModification = LoanModification::find($id)->delete();

        return redirect()->route('loan-modifications.index')
            ->with('flash_success', 'LoanModification deleted successfully');
    }
}
