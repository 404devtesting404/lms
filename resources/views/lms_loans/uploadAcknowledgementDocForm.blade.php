@extends('layouts.master')
@section('page_title', 'Upload Acknowledgement Docs')
@section('content')
    <style>
        .grid1{
            border-radius: 10px;
            padding: 10px;
            border: 1px solid lightskyblue;

        }
        .panel-primary{
            padding: 10px;
        }
    </style>

    <div class="card card-default">
        <div class="card-header">
            <span class="card-title">Upload Acknowledgement Docs</span>
        </div>
        <div class="card-body">
            <div class="col-12">
                <div class="card">
                    <div class="card-body" id="PrintEmployeeList">
                        <div class="row">

                            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
                                <form method="post" action="{{ route('loans.upload_acknowledgement_docs', $loan_id) }}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-12 text-right">
                                            @foreach($docs as $val)
                                                 <div id="inputFormRow">
                                                    <div class="input-group mb-3">
                                                        <input type="text" name="doc_name[]" value="{{$val->doc_name}}" class="form-control m-input" placeholder="Enter title" autocomplete="off">
                                                        <div class="input-group-append">
                                                            <button id="removeRow" type="button" class="btn btn-danger">Remove</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                            <div id="newRow"></div>

                                            <br> <br>
                                            <button type="submit" class="btn btn-success">Submit</button>
                                        </div>

                                    </div>

                                </form>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                <button id="addRow" type="button" class="btn btn-info">Add Row</button>
                            </div>
                        </div>
                        <br>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        // add row
        $("#addRow").click(function () {
            var html = '';
            html += '<div id="inputFormRow">';
            html += '<div class="input-group mb-3">';
            html += '<input type="text" name="doc_name[]" class="form-control m-input" placeholder="Enter title" autocomplete="off">';
            html += '<div class="input-group-append">';
            html += '<button id="removeRow" type="button" class="btn btn-danger">Remove</button>';
            html += '</div>';
            html += '</div>';

            $('#newRow').append(html);
        });

        // remove row
        $(document).on('click', '#removeRow', function () {
            $(this).closest('#inputFormRow').remove();
        });
    </script>
@endsection
