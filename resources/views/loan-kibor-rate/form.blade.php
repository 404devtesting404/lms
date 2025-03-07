<div class="box box-info padding-1">
    <div class="box-body">
        
        <div class="form-group">
            {{ Form::label('kibor_rate') }}
<!--            {{ Form::number('kibor_rate', $loanKiborRate->kibor_rate, ['class' => 'form-control' . ($errors->has('kibor_rate') ? ' is-invalid' : ''), 'placeholder' => 'Kibor Rate']) }}-->
            <input type="text" name="kibor_rate" id="kibor_rate" value="{{ $loanKiborRate->kibor_rate }}" class="form-control" />
            {!! $errors->first('kibor_rate', '<div class="invalid-feedback">:message</p>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('start_date') }}
            {{ Form::date('start_date', $loanKiborRate->start_date, ['class' => 'form-control' . ($errors->has('start_date') ? ' is-invalid' : ''), 'placeholder' => 'Start Date']) }}
            {!! $errors->first('start_date', '<div class="invalid-feedback">:message</p>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('end_date') }}
            {{ Form::date('end_date', $loanKiborRate->end_date, ['class' => 'form-control' . ($errors->has('end_date') ? ' is-invalid' : ''), 'placeholder' => 'End Date']) }}
            {!! $errors->first('end_date', '<div class="invalid-feedback">:message</p>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('status') }}
            {{ Form::select('status', array('1' => 'Active', '0' => 'InActive'),null, ['class' => 'form-control select'] ); }}
            
            {!! $errors->first('status', '<div class="invalid-feedback">:message</p>') !!}
        </div>

    </div>
    <div class="box-footer mt20">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</div>