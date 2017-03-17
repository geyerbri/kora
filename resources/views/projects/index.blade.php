@extends('app')

@section('content')
    <h1>{{trans('projects_index.projects')}}</h1>

    <hr/>

    @include('projectSearch.bar', ['projectArrays' => $projectArrays])

    @include('partials.adminpanel')

    <hr/>
    @if(\Auth::user()->admin)
        <div>
            <a href="{{ action('ProjectController@importProjectView') }}">[{{trans('projects_index.import')}}]</a>
        </div>
    @endif
    <div id="toggle_proj">
        Toggle Inactive: <input type="checkbox" id="toggle_proj_check">
    </div> <br>
    @if(sizeof($requestProjects)>0)
        @if(!$hasProjects)
            <div id="access_div">
                You currently don't have permissions to work on any projects, would you like to <a id="access">request access to a project</a>?
            </div>
        @else
            <div id="access_div">
                Don't see the project you need to work on? <a id="access">Request access to a project</a>.
            </div>
        @endif
        <div id="request_project_div" style="display:none">
            {!! Form::open(['action' => 'ProjectController@request']) !!}
            <select multiple class="form-control" id="request_project" name="pid[]" style="width:100%">
                @foreach($requestProjects as $name=>$pid)
                    <option value="{{$pid}}">{{$name}}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary">Request Access</button>
            {!! Form::close() !!}
        </div>
        <br>
    @elseif(\Auth::user()->id!=1)
        @if(!$hasProjects)
            <div>You currently don't have permissions to work on any projects. There are currently no projects for you to request. Please contact your Kora administrator to request project creation. </div>
        @else
            <div>Don't see the project you need to work on? There are currently no projects for you to request. Please contact your Kora administrator to request project creation.</div>
        @endif
        <br>
    @endif

    @foreach ($projects as $project)
            @if($project->active==1)
            <div class="panel panel-default">
                <div class="panel-heading">
                    <a href="{{ action('ProjectController@show',[$project->pid]) }}" style="font-size: 1.5em;">{{ $project->name }}</a>
                </div>
                <div class="collapseTest" style="display:none">
                <div class="panel-body">
                    <span><b>{{trans('projects_index.status')}}:</b> </span>
                    <span style="color:green">{{trans('projects_index.active')}}</span>
                    <div><b>{{trans('projects_index.desc')}}:</b> {{ $project->description }}</div>
                </div>
            @else
            <div class="panel panel-default inactiveProj" style="display:none">
                <div class="panel-heading" style="font-size: 1.5em;">
                    {{ $project->name }}
                </div>
                <div class="collapseTest" style="display:none">
                <div class="panel-body">
                    <span><b>{{trans('projects_index.status')}}:</b> </span>
                    <span style="color:red">{{trans('projects_index.inactive')}}</span>
                    <div><b>{{trans('projects_index.desc')}}:</b> {{ $project->description }}</div>
                </div>
            @endif
            <div class="panel-footer">

                <span>
                    @if(\Auth::user()->admin) <a href="{{ action('ProjectController@edit',[$project->pid]) }}">[{{trans('projects_index.edit')}}]</a> @endif
                </span>
                <span>
                    @if(\Auth::user()->admin) <a onclick="deleteProject('{{ $project->name }}', {{ $project->pid }})" href="javascript:void(0)">[{{trans('projects_index.delete')}}]</a> @endif
                </span>
            </div></div><!-- this is the close tag for the collapseTest div -->
        </div>
    @endforeach

    <br/>

    <form action="{{ action('ProjectController@create') }}">
        @if(\Auth::user()->admin) <input type="submit" value="{{trans('projects_index.create')}}" class="btn btn-primary form-control"> @endif
    </form>

@stop

@section('footer')
    <script>
        $( ".panel-heading" ).on( "click", function() {
            if ($(this).siblings('.collapseTest').css('display') == 'none' ){
                $(this).siblings('.collapseTest').slideDown();
            }else {
                $(this).siblings('.collapseTest').slideUp();
            }
        });

        $( "#toggle_proj" ).on( "click", "#toggle_proj_check", function() {
            var checked = $(this).is(":checked");

            $('.inactiveProj').each(function () {
                if (checked){
                    $(this).slideDown();
                }else {
                    $(this).slideUp();
                }
            });
        });

        $('#request_project').select2();

        $( "#access_div" ).on( "click", "#access", function() {
            $("#access_div").slideUp();
            $("#request_project_div").slideDown();
        });

        function deleteProject(projName,pid) {
            var encode = $('<div/>').html("{{trans('projects_index.areyousure')}}").text();
            var response = confirm(encode + projName + "?");
            if (response) {
                $.ajax({
                    //We manually create the link in a cheap way because the JS isn't aware of the pid until runtime
                    //We pass in a blank project to the action array and then manually add the id
                    url: '{{ action('ProjectController@destroy',['']) }}/'+pid,
                    type: 'DELETE',
                    data: {
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function (result) {
                        location.reload();
                    }
                });
            }
        }
    </script>
@stop