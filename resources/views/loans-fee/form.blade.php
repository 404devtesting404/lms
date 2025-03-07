<div class="box box-info padding-1">
    <div class="box-body">
        
        <div class="form-group">
            {{ Form::label('loanId') }}
            {{ Form::text('loanId', $loansFee->loanId, ['class' => 'form-control' . ($errors->has('loanId') ? ' is-invalid' : ''), 'placeholder' => 'Loanid']) }}
            {!! $errors->first('loanId', '<div class="invalid-feedback">:message</p>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('processingFees') }}
            {{ Form::text('processingFees', $loansFee->processingFees, ['class' => 'form-control' . ($errors->has('processingFees') ? ' is-invalid' : ''), 'placeholder' => 'Processingfees']) }}
            {!! $errors->first('processingFees', '<div class="invalid-feedback">:message</p>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('processingFeesStatus') }}
            {{ Form::text('processingFeesStatus', $loansFee->processingFeesStatus, ['class' => 'form-control' . ($errors->has('processingFeesStatus') ? ' is-invalid' : ''), 'placeholder' => 'Processingfeesstatus']) }}
            {!! $errors->first('processingFeesStatus', '<div class="invalid-feedback">:message</p>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('fedFees') }}
            {{ Form::text('fedFees', $loansFee->fedFees, ['class' => 'form-control' . ($errors->has('fedFees') ? ' is-invalid' : ''), 'placeholder' => 'Fedfees']) }}
            {!! $errors->first('fedFees', '<div class="invalid-feedback">:message</p>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('fedFeesStatus') }}
            {{ Form::text('fedFeesStatus', $loansFee->fedFeesStatus, ['class' => 'form-control' . ($errors->has('fedFeesStatus') ? ' is-invalid' : ''), 'placeholder' => 'Fedfeesstatus']) }}
            {!! $errors->first('fedFeesStatus', '<div class="invalid-feedback">:message</p>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('legalFeesCompanyId') }}
            {{ Form::text('legalFeesCompanyId', $loansFee->legalFeesCompanyId, ['class' => 'form-control' . ($errors->has('legalFeesCompanyId') ? ' is-invalid' : ''), 'placeholder' => 'Legalfeescompanyid']) }}
            {!! $errors->first('legalFeesCompanyId', '<div class="invalid-feedback">:message</p>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('legalFees') }}
            {{ Form::text('legalFees', $loansFee->legalFees, ['class' => 'form-control' . ($errors->has('legalFees') ? ' is-invalid' : ''), 'placeholder' => 'Legalfees']) }}
            {!! $errors->first('legalFees', '<div class="invalid-feedback">:message</p>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('legalFeesStatus') }}
            {{ Form::text('legalFeesStatus', $loansFee->legalFeesStatus, ['class' => 'form-control' . ($errors->has('legalFeesStatus') ? ' is-invalid' : ''), 'placeholder' => 'Legalfeesstatus']) }}
            {!! $errors->first('legalFeesStatus', '<div class="invalid-feedback">:message</p>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('valuationCompanyId') }}
            {{ Form::text('valuationCompanyId', $loansFee->valuationCompanyId, ['class' => 'form-control' . ($errors->has('valuationCompanyId') ? ' is-invalid' : ''), 'placeholder' => 'Valuationcompanyid']) }}
            {!! $errors->first('valuationCompanyId', '<div class="invalid-feedback">:message</p>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('valuationFees') }}
            {{ Form::text('valuationFees', $loansFee->valuationFees, ['class' => 'form-control' . ($errors->has('valuationFees') ? ' is-invalid' : ''), 'placeholder' => 'Valuationfees']) }}
            {!! $errors->first('valuationFees', '<div class="invalid-feedback">:message</p>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('valuationFeesStatus') }}
            {{ Form::text('valuationFeesStatus', $loansFee->valuationFeesStatus, ['class' => 'form-control' . ($errors->has('valuationFeesStatus') ? ' is-invalid' : ''), 'placeholder' => 'Valuationfeesstatus']) }}
            {!! $errors->first('valuationFeesStatus', '<div class="invalid-feedback">:message</p>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('incomeEstCompanyId') }}
            {{ Form::text('incomeEstCompanyId', $loansFee->incomeEstCompanyId, ['class' => 'form-control' . ($errors->has('incomeEstCompanyId') ? ' is-invalid' : ''), 'placeholder' => 'Incomeestcompanyid']) }}
            {!! $errors->first('incomeEstCompanyId', '<div class="invalid-feedback">:message</p>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('incomeEstFees') }}
            {{ Form::text('incomeEstFees', $loansFee->incomeEstFees, ['class' => 'form-control' . ($errors->has('incomeEstFees') ? ' is-invalid' : ''), 'placeholder' => 'Incomeestfees']) }}
            {!! $errors->first('incomeEstFees', '<div class="invalid-feedback">:message</p>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('incomeEstFeesStatus') }}
            {{ Form::text('incomeEstFeesStatus', $loansFee->incomeEstFeesStatus, ['class' => 'form-control' . ($errors->has('incomeEstFeesStatus') ? ' is-invalid' : ''), 'placeholder' => 'Incomeestfeesstatus']) }}
            {!! $errors->first('incomeEstFeesStatus', '<div class="invalid-feedback">:message</p>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('stampPaperFees') }}
            {{ Form::text('stampPaperFees', $loansFee->stampPaperFees, ['class' => 'form-control' . ($errors->has('stampPaperFees') ? ' is-invalid' : ''), 'placeholder' => 'Stamppaperfees']) }}
            {!! $errors->first('stampPaperFees', '<div class="invalid-feedback">:message</p>') !!}
        </div>

    </div>
    <div class="box-footer mt20">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</div>