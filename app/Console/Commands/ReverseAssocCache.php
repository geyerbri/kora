<?php namespace App\Console\Commands;

use App\Form;
use App\KoraFields\AssociatorField;
use App\Record;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReverseAssocCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kora:assoc-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Builds out the cache for records to reference any records that point at them';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ini_set('memory_limit','2G'); //We might be pulling a lot of rows so this is a safety precaution
        $this->info('Generating reverse association cache...');

        $forms = Form::all();

        $this->info('Clearing old cache...');
        $tableManager = new \CreateAssociationsTable();
        $tableManager->buildTempCacheTable();

        $this->info('Rebuilding cache...');
        $inserts = [];
        foreach($forms as $form) {
            $this->info('Processing Form '.$form->internal_name.'...');

            $fields = $form->layout['fields'];
            if(is_null($fields))
                continue;
            $recModel = new Record(array(),$form->id);

            foreach($fields as $flid => $field) {
                if($field['type'] == Form::_ASSOCIATOR) {
                    $assocData = $recModel->newQuery()->select('kid',$flid)->get();
                    foreach($assocData as $row) {
                        $values = json_decode($row->{$flid},true);
                        if(is_null($values))
                            continue;

                        foreach($values as $val) {
                            if(!Record::isKIDPattern($val))
                                continue;

                            $inserts[] = [
                                'associated_kid' => $val,
                                'associated_form_id' => explode('-',$val)[1],
                                'source_kid' => $row->kid,
                                'source_flid' => $field['name'],
                                'source_form_id' => $form->id
                            ];
                        }
                    }
                } else if($field['type'] == Form::_COMBO_LIST && $field['one']['type'] == Form::_ASSOCIATOR) {
                    $subFieldName = $field['one']['flid'];
                    $assocData = $recModel->newQuery()->select('kid',$flid)->get();
                    foreach($assocData as $row) {
                        $values = json_decode($row->{$flid},true);
                        if(is_null($values))
                            continue;

                        //Need to pull values from combo table
                        $subvalues = DB::table($flid.$form->id)->whereIn('id',$values)->select($subFieldName)->get();

                        foreach($subvalues as $subval) {
                            $vals = json_decode($subval->{$subFieldName},true);

                            foreach($vals as $val) {
                                if(!Record::isKIDPattern($val))
                                    continue;

                                $inserts[] = [
                                    'associated_kid' => $val,
                                    'associated_form_id' => explode('-', $val)[1],
                                    'source_kid' => $row->kid,
                                    'source_flid' => $field['name'],
                                    'source_form_id' => $form->id
                                ];
                            }
                        }
                    }
                } else if($field['type'] == Form::_COMBO_LIST && $field['two']['type'] == Form::_ASSOCIATOR) {
                    $subFieldName = $field['two']['flid'];
                    $assocData = $recModel->newQuery()->select('kid',$flid)->get();
                    foreach($assocData as $row) {
                        $values = json_decode($row->{$flid},true);
                        if(is_null($values))
                            continue;

                        //Need to pull values from combo table
                        $subvalues = DB::table($flid.$form->id)->whereIn('id',$values)->select($subFieldName)->get();

                        foreach($subvalues as $subval) {
                            $vals = json_decode($subval->{$subFieldName},true);

                            foreach($vals as $val) {
                                if(!Record::isKIDPattern($val))
                                    continue;

                                $inserts[] = [
                                    'associated_kid' => $val,
                                    'associated_form_id' => explode('-', $val)[1],
                                    'source_kid' => $row->kid,
                                    'source_flid' => $field['name'],
                                    'source_form_id' => $form->id
                                ];
                            }
                        }
                    }
                }
            }
        }

        if(!empty($inserts)) {
            $this->info('Storing values...');
            $chunks = array_chunk($inserts, 1000);
            foreach($chunks as $chunk) {
                //Break up the inserts into chuncks
                DB::table(AssociatorField::Reverse_Temp_Table)->insert($chunk);
            }
        }

        $tableManager->swapTempCacheTable();

        updateGlobalTimer("reverse_assoc_cache_build");

        $this->info('Reverse association cache generated!');
    }
}
