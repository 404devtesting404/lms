@extends('layouts.master')
@section('page_title', 'Add Borrower')
@section('content')

    @php
        $name = explode(' ', $data['data']['user']['name'])
    @endphp
    <div class="card card-default">
        <div class="card-header">
            <span class="card-title">Add Borrower in LMS</span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('loans.storenewloan') }}"  role="form" enctype="multipart/form-data">
                @csrf
                <input type="hidden" value="{{ $data['data']['application_number'] }}" name="los_app_id">

                <div class="box box-info padding-1">
                    <div class="box-body">

                        <div class="form-group">
                            <label for="fname">First Name:</label>
                            <input type="text" id="fname" name="fname" class="form-control" required placeholder="First Name" readonly value="{{ $name[0] ?? '' }}" />
                        </div>
                        <div class="form-group">
                            <label for="mname">Middle Name:</label>
                            <input type="text" id="fname" name="mname" class="form-control" placeholder="Middle Name" readonly value="{{ $name[1] ?? '' }}" />
                        </div>
                        <div class="form-group">
                            <label for="lname">Last Name:</label>
                            <input type="text" id="fname" name="lname" class="form-control" required placeholder="Last Name" readonly value="{{ $name[2] ?? '' }} {{ $name[3] ?? '' }}" />
                        </div>
                        <div class="form-group">
                            <label for="cnic">CNIC:</label>
                            <input type="number" id="cnic" name="cnic" class="form-control" required placeholder="CNIC" readonly value="{{ $data['data']['user']['cnic'] }}" />
                        </div>
                        <div class="form-group">
                            <label for="gender">Gender:</label>
                            <select id="gender" class="form-control" readonly name="gender">
                                <option value="M" {{ $data['data']['user']['gender_id'] == 1 ? 'selected' : '' }}>Male</option>
                                <option value="F" {{ $data['data']['user']['gender_id'] == 2 ? 'selected' : '' }}>Female</option>
                                <option value="O" {{ $data['data']['user']['gender_id'] == 3 ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="dob">Date Of Birth:</label>
                            <input type="date" id="dob" name="dob" class="form-control" required placeholder="DOB" readonly value="{{ date('Y-m-d', strtotime($data['data']['user']['dob'])) }}" />
                        </div>
                        <div class="form-group">
                            <label for="disb_amount">Disbursed Amount:</label>
                            <input type="text" id="disb_amount" name="disb_amount" class="form-control" required placeholder="Disbursed Amount" readonly value="{{ $data['data']['loan_amount'] }}" />
                        </div>
                        <div class="form-group">
                            <label for="loan_period">Loan Period (Months):</label>
                            <input type="text" id="loan_period" min="12" max="240" name="loan_period" class="form-control" required placeholder="Loan Period" readonly value="{{ $data['data']['period'] * 12 }}" />
                        </div>
                        <div class="form-group">
                            <label for="musharkah_date">Mushakah Date:</label>
                            <input type="date" id="musharkah_date" name="musharkah_date" class="form-control" required placeholder="Musharkah Date" />
                        </div>
                        <div class="form-group">
                            <label for="fin_amount">Property Amount:</label>
                            <input type="text" id="fin_amount" name="fin_amount" class="form-control" required placeholder="Property Amount" value="{{ $data['data']['total_share'] }}" readonly />
                        </div>
                        <div class="form-group">
                            <label for="loan_type">Loan Type:</label>
                            <select id="loan_type" class="form-control" name="loan_type" readonly>
                                <option value="1" {{ $data['data']['product_id'] == 1 ? 'selected' : '' }}>Home Purchase</option>
                                <option value="2" {{ $data['data']['product_id'] == 2 ? 'selected' : '' }}>Home Renovation</option>
                                <option value="3" {{ $data['data']['product_id'] == 3 ? 'selected' : '' }}>Home Construction</option>
                                <option value="4" {{ $data['data']['product_id'] == 4 ? 'selected' : '' }}>Home Land + Construction</option>
                                <option value="5" {{ $data['data']['product_id'] == 5 ? 'selected' : '' }}>Balance Transfer</option>
                                <option value="6" {{ $data['data']['product_id'] == 6 ? 'selected' : '' }}>Balance Transfer (Staff To Commercial)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="kibor_revision_cycle">Kibor Revision Cycle:</label>
                            <select id="kibor_revision_cycle" class="form-control" name="kibor_revision_cycle" readonly>
                                <option value="1" {{ $data['data']['renewal_of_kibor'] = 1 ? 'selected' : '' }}>1 Year</option>
                                <option value="3" {{ $data['data']['renewal_of_kibor'] = 3 ? 'selected' : '' }}>3 Years</option>
                                <option value="5" {{ $data['data']['renewal_of_kibor'] = 5 ? 'selected' : '' }}>5 Years</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="kibor_revision_date">Kibor Revision Date:</label>
                            <input type="date" id="kibor_revision_date" name="kibor_revision_date" class="form-control" required placeholder="Kibor Revision Date" />
                        </div>

                    </div>
                    <div class="box-footer mt20">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
<script>//
//    $(document).ready(function() {
//        $(":input").inputmask();
//    });
</script>
@endsection
