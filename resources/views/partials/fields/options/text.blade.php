@extends('fields.show')

@section('presetModal')
    @include('partials.fields.fieldValuePresetModals.addRegexPresetModal', ['presets' => $presets])
    @include('partials.fields.fieldValuePresetModals.createRegexPresetModal')
@stop

@section('fieldOptions')
    <div class="form-group single-line-js">
        {!! Form::label('default','Default') !!}
        <span class="error-message single-line"></span>
        {!! Form::text('default', $field->default, ['class' => 'text-input text-default-js', 'placeholder' => 'Enter default value here']) !!}
    </div>

    <div class="form-group multi-line-js hidden">
        {!! Form::label('default','Default') !!}
        <span class="error-message multi-line"></span>
        {!! Form::textarea('default', null, ['class' => 'text-area text-area-default text-area-default-js', 'placeholder' => "Enter default value here"]) !!}
    </div>

    <div class="form-group mt-xl">
        {!! Form::label('regex','Regex') !!}
        <span class="error-message"></span>
        {!! Form::text('regex', \App\Http\Controllers\FieldController::getFieldOption($field,'Regex'), ['class' => 'text-input text-regex-js', 'placeholder' => 'Enter regular expression pattern here']) !!}
        <div><a href="#" class="field-preset-link open-regex-modal-js">Use a Value Preset for this Regex</a></div>
        <div><a href="#" class="field-preset-link open-create-regex-modal-js right
            @if(\App\Http\Controllers\FieldController::getFieldOption($field,'Regex')=='') disabled @endif">
                Create a New Value Preset from this Regex</a></div>
    </div>

    <div class="form-group mt-xxxl">
        <label for="multi">Multilined?</label>
        <div class="check-box">
            <input type="checkbox" value="1" id="preset" class="check-box-input" name="multi" {{\App\Http\Controllers\FieldController::getFieldOption($field,'MultiLine') ? 'checked': ''}} />
            <div class="check-box-background"></div>
            <span class="check"></span>
            <span class="placeholder">Select to set the field as multilined</span>
            <span class="placeholder-alt">Field is set to be multilined</span>
        </div>
    </div>
@stop

@section('fieldOptionsJS')
    Kora.Fields.Options('Text');
@stop