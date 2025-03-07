<?php

namespace App\Http\Controllers;

use App\Models\LoanTakaful;
use Illuminate\Http\Request;

/**
 * Class LoanTakafulController
 * @package App\Http\Controllers
 */
class LoanTakafulController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        $loanTakafuls = LoanTakaful::orderBy('id')->get();

//        dd($loanTakafuls);
        return view('loan-takaful.index');
    }
     public function takaful(Request $request)
    {
        $query = LoanTakaful::select('*')->join('loan_history', 'loan_history.id' ,'=', 'loan_takaful.loan_id');

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $type = $request->input('type');

        if ($startDate && $endDate) {
            $query->orWhereBetween('end_date', [$startDate, $endDate]);
        }


        $loanTakafuls = $query->where('loan_history.loan_status_id', '!=', 7)->orderBy('loan_id')->with('loan_history')->get();
        $loanTakafuls = $loanTakafuls->where('type', $type);

        return view('loan-takaful.index', compact('loanTakafuls', 'startDate', 'endDate', 'type'));
    }

    public function storetakafulpolicy(Request $request){
//        dd($request->all());
        $id = $request->get("tak_id");
        $val = $request->get("tak_val");
        $loanId = $request->get("loan_id");
        LoanTakaful::where('id',$id)->update(['policy_number'=>$val]);
        return true;
//        return redirect()->route('loans.menu', $loanId)
//            ->with('flash_success', 'LoanTakaful created successfully.');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $loanTakaful = new LoanTakaful();
        return view('loan-takaful.create', compact('loanTakaful'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(LoanTakaful::$rules);

        $loanTakaful = LoanTakaful::create($request->all());

        return redirect()->route('loan-takaful')
            ->with('flash_success', 'LoanTakaful created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $loanTakaful = LoanTakaful::find($id);

        return view('loan-takaful.show', compact('loanTakaful'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $loanTakaful = LoanTakaful::find($id);

        return view('loan-takaful.edit', compact('loanTakaful'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  LoanTakaful $loanTakaful
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        request()->validate(LoanTakaful::$rules);
        $loanTakaful = LoanTakaful::find($id);

        $data = $request->all();
        unset($data['_method']);
        unset($data['_token']);
//        dd($data);
        $loanTakaful->update($data);

        return redirect()->route('loan-takaful.index')
            ->with('flash_success', 'LoanTakaful updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $loanTakaful = LoanTakaful::find($id)->delete();

        return redirect()->route('loan-takaful')
            ->with('flash_success', 'LoanTakaful deleted successfully');
    }
}
