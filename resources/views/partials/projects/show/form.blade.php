<div class="form card {{ $index == 0 ? 'active' : '' }}" id="{{$form->fid}}">
  <div class="header {{ $index == 0 ? 'active' : '' }}">
    <div class="left {{ !$isCustom ? 'pl-m' : null}}">
      @if ($isCustom)
        <div class="move-actions">
          <a class="action move-action-js up-js" href="">
            <i class="icon icon-arrow-up"></i>
          </a>

          <a class="action move-action-js down-js" href="">
            <i class="icon icon-arrow-down"></i>
          </a>
        </div>
      @endif

      <a class="title {{!$active ? 'inactive': 'underline-middle-hover'}}" href="{{ action('FormController@show',['pid' => $project->pid, 'fid' => $form->fid]) }}">
        <span class="name">{{$form->name}}</span>
          <i class="icon icon-arrow-right"></i>
      </a>
    </div>

    <div class="card-toggle-wrap">
      <a href="#" class="card-toggle form-toggle-js">
        <i class="icon icon-chevron {{ $index == 0 ? 'active' : '' }}"></i>
      </a>
    </div>
  </div>

  <div class="content content-js {{ $index == 0 ? 'active' : '' }}">
    <div class="id">
      <span class="attribute">Nick Name: </span>
      <span>{{$form->slug}}</span>
    </div>

    <div class="description">
      {{$form->description}}
    </div>

    <div class="footer">
      @if(\Auth::user()->canEditForms($project))
        <a class="quick-action underline-middle-hover" href="{{ action('FormController@edit',['pid' => $project->pid, 'fid' => $form->fid]) }}">
          <i class="icon icon-edit-little"></i>
          <span>Edit Form Info</span>
        </a>
      @endif

      <a class="quick-action underline-middle-hover" href="#">
        <i class="icon icon-search"></i>
        <span>Form Records</span>
      </a>

      <a class="quick-action underline-middle-hover" href="#">
        <i class="icon icon-search"></i>
        <span>Create New Records</span>
      </a>

      <a class="quick-action underline-middle-hover" href="{{ action('FormController@show',['pid' => $project->pid, 'fid' => $form->fid]) }}">
        <span>Go to Form</span>
        <i class="icon icon-arrow-right"></i>
      </a>
    </div>
  </div>
</div>