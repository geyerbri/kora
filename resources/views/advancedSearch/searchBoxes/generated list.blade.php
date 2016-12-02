<div class="panel panel-default">
    <div class="panel-heading">
        <div class="checkbox">
            <label style="font-size:1.25em;"><input type="checkbox" name="{{$field->flid}}_dropdown"> {{$field->name}}</label>
        </div>
    </div>
    <div id="input_collapse_{{$field->flid}}" style="display: none;">
        <div class="panel-body">
            <div class="form-group">
                <label for="{{$field->flid}}_input">Select options:</label></br>
                {!! Form::select( $field->flid . "_input[]", \App\GeneratedListField::getList($field, false), "", ["class" => "form-control", "Multiple", 'id' => $field->flid."_input", "style" => "width: 100%"]) !!}
            </div>
            Input is: <span id="{{$field->flid}}_valid_selection">invalid</span>.
        </div>
    </div>
    <input type="hidden" id="{{$field->flid}}_valid" name="{{$field->flid}}_valid" value="0">
</div>
<script>
    $("#{{$field->flid}}_input").select2({tags:true});
    $("#{{$field->flid}}_input").change(function() {
        if (this.value == "") {
            $("#{{$field->flid}}_valid_selection").html("invalid");
            $("#{{$field->flid}}_valid").val("0");
        }
        else {
            $("#{{$field->flid}}_valid_selection").html("valid");
            $("#{{$field->flid}}_valid").val("1");
        }
    });
</script>