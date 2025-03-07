@extends('layouts.master')
@section('page_title', 'Takaful Schedule')
@section('content')

<div class="card card-default">
    <div class="card-header">
        <span class="card-title">Takaful Schedule</span>
    </div>
    <div class="card-body">
        <table class="table table-hover" width="100%">
            <tr>
                <th>Sr#</th>
                <th>Type</th>
                <th>Issue Date</th>
                <th>Policy Number</th>
                <th>Covered Amount</th>
                <th>Expiry Date</th>

            </tr>
            <input type="hidden" value="{{ csrf_token() }}" id="_token" />
            <input type="hidden" value="{{ $loan_id }}" id="loan_id" />
            @foreach($takaful as $row)
            <tr>
                <td>{{ $i++ }}</td>
                <td>{{ $row->type==1 ? "Life" : "Property" }}</td>
                <td>{{ date("d M Y", strtotime($row->start_date)) }}</td>
                <td>
                    <div class="row col-12">
                        <div class="col-8">
                            <input type="hidden" value="{{ $row->id }}" id="id_{{ $row->id }}" name="id[]" />
                            <input type="text" class="form-control" value="{{ $row->policy_number }}" id="val_{{ $row->id }}" name="policy_number[]" />

                        </div>
                        <div class="col-4">
                            <input type="button" class="col-12 btn btn-success" value="Save" onclick="updateval({{ $row->id }})" />

                        </div>
                    </div>
                </td>
                <td>{{ number_format($row->covered_amount,0) }}</td>
                <td>{{ date("d M Y", strtotime($row->end_date)) }}</td>
            </tr>
            @endforeach

        </table>
    </div>
</div>
<script>
    function updateval(id){
//        alert(id);
        var tak_id = $("#id_"+id).val();
        var tak_val = $("#val_"+id).val();
        var loan_id = $("#loan_id").val();
        var payload = {"_token": $("#_token").val(), tak_id: tak_id, tak_val: tak_val, loan_id: loan_id};
        // $.post("/loans/takaful/storetakafulpolicy", payload, function (data) {
        //     alert("Successfully Saved");
        //     location.reload();
        // });
        $.ajax({
            url: "{{route('storetakafulpolicy')}}",
            method: 'POST',
            data: payload,
            success: function (data) {
                alert("Successfully Saved");
                // location.reload();
            }
        });
    }
</script>
@endsection
