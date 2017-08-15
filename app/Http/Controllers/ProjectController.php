<?php namespace App\Http\Controllers;

use App\User;
use App\Project;
use App\ProjectGroup;
use App\Http\Requests\ProjectRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProjectController extends Controller {

    /*
    |--------------------------------------------------------------------------
    | Project Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles projects within Kora3
    |
    */

    /**
     * Constructs controller and makes sure user is authenticated.
     */
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('active');
        $this->middleware('admin', ['except' => ['index', 'show', 'request']]);
    }

    /**
     * Gets the view for the main projects page.
     *
     * @return View
     */
	public function index() {
        $projectCollections = Project::all();

        $projectArrays = [];
        $projects = array();
        $hasProjects = false;
        $requestProjects = array();
        foreach($projectCollections as $project) {
            if(\Auth::user()->admin || \Auth::user()->inAProjectGroup($project)) {
                //TODO::$projectArrays[] = $project->buildFormSelectorArray(); //This data is used for project search
                array_push($projects,$project);
                $hasProjects = true;
            } else if($project->active) {
                $requestProjects[$project->name] = $project->pid;
            }
        }

        $c = new UpdateController();
        if($c->checkVersion() && !session('notified_of_update')) {
            session(['notified_of_update' => true]);
            flash()->overlay(trans('controller_update.updateneeded'), trans('controller_update.updateheader'));
        }

        return view('projects.index', compact('projects', 'projectArrays', 'hasProjects','requestProjects'));
	}

    /**
     * Sends an access request to admins of project(s).
     *
     * @param  Request $request
     * @return Redirect
     */
    public function request(Request $request) {
        $projects = array();
        if(!is_null($request->pid)) {
            foreach($request->pid as $pid) {
                $project = self::getProject($pid);
                if(!is_null($project))
                    array_push($projects, $project);
            }
        }

        if(sizeof($projects)==0) {
            flash()->overlay(trans('controller_project.requestfail'),trans('controller_project.whoops'));

            return redirect('projects');
        } else {
            foreach($projects as $project) {
                $admins = $this->getProjectAdminNames($project);

                foreach($admins as $user) {
                    Mail::send('emails.request.access', compact('project'), function ($message) use($user) {
                        $message->from(env('MAIL_FROM_ADDRESS'));
                        $message->to($user->email);
                        $message->subject('Kora Project Request');
                    });
                }
            }

            flash()->overlay(trans('controller_project.requestsuccess'),trans('controller_project.whoops'));

            return redirect('projects');
        }
    }

    /**
     * Gets the create view for a project.
     *
     * @return View
     */
	public function create() {
        $users = User::lists('username', 'id')->all();

        return view('projects.create', compact('users'));
	}

    /**
     * Saves a new project model to the DB.
     *
     * @param  ProjectRequest $request
     * @return Redirect
     */
	public function store(ProjectRequest $request) {
        $project = Project::create($request->all());

        $adminGroup = ProjectGroup::makeAdminGroup($project, $request);
        ProjectGroup::makeDefaultGroup($project);
        $project->adminGID = $adminGroup->id;
        $project->save();

        flash()->overlay(trans('controller_project.create'),trans('controller_project.goodjob'));

        return redirect('projects');
	}

    /**
     * Gets the view for an individual project page.
     *
     * @param  int $id - Project ID
     * @return View
     */
	public function show($id) {
        if(!self::validProj(($id))) {
            return redirect('/projects');
        }

        if(!FormController::checkPermissions($id)) {
            return redirect('/projects');
        }

        $project = self::getProject($id);
        $projectArrays = [$project->buildFormSelectorArray()];

        return view('projects.show', compact('project', 'projectArrays'));
	}

    /**
     * Gets the view for editing a project.
     *
     * @param  int $id - Project ID
     * @return View
     */
	public function edit($id) {
        if(!self::validProj(($id))) {
            return redirect()->action('ProjectController@Index');
        }

        $user = \Auth::user();
        $project = self::getProject($id);

        if(!$user->admin && !self::isProjectAdmin($user, $project)) {
            flash()->overlay(trans('controller_project.editper'), trans('controller_project.whoops'));
            return redirect()->action('ProjectController@Index');
        }

        return view('projects.edit', compact('project'));
	}

    /**
     * Updates an edited project.
     *
     * @param  int $id - Project ID
     * @param  ProjectRequest $request
     * @return Redirect
     */
	public function update($id, ProjectRequest $request) {
        $project = self::getProject($id);
        $project->update($request->all());

        ProjectGroupController::updateMainGroupNames($project);

        flash()->overlay(trans('controller_project.updated'),trans('controller_project.goodjob'));

        return redirect('projects');
	}

    /**
     * Deletes a project.
     *
     * @param  int $id - Project ID
     */
	public function destroy($id) {
        if(!self::validProj(($id))) {
            return redirect('/projects');
        }

        $user = \Auth::user();
        $project = self::getProject($id);

        if(!$user->admin && !self::isProjectAdmin($user, $project)) {
            flash()->overlay(trans('controller_project.deleteper'), trans('controller_project.whoops'));
            return redirect('/projects');
        }

        $project->delete();

        flash()->overlay(trans('controller_project.deleted'),trans('controller_project.goodjob'));
	}

    /**
     * Determines if user is an admin of the project.
     *
     * @param  User $user - User to authenticate
     * @param  Project $project - Project to check against
     * @return bool - Is project admin
     */
    public function isProjectAdmin(User $user, Project $project) {
        if ($user->admin)
            return true;

        $adminGroup = $project->adminGroup()->first();
        if($adminGroup->hasUser($user))
            return true;
        else
            return false;
    }

    /**
     * Gets back a project using its ID or slug.
     *
     * @param  int $id - Project ID
     * @return Project - Project model matching ID/slug
     */
    public static function getProject($id) {
        $project = Project::where('pid','=',$id)->first();
        if(is_null($project))
            $project = Project::where('slug','=',$id)->first();

        return $project;
    }

    /**
     * Determines if project exists.
     *
     * @param  int $id - Project ID
     * @return bool - Is a project
     */
    public static function validProj($id) {
        return !is_null(self::getProject($id));
    }

    /**
     * Gets the view for importing a k3Proj file.
     *
     * @return View
     */
    public function importProjectView() {
        return view('projects.import');
    }

    /**
     * Get a list of project admins for a project.
     *
     * @param  Project $project - Project to retrieve from
     * @return Collection - List of users
     */
    private function getProjectAdminNames($project) {
        $group = $project->adminGroup()->first();
        $users = $group->users()->get();

        return $users;
    }
}
