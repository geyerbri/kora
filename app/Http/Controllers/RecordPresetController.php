<?php namespace App\Http\Controllers;

use App\Record;
use App\RecordPreset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RecursiveIteratorIterator;
use Symfony\Component\Finder\Iterator\RecursiveDirectoryIterator;

class RecordPresetController extends Controller {

    /*
    |--------------------------------------------------------------------------
    | Record Preset Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles creation and management of record presets
    |
    */

    /**
     * Constructs controller and makes sure user is authenticated.
     */
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('active');
    }

    /**
     * Gets the view for managing existing presets.
     *
     * @param  int $pid - Project ID
     * @param  int $fid - Form ID
     * @return View
     */
    public function index($pid, $fid) {
        if(!FormController::validProjForm($pid, $fid))
            return redirect('projects/'.$pid)->with('k3_global_error', 'form_invalid');

        $form = FormController::getForm($fid);

        if(!(\Auth::user()->isFormAdmin($form)))
            return redirect('projects/'.$pid)->with('k3_global_error', 'not_form_admin');

        $presets = RecordPreset::where('form_id', '=', $fid)->get();

        return view('recordPresets/index', compact('form', 'presets'));
    }

    /**
     * Copies a record and saves it as a record preset template.
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function presetRecord(Request $request) {
        $name = $request->name;
        $kid = $request->kid;

        if(!is_null(RecordPreset::where('record_kid', '=', $kid)->first())) {
            return response()->json(["status"=>false,"message"=>"record_already_preset"],500);
        } else {
            $record = RecordController::getRecord($kid);
            $preset = new RecordPreset();
            $preset->form_id = $record->form_id;
            $preset->record_kid = $record->kid;

            $preset->preset = $this->getRecordArray($record, $name);
            $preset->save();

            return response()->json(["status"=>true,"message"=>"record_preset_saved"],200);
        }
    }

    /**
     * Takes a record and turns it into an array that is saved in the record preset.
     *
     * @param  Record $record - Record model
     * @param  string $name - Name of preset
     * @return array - The data array
     */
    public function getRecordArray($record, $name) {
        $form = FormController::getForm($record->form_id);

        $field_collect = $form->layout["fields"];
        $field_array = array();

        $fileFields = false; // Does the record have any file fields? //TODO::CASTLE

        foreach($field_collect as $flid => $field) {
            //We hit a file type field //TODO::CASTLE
//            if($typedField instanceof FileTypeField)
//                $fileFields = true;

            $field_array[$flid] = $record->{$flid};
        }

        // A file field was in use, so we need to move the record files to a preset directory. //TODO::CASTLE
//        if($fileFields and !is_null($preID))
//            $this->moveFilesToPreset($record->rid, $preID);

        $response['data'] = $field_array;
        $response['name'] = $name;

        return $response;
    }

    /**
     * Updates a record's preset if one was made.
     *
     * @param  int $rid - Record ID
     */
    public static function updateIfExists($rid) { //TODO::CASTLE
        $pre = RecordPreset::where("rid", '=', $rid)->first();

        if(!is_null($pre)) {
            $rpc = new self();
            $pre->preset = $rpc->getRecordArray($rid, $pre->id);
        }
    }

    /**
     * Changes the saved name of the preset.
     *
     * @param  Request $request
     */
    public function changePresetName(Request $request) { //TODO::CASTLE
        $name = $request->name;
        $id = $request->id;

        $preset = RecordPreset::where('id', '=', $id)->first();

        $preset->name = $name;
        $preset->save();
    }

    /**
     * Deletes a record preset.
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function deletePreset(Request $request) { //TODO::CASTLE
        $id = $request->id;
        $preset = RecordPreset::where('id', '=', $id)->first();
        $preset->delete();

        //
        // Delete the preset's file directory.
        //
        $path = storage_path('app/presetFiles/preset'. $id);

        if(is_dir($path)) {
            $it = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
            $files = new RecursiveIteratorIterator($it,
                RecursiveIteratorIterator::CHILD_FIRST);
            foreach($files as $file) {
                if ($file->isDir())
                    rmdir($file->getRealPath());
                else
                    unlink($file->getRealPath());
            }
            rmdir($path);
        }

        return response()->json(["status"=>true,"message"=>"record_preset_deleted"],200);
    }

    /**
     * Moves a records files into the folder for the preset.
     *
     * @param  int $rid - Record ID
     * @param  int $preID - Preset ID
     */
    public function moveFilesToPreset($rid, $preID) { //TODO::CASTLE
        $presets_path = storage_path('app/presetFiles');

        //
        // Create the presets file path if it does not exist.
        //
        if(!is_dir($presets_path))
            mkdir($presets_path, 0775, true);

        $path = $presets_path . '/preset' . $preID; // Path for the new preset's directory.

        if(!is_dir($path))
            mkdir($path, 0775, true);

        // Build the record's directory.
        $record = RecordController::getRecord($rid);

        $record_path = storage_path('app/files/p' . $record->pid . '/f' . $record->fid . '/r' . $record->rid);

        //
        // Recursively copy the record's file directory.
        //
        self::recurse_copy($record_path, $path);
    }

    /**
     * Moves file to tmp directory
     *
     * @param  Request $request
     */
    public function moveFilesToTemp(Request $request) { //TODO::CASTLE
        $presetID = $request->presetID;
        $flid = $request->flid;
        $userID = $request->userID;

        $presetPath = storage_path('app/presetFiles/preset' . $presetID . '/fl' . $flid);
        $tempPath = storage_path('app/tmpFiles/f'. $flid . 'u' . $userID);

        //
        // If the temp directory exists for the user, clear out the existing files.
        // Else create the directory.
        //
        if(is_dir($tempPath)) {
            $it = new RecursiveDirectoryIterator($tempPath, RecursiveDirectoryIterator::SKIP_DOTS);
            $files = new RecursiveIteratorIterator($it,
                RecursiveIteratorIterator::CHILD_FIRST);
            foreach($files as $file) {
                if ($file->isDir())
                    rmdir($file->getRealPath());
                else
                    unlink($file->getRealPath());
            }
        } else {
            mkdir($tempPath, 0775, true);
        }

        //
        // Copy the preset directory to the temporary directory.
        //
        self::recurse_copy($presetPath, $tempPath);
    }

    /**
     * Recursively copies a directory and its files to directory.
     *
     * @param  string $src - Directory to copy
     * @param  string $dst - Directory to copy to
     */
    public static function recurse_copy($src, $dst) { //TODO::CASTLE
        if(file_exists($src)) {
            $dir = opendir($src);

            if(!is_dir($dst) && !is_file($dst))
                mkdir($dst, 0775, true);

            while(false !== ($file = readdir($dir))) {
                if(($file != '.') && ($file != '..')) {
                    if(is_dir($src . '/' . $file))
                        self::recurse_copy($src . '/' . $file, $dst . '/' . $file);
                    else
                        copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
            closedir($dir);
        }
    }
}
