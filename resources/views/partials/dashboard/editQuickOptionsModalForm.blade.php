{!! csrf_field() !!}
<input type="hidden" name="_method" value="PATCH">

<div class="card">
    <div class="left">
        <div class="move-actions">
            <a><i class="icon icon-arrow-up"></i></a>
            <a><i class="icon icon-arrow-down"></i></a>
        </div>
        <a class="title underline-middle-hover" href="#">Quick Option</a>
    </div>
</div>

<div class="form-group mt-xxl">
    {!! Form::submit('Update Quick Actions',['class' => 'btn disabled edit-quick-actions-submit-js']) !!}
</div>