<?php

Route::group(['middleware' => 'web'], function () {
    Route::get('/', 'WelcomeController@index');
    Route::get('/home', 'WelcomeController@index');
    Route::post('/language', 'WelcomeController@setTemporaryLanguage');

//dashboard routes
    Route::get('/dashboard', 'DashboardController@dashboard');
    Route::post('/dashboard/addBlock', 'DashboardController@addBlock');
    Route::post('/dashboard/addBlock/validate', 'DashboardController@validateBlockFields');
    Route::post('/dashboard/addSection/{sectionTitle}','DashboardController@addSection');
    Route::patch('/dashboard/editBlock', 'DashboardController@editBlock');
    Route::patch('/dashboard/editNoteBlock', 'DashboardController@editNoteBlock');
    Route::patch('/dashboard/editSection', 'DashboardController@editSection');
    Route::patch('/dashboard/editBlockOrder', 'DashboardController@editBlockOrder');
    Route::patch('/dashboard/editBlockQuickActions', 'DashboardController@editBlockQuickActions');
    Route::delete('/dashboard/deleteBlock/{blkID}/{secID}', 'DashboardController@deleteBlock');
    Route::delete('/dashboard/deleteSection/{sectionID}', 'DashboardController@deleteSection');

//project routes
    Route::get('/projects/import', 'ProjectController@importProjectView');
    Route::post('/projects/getProjectPermissionsModal', 'ProjectController@getProjectPermissionsModal');
    Route::post('/projects/import', 'ImportController@importProject');
    Route::resource('projects', 'ProjectController');
    Route::post('projects/request', 'ProjectController@request');
    Route::post('projects/{pid}/archive', 'ProjectController@setArchiveProject');
    Route::get('/projects/{pid}/importMF', 'ImportMultiFormController@index');
    Route::post('/projects/{pid}/importMF', 'ImportMultiFormController@beginImport');
    Route::post('/projects/{pid}/importMFRecord', 'ImportMultiFormController@importRecord');
    Route::post('/projects/{pid}/connectRecords', 'ImportMultiFormController@connectRecords');
    Route::post('/saveTmpFileMF', 'ImportMultiFormController@saveTmpFile');
    Route::patch('/saveTmpFileMF', 'ImportMultiFormController@saveTmpFile');
    Route::delete('/deleteTmpFileMF/{filename}', 'ImportMultiFormController@delTmpFile');
    Route::post('/projects/{pid}/importRecordFailed', 'ImportMultiFormController@downloadFailedRecords');
    Route::post('/projects/{pid}/importReasonsFailed', 'ImportMultiFormController@downloadFailedReasons');
    Route::post('/projects/{pid}/importConnectionsFailed', 'ImportMultiFormController@downloadFailedConnections');
    Route::post('projects/validate', 'ProjectController@validateProjectFields');
    Route::patch('projects/validate/{projects}', 'ProjectController@validateProjectFields');

//project group routes
    Route::get('/projects/{pid}/manage/projectgroups/', 'ProjectGroupController@index');
    Route::post('/projects/{pid}/manage/projectgroups/create', 'ProjectGroupController@create');
    Route::patch('projects/{pid}/manage/projectgroups/removeUser', 'ProjectGroupController@removeUser');
    Route::patch('projects/{pid}/manage/projectgroups/addUsers', 'ProjectGroupController@addUsers');
    Route::patch('projects/{pid}/manage/projectgroups/updatePermissions', 'ProjectGroupController@updatePermissions');
    Route::patch('projects/{pid}/manage/projectgroups/updateName', 'ProjectGroupController@updateName');
    Route::delete('projects/{pid}/manage/projectgroups/deleteProjectGroup', 'ProjectGroupController@deleteProjectGroup');

//form group routes
    Route::get('/projects/{pid}/forms/{fid}/manage/formgroups', 'FormGroupController@index');
    Route::post('/projects/{pid}/forms/{fid}/manage/formgroups/create', 'FormGroupController@create');
    Route::patch('projects/{pid}/forms/{fid}/manage/formgroups/removeUser', 'FormGroupController@removeUser');
    Route::patch('projects/{pid}/forms/{fid}/manage/formgroups/addUser', 'FormGroupController@addUser');
    Route::patch('projects/{pid}/forms/{fid}/manage/formgroups/updatePermissions', 'FormGroupController@updatePermissions');
    Route::patch('projects/{pid}/forms/{fid}/manage/formgroups/updateName', 'FormGroupController@updateName');
    Route::delete('projects/{pid}/forms/{fid}/manage/formgroups/deleteFormGroup', 'FormGroupController@deleteFormGroup');

//admin routes
    Route::get('/admin/users', 'AdminController@users');
    Route::get('/admin/users/{id}/edit', 'AdminController@editUser');
    Route::post('/admin/users/validateEmails', 'AdminController@validateEmails');
    Route::post('admin/reverseCache', 'AdminController@buildReverseCache');
    Route::patch('/admin/update/{id}', 'AdminController@update');
    Route::patch('/admin/updateActivation/{id}', 'AdminController@updateActivation');
    Route::patch('/admin/updateStatus/{id}', 'AdminController@updateStatus');
    Route::patch('/admin/batch', 'AdminController@batch');
    Route::delete('admin/deleteUser/{id}', 'AdminController@deleteUser');

//token routes
    Route::get('/tokens', 'TokenController@index');
    Route::post('/tokens/create', 'TokenController@create');
    Route::post('/tokens/store', 'TokenController@edit');
    Route::post('/tokens/unassigned', 'TokenController@getUnassignedProjects');
    Route::patch('/tokens/deleteProject', 'TokenController@deleteProject');
    Route::patch('/tokens/addProject', 'TokenController@addProject');
    Route::delete('/tokens/deleteToken', 'TokenController@deleteToken');

//association routes
    Route::get('/projects/{pid}/forms/{fid}/assoc', 'AssociationController@index');
    Route::post('/projects/{pid}/forms/{fid}/assoc', 'AssociationController@create');
    Route::post('/projects/{pid}/forms/{fid}/assoc/request', 'AssociationController@requestAccess');
    Route::delete('/projects/{pid}/forms/{fid}/assoc', 'AssociationController@destroy');
    Route::delete('/projects/{pid}/forms/{fid}/assocReverse', 'AssociationController@destroyReverse');

//form routes
    Route::get('/projects/{pid}/forms', 'ProjectController@show'); //alias for project/{id}
    Route::post('projects/{pid}/forms/validate', 'FormController@validateFormFields');
    Route::patch('projects/{pid}/forms/validate/{fid}', 'FormController@validateFormFields');
    Route::patch('/projects/{pid}/forms/{fid}', 'FormController@update');
    Route::get('/projects/{pid}/forms/create', 'FormController@create');
    Route::get('/projects/{pid}/forms/import', 'FormController@importFormView');
    Route::post('/projects/{pid}/forms/import', 'ImportController@importForm');
    Route::get('/projects/{pid}/forms/{fid}', 'FormController@show');
    Route::delete('/projects/{pid}/forms/{fid}', 'FormController@destroy');
    Route::get('/projects/{pid}/forms/{fid}/edit', 'FormController@edit');
    Route::post('/projects/{pid}/forms/{fid}/preset', 'FormController@preset');
    Route::post('/projects/{pid}', 'FormController@store');
    Route::post('/projects/{pid}/forms/{fid}/pages/modify', 'PageController@modifyFormPage');
    Route::post('/projects/{pid}/forms/{fid}/pages/layout', 'PageController@saveFullFormLayout');

//export routes
    Route::get('/projects/{pid}/forms/{fid}/exportRecords/{type}', 'ExportController@exportRecords');
    Route::post('/projects/{pid}/forms/{fid}/exportSelectedRecords/{type}', 'ExportController@exportSelectedRecords');
    Route::post('/projects/{pid}/forms/{fid}/prepFiles', 'ExportController@prepRecordFiles');
    Route::post('/projects/{pid}/forms/{fid}/checkFiles', 'ExportController@checkRecordFiles');
    Route::get('/projects/{pid}/forms/{fid}/exportFiles/{name}', 'ExportController@exportRecordFiles');
    Route::get('/projects/{pid}/forms/{fid}/exportForm', 'ExportController@exportForm');
    Route::get('/projects/{pid}/exportProj', 'ExportController@exportProject');

//field routes
    Route::get('/projects/{pid}/forms/{fid}/fields', 'FormController@show'); //alias for form/{id}
    Route::post('projects/{pid}/forms/{fid}/fields/validate', 'FieldController@validateFieldFields');
    Route::patch('projects/{pid}/forms/{fid}/fields/validate/{flid}', 'FieldController@validateFieldFields');
    Route::patch('/projects/{pid}/forms/{fid}/fields/{flid}', 'FieldController@update');
    Route::get('/projects/{pid}/forms/{fid}/fields/create/{rootPage}', 'FieldController@create');
    Route::get('/projects/{pid}/forms/{fid}/fields/{flid}', 'FieldController@show');
    Route::delete('/projects/{pid}/forms/{fid}/fields/{flid}', 'FieldController@destroy');
    Route::get('/projects/{pid}/forms/{fid}/fields/{flid}/edit', 'FieldController@edit');
    Route::get('/projects/{pid}/forms/{fid}/fields/{flid}/options', 'FieldController@show'); //alias for fields/{id}
    Route::post('/projects/{pid}/forms/{fid}/advOpt', 'FieldAjaxController@getAdvancedOptionsPage');
    Route::patch('/projects/{pid}/forms/{fid}/fields/{flid}/flag', 'FieldController@updateFlag');
    Route::post('/projects/{pid}/forms/{fid}/fields/{flid}/options/geoConvert', 'FieldAjaxController@geoConvert');
    Route::post('/projects/{pid}/forms/{fid}/fields/{flid}/options/assoc', 'AssociatorSearchController@assocSearch');
    Route::post('/projects/{pid}/forms/{fid}', 'FieldController@store');
    Route::post('/saveTmpFile/{fid}/{flid}', 'FieldAjaxController@saveTmpFile');
    Route::patch('/saveTmpFile/{fid}/{flid}', 'FieldAjaxController@saveTmpFile');
    Route::delete('/deleteTmpFile/{fid}/{flid}/{filename}', 'FieldAjaxController@delTmpFile');
    Route::get('/download/{kid}/zip', 'FieldAjaxController@getZipDownload');
    Route::get('/download/{kid}/{filename}', 'FieldAjaxController@getFileDownload');
    Route::get('/files/{kid}/{filename}', 'FieldAjaxController@publicRecordFile');
    Route::get("/validateAddress", "FieldAjaxController@validateAddress");

//record preset routes
    Route::get('/projects/{pid}/forms/{fid}/records/presets', 'RecordPresetController@index');
    Route::patch('/changePresetName', 'RecordPresetController@changePresetName');
    Route::delete('/deletePreset', 'RecordPresetController@deletePreset');
    Route::post('/getRecordArray', 'RecordPresetController@getRecordArray');
    Route::post('/presetRecord', 'RecordPresetController@presetRecord');
    Route::post('/getData', 'RecordPresetController@getData');
    Route::post('/moveFilesToTemp', 'RecordPresetController@moveFilesToTemp');

//option preset routes
    Route::get('/projects/{pid}/presets', 'FieldValuePresetController@index');
    Route::get('/projects/{pid}/presets/create', 'FieldValuePresetController@newPreset');
    Route::post('/projects/{pid}/presets/create', 'FieldValuePresetController@create');
    Route::post('/projects/{pid}/presets/createApi', 'FieldValuePresetController@createApi');
    Route::post('projects/{pid}/presets/validate', 'FieldValuePresetController@validatePresetFormFields');
    Route::delete('/projects/{pid}/presets/delete', 'FieldValuePresetController@delete');
    Route::get('/projects/{pid}/presets/{id}/edit', 'FieldValuePresetController@edit');
    Route::post('/projects/{pid}/presets/{id}/edit', 'FieldValuePresetController@update');

//record routes
    Route::get('/projects/{pid}/forms/{fid}/records', 'RecordController@index');
    Route::get('projects/{pid}/forms/{fid}/records/massAssignRecords', 'RecordController@showMassAssignmentView');
    Route::get('projects/{pid}/forms/{fid}/records/showSelectedAssignmentView', 'RecordController@showSelectedAssignmentView');//this
    Route::post('projects/{pid}/forms/{fid}/records/massAssignRecords', 'RecordController@massAssignRecords');
    Route::post('projects/{pid}/forms/{fid}/records/massAssignRecordSet', 'RecordController@massAssignRecordSet');
    Route::patch('/projects/{pid}/forms/{fid}/records/{rid}', 'RecordController@update');
    Route::get('/projects/{pid}/forms/{fid}/records/create', 'RecordController@create');
    Route::get('/projects/{pid}/forms/{fid}/records/import', 'RecordController@importRecordsView');
    Route::post('/projects/{pid}/forms/{fid}/records/matchup', 'ImportController@matchupFields');
    Route::post('/projects/{pid}/forms/{fid}/records/validate', 'RecordController@validateRecord');
    Route::post('/projects/{pid}/forms/{fid}/records/validateMass', 'RecordController@validateMassRecord');
    Route::post('/projects/{pid}/forms/{fid}/records/importRecord', 'ImportController@importRecord');
    Route::post('/projects/{pid}/forms/{fid}/records/connectRecords', 'ImportController@connectRecords');
    Route::post('/projects/{pid}/forms/{fid}/records/importFailureSave', 'ImportController@saveImportFailure');
    Route::post('/projects/{pid}/forms/{fid}/records/importRecordFailed', 'ImportController@downloadFailedRecords');
    Route::post('/projects/{pid}/forms/{fid}/records/importReasonsFailed', 'ImportController@downloadFailedReasons');
    Route::post('/projects/{pid}/forms/{fid}/records/importConnectionsFailed', 'ImportController@downloadFailedConnections');
    Route::get('/projects/{pid}/forms/{fid}/records/{rid}', 'RecordController@show');
    Route::post('/projects/{pid}/forms/{fid}/records/{rid}/revData', 'RecordController@getAssociatedRecordData');
    Route::get('/projects/{pid}/forms/{fid}/records/{rid}/edit', 'RecordController@edit');
    Route::post('/projects/{pid}/forms/{fid}/records', 'RecordController@store');
    Route::delete('projects/{pid}/forms/{fid}/records/deleteMultipleRecords', 'RecordController@deleteMultipleRecords');
    Route::delete('projects/{pid}/forms/{fid}/records/{rid}', 'RecordController@destroy');
    Route::delete('projects/{pid}/forms/{fid}/deleteAllRecords', 'RecordController@deleteAllRecords');
    Route::post('/projects/{pid}/forms/{fid}/cleanUp', 'RecordController@cleanUp');
    Route::get('/projects/{pid}/forms/{fid}/clone/{rid}', 'RecordController@cloneRecord');
    Route::get('/projects/{pid}/forms/{fid}/records/{rid}/geolocator/{flid}', 'FieldAjaxController@singleGeolocator');
    Route::get('/projects/{pid}/forms/{fid}/records/{rid}/fields/{flid}/model', 'FieldController@singleModel');

//revision routes
    Route::get('/projects/{pid}/forms/{fid}/records/revisions/recent', 'RevisionController@index');
    Route::get('/projects/{pid}/forms/{fid}/records/revisions/{rid}', 'RevisionController@show');
    Route::get('/rollback', 'RevisionController@rollback');

//user routes
    Route::get('/user', 'Auth\UserController@redirect');
    Route::get('/auth/activate', 'Auth\UserController@activateshow');
    Route::get('/user/activate/{token}', 'Auth\UserController@activate');
    Route::get('/user/invitedactivate/{token}', 'Auth\UserController@activateFromInvite');
    Route::get('/user/{uid}/edit', 'Auth\UserController@editProfile');
    Route::get('/user/{uid}/preferences', 'Auth\UserController@preferences'); // get all user prefs
    Route::get('user/{uid}/removeGitlab', 'Auth\UserController@removeGitlab');
    Route::get('/user/{uid}/{section?}', 'Auth\UserController@index');
    Route::get('/returnUserPrefs/{pref}', 'Auth\UserController@returnUserPrefs'); // get individual user pref
	Route::get('/getOnboardingProjects/{user}', 'Auth\UserController@getOnboardingProjects');
    Route::delete('/user/{uid}/delete', 'Auth\UserController@delete');
    Route::patch('/user/validate/{uid}', 'Auth\UserController@validateUserFields');
    Route::patch('/user/{uid}/update', 'Auth\UserController@update');
    Route::patch('/user/{uid}/updateFromEmail', 'Auth\UserController@updateFromEmail');
    Route::patch('/user/{uid}/preferences', 'Auth\UserController@updatePreferences'); // edit user prefs from user prefs page
    Route::post('/auth/resendActivate', 'Auth\UserController@resendActivation');
    Route::post('/auth/activator', 'Auth\UserController@activator');
    Route::post('/user/picture', 'Auth\UserController@changepicture');
    Route::post('/user/validate', 'Auth\RegisterController@validateUserFields');
	Route::patch('/toggleOnboarding', 'Auth\UserController@toggleOnboarding');
	Route::patch('/user/validateEditProfile', 'Auth\UserController@validateEditProfile');

//install routes
    Route::get('/helloworld', 'InstallController@helloworld');
    Route::get('/install', 'InstallController@index');
    Route::post('/install', 'InstallController@installFromWeb');
    Route::get('/readyplayerone', "WelcomeController@installSuccess");
    Route::get('/install/config', "InstallController@editEnvConfigs");
    Route::post('/install/config', "InstallController@updateEnvConfigs");

//update routes
    Route::get('/update', 'UpdateController@index');
    Route::get('/update/runScripts', 'UpdateController@runScripts');

//form search routes
    Route::get('/keywordSearch/project/{pid}/forms/{fid}', 'FormSearchController@keywordSearch');
    Route::get('/keywordSearch/project/{pid}/forms/{fid}/delete', 'FormSearchController@deleteSubset');

//project search routes
    Route::get("keywordSearch/project/{pid}", "ProjectSearchController@keywordSearch");

//global search routes
    Route::get("globalSearch", "ProjectSearchController@globalSearch");
    Route::post("globalQuickSearch", "ProjectSearchController@globalQuickSearch");
    Route::post("cacheGlobalSearch", "ProjectSearchController@cacheGlobalSearch");
    Route::delete("clearGlobalCache", "ProjectSearchController@clearGlobalCache");

//advanced search routes
    Route::get("/projects/{pid}/forms/{fid}/advancedSearch", "AdvancedSearchController@index");
    Route::get("/projects/{pid}/forms/{fid}/advancedSearch/results", "AdvancedSearchController@recent");
    Route::post("/projects/{pid}/forms/{fid}/advancedSearch/results", "AdvancedSearchController@search");

//reset password routes
	Route::post("/reset/email/validate", "Auth\ResetPasswordController@preValidateEmail");

//user auth
    Auth::routes(); // generates user authentication routes
    Route::get('login/gitlab', 'Auth\LoginController@redirectToGitlab');
    Route::get('login/gitlab/callback', 'Auth\LoginController@handleGitlabCallback');

    Route::post("/user/projectCustom", "Auth\UserController@saveProjectCustomOrder");
    Route::post("/user/formCustom/{pid}", "Auth\UserController@saveFormCustomOrder");

	// fallback route (allows for serving 404 with sessions, auth, cookies)
	Route::fallback('FallbackController@routeNotFound');
});

Route::group(['middleware' => 'api'], function () {
//api routes
    Route::get('/api/version', 'RestfulController@getKoraVersion');
    Route::get('/api/projects/{pid}/forms', 'RestfulController@getProjectForms');
    Route::post('/api/projects/{pid}/forms/create', 'RestfulController@createForm');
    Route::get('/api/projects/{pid}/forms/{fid}/fields', 'RestfulController@getFormFields');
    Route::put('/api/projects/{pid}/forms/{fid}/fields', 'RestfulController@modifyFormFields');
    Route::get('/api/projects/{pid}/forms/{fid}/recordCount', 'RestfulController@getFormRecordCount');
    Route::post('/api/search', 'RestfulController@search');
    Route::delete('/api/delete', 'RestfulController@delete');
    Route::post('/api/create', 'RestfulController@create');
    Route::put('/api/edit', 'RestfulController@edit');

//beta api routes
    Route::get('/api/beta/version', 'RestfulBetaController@getKoraVersion');
    Route::get('/api/beta/projects/{pid}/forms', 'RestfulBetaController@getProjectForms');
    Route::post('/api/beta/projects/{pid}/forms/create', 'RestfulBetaController@createForm');
    Route::get('/api/beta/projects/{pid}/forms/{fid}/fields', 'RestfulBetaController@getFormFields');
    Route::put('/api/beta/projects/{pid}/forms/{fid}/fields', 'RestfulBetaController@modifyFormFields');
    Route::get('/api/beta/projects/{pid}/forms/{fid}/recordCount', 'RestfulBetaController@getFormRecordCount');
    Route::post('/api/beta/search', 'RestfulBetaController@search');
    Route::delete('/api/beta/delete', 'RestfulBetaController@delete');
    Route::post('/api/beta/create', 'RestfulBetaController@create');
    Route::put('/api/beta/edit', 'RestfulBetaController@edit');
});
