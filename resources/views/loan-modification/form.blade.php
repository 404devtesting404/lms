<div class="box box-info padding-1">
    <div class="box-body">
        
        <div class="form-group">
            {{ Form::label('loan_id') }}
            {{ Form::text('loan_id', $loanModification->loan_id, ['class' => 'form-control' . ($errors->has('loan_id') ? ' is-invalid' : ''), 'placeholder' => 'Loan Id']) }}
            {!! $errors->first('loan_id', '<div class="invalid-feedback">:message</p>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('modification') }}
            {{ Form::text('modification', $loanModification->modification, ['class' => 'form-control' . ($errors->has('modification') ? ' is-invalid' : ''), 'placeholder' => 'Modification']) }}
            {!! $errors->first('modification', '<div class="invalid-feedback">:message</p>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('amount') }}
            {{ Form::text('amount', $loanModification->amount, ['class' => 'form-control' . ($errors->has('amount') ? ' is-invalid' : ''), 'placeholder' => 'Amount']) }}
            {!! $errors->first('amount', '<div class="invalid-feedback">:message</p>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('modify_by') }}
            {{ Form::text('modify_by', $loanModification->modify_by, ['class' => 'form-control' . ($errors->has('modify_by') ? ' is-invalid' : ''), 'placeholder' => 'Modify By']) }}
            {!! $errors->first('modify_by', '<div class="invalid-feedback">:message</p>') !!}
        </div>

    </div>
    <div class="box-footer mt20">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</div>