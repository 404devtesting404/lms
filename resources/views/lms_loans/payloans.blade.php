@extends('layouts.master')
@section('page_title', 'Installment Posting')
@section('content')

<div class="card card-default">
    <div class="card-header">
        <span class="card-title">Installment Posting</span>
    </div>
    @if (session('message'))
        <div class="alert alert-danger">{{ session('message') }}</div>
    @endif
    <div class="card-body">
        <form method="POST" action="{{ route('loans.postbulkpayment') }}"  role="form" enctype="multipart/form-data">
        @csrf

        <table class="table table-hover" width="100%">
            <tr>
                <th></th>
                <th>Sr#</th>
                <th>Installment#</th>
                <th>Name</th>
                <th>Due Date</th>
                <th style="text-align: right !important;">Principle</th>
                <th style="text-align: right !important;">Profit</th>
                <th style="text-align: right !important;">Installment Amount</th>
            </tr>

            @foreach($loanData as $row)
            <tr <?php echo $i%2==0 ? 'style="background-color:#BEE5EB !important; font-weight: 800;"' : 'style="background-color:#dac484 !important; font-weight: 800;" ' ?>>
                <td><input type="checkbox" name="payset[]" value="{{ $row }}" /></td>
                <td>{{ ++$i }}</td>
                <td>{{ $row->installment_no }}</td>
                <td>{{ $row->name }}</td>
                <td>{{ date("d M Y", strtotime($row->due_date)) }}</td>
                <td align="right">{{ number_format($row->amount_pr,0) }}</td>
                <td align="right">{{ number_format($row->amount_mu,0) }}</td>
                <td align="right">{{ number_format(($row->amount_pr+$row->amount_mu),0) }}</td>
            </tr>
            @endforeach
            @if(!$loanData)
            <tr>
                <td>No Data Found</td>
            </tr>

            @endif
        </table>
        <br>
        <button class="btn btn-success col-md-12" type="submit">Pay the Installments</button> 
        </form>
    </div>
</div>
<script>
    function updateval(id){
//        alert(id);
        var tak_id = $("#id_"+id).val();
        var tak_val = $("#val_"+id).val();
        var loan_id = $("#loan_id").val();
        var payload = {"_token": $("#_token").val(), tak_id: tak_id, tak_val: tak_val, loan_id: loan_id};
        $.post("/loans/takaful/storetakafulpolicy", payload, function (data) {
            alert("Successfully Saved");
            location.reload();
        });
    }
</script>
@endsection
