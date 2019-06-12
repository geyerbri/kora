<?php namespace App\KoraFields;

use App\Form;
use App\Record;
use Illuminate\Http\Request;

class DateField extends BaseField {

    /*
    |--------------------------------------------------------------------------
    | Date Field
    |--------------------------------------------------------------------------
    |
    | This model represents the date field in Kora3
    |
    */

    /**
     * @var string - Views for the typed field options
     */
    const FIELD_OPTIONS_VIEW = "partials.fields.options.date";
    const FIELD_ADV_OPTIONS_VIEW = "partials.fields.advanced.date";
    const FIELD_ADV_INPUT_VIEW = "partials.records.advanced.date";
    const FIELD_INPUT_VIEW = "partials.records.input.date";
    const FIELD_DISPLAY_VIEW = "partials.records.display.date";

    /**
     * Get the field options view.
     *
     * @return string - The view
     */
    public function getFieldOptionsView() {
        return self::FIELD_OPTIONS_VIEW;
    }

    /**
     * Get the field options view for advanced field creation.
     *
     * @return string - The view
     */
    public function getAdvancedFieldOptionsView() {
        return self::FIELD_ADV_OPTIONS_VIEW;
    }

    /**
     * Get the field input view for advanced field search.
     *
     * @return string - The view
     */
    public function getAdvancedSearchInputView() {
        return self::FIELD_ADV_INPUT_VIEW;
    }

    /**
     * Get the field input view for record creation.
     *
     * @return string - The view
     */
    public function getFieldInputView() {
        return self::FIELD_INPUT_VIEW;
    }

    /**
     * Get the field input view for record creation.
     *
     * @return string - The view
     */
    public function getFieldDisplayView() {
        return self::FIELD_DISPLAY_VIEW;
    }

    /**
     * Gets the default options string for a new field.
     *
     * @param  int $fid - Form ID
     * @param  string $slug - Name of database column based on field internal name
     * @param  array $options - Extra information we may need to set up about the field
     * @return array - The default options
     */
    public function addDatabaseColumn($fid, $slug, $options = null) {
        $table = new \CreateRecordsTable();
        $table->addDateColumn($fid, $slug);
    }

    /**
     * Gets the default options string for a new field.
     *
     * @return array - The default options
     */
    public function getDefaultOptions($type = null) {
        return [
            'Start' => 1900,
            'End' => 2030,
            'Format' => 'MMDDYYYY'
        ];
    }

    /**
     * Update the options for a field
     *
     * @param  array $field - Field to update options
     * @param  Request $request
     * @param  int $flid - The field internal name
     * @return array - The updated field array
     */
    public function updateOptions($field, Request $request, $flid = null, $prefix = 'records_') {
        if(self::validateDate($request->default_month,$request->default_day,$request->default_year)) {
            $default = [
                'month' => $request->default_month,
                'day' => $request->default_day,
                'year' => $request->default_year
            ];
        } else {
            $default = null;
        }

        if($request->start=='')
            $request->start = 1;

        if($request->end=='')
            $request->end = 9999;

        // If the years don't make sense, flip em
        // Use temp start & end vars to keep 0 (current year) value
        $start = $request->start;
        if ($start == 0)
            $start = date("Y");

        $end = $request->end;
        if ($end == 0)
            $end = date("Y");

        if ($start > $end) {
            $pivot = $request->start;
            $request->start = $request->end;
            $request->end = $pivot;
        }

        $field['default'] = $default;
        $field['options']['Start'] = $request->start;
        $field['options']['End'] = $request->end;
        $field['options']['Format'] = $request->format;

        return $field;
    }

    /**
     * Validates the record data for a field against the field's options.
     *
     * @param  int $flid - The field internal name
     * @param  array $field - The field data array to validate
     * @param  Request $request
     * @param  bool $forceReq - Do we want to force a required value even if the field itself is not required?
     * @return array - Array of errors
     */
    public function validateField($flid, $field, $request, $forceReq = false) {
        $req = $field['required'];
        $start = $field['options']['Start'];
        $end = $field['options']['End'];
        $month = $request->input('month_'.$flid,'');
        $day = $request->input('day_'.$flid,'');
        $year = $request->input('year_'.$flid,'');

        $dateNotProvided = ($month=='' && $day=='' && $year=='');

        // A year set to 0 is actually 'Current Year'
        if ($year == 0)
            $year = date("Y");

        if ($start == 0)
            $start = date("Y");

        if ($end == 0)
            $end = date("Y");

        if(($req==1 | $forceReq) && $dateNotProvided) {
            return [
                'month_' . $flid . '_chosen' => $field['name'] . ' is required',
                'day_' . $flid . '_chosen' => ' ',
                'year_' . $flid . '_chosen' => ' '
            ];
        } else if($dateNotProvided) {
            return array();
        }

        if(($year<$start | $year>$end) && $year!='')
            return [
                'year_'.$flid.'_chosen' => $field['name'].'\'s year is outside of the expected range'
            ];

        if(!self::validateDate($month,$day,$year))
            return [
                'month_'.$flid.'_chosen' => $field['name'].' is an invalid date or is missing pieces',
                'day_'.$flid.'_chosen' => ' ',
                'year_'.$flid.'_chosen' => ' '
            ];

        return array();
    }

    /**
     * Validates the month, day, year combinations so illegal dates can't happen.
     *
     * @param  int $m - Month
     * @param  int $d - Day
     * @param  int $y - Year
     * @return bool - Is valid
     */
    private static function validateDate($m,$d,$y) {
        //Date requires all parts
        if($m=='' | $d=='' | $y=='')
            return false;

        //Next we need to make sure the date provided is legal (i.e. no Feb 30th, etc)
        return checkdate($m, $d, $y);
    }

    /**
     * Formats data for record entry.
     *
     * @param  array $field - The field to represent record data
     * @param  string $value - Data to add
     * @param  Request $request
     *
     * @return mixed - Processed data
     */
    public function processRecordData($field, $value, $request) {
        $month = $request->input('month_'.$value,'');
        $day = $request->input('day_'.$value,'');
        $year = $request->input('year_'.$value,'');
        if(!self::validateDate($month,$day,$year))
            return null;
        else
            return "$year-$month-$day";
    }

    /**
     * Formats data for revision display.
     *
     * @param  mixed $data - The data to store
     * @param  Request $request
     *
     * @return mixed - Processed data
     */
    public function processRevisionData($data) {
        return $data;
    }

    /**
     * Formats data for record entry.
     *
     * @param  string $flid - Field ID
     * @param  array $field - The field to represent record data
     * @param  array $value - Data to add
     * @param  Request $request
     *
     * @return Request - Processed data
     */
    public function processImportData($flid, $field, $value, $request) {
        $request[$flid] = $flid;
        $parts = explode('-',$value);
        $request['month_'.$flid] = $parts[1];
        $request['day_'.$flid] = $parts[2];
        $request['year_'.$flid] = $parts[0];

        return $request;
    }

    /**
     * Formats data for record entry.
     *
     * @param  string $flid - Field ID
     * @param  array $field - The field to represent record data
     * @param  \SimpleXMLElement $value - Data to add
     * @param  Request $request
     *
     * @return Request - Processed data
     */
    public function processImportDataXML($flid, $field, $value, $request) {
        $request[$flid] = $flid;
        $parts = explode('-',(string)$value);
        $request['month_'.$flid] = $parts[1];
        $request['day_'.$flid] = $parts[2];
        $request['year_'.$flid] = $parts[0];

        return $request;
    }

    /**
     * Formats data for record display.
     *
     * @param  array $field - The field to represent record data
     * @param  string $value - Data to display
     *
     * @return mixed - Processed data
     */
    public function processDisplayData($field, $value) {
        return $this->displayDate($value, $field);
    }

    /**
     * Formats data for XML record display.
     *
     * @param  string $field - Field ID
     * @param  string $value - Data to format
     *
     * @return mixed - Processed data
     */
    public function processXMLData($field, $value) {
        $xml = "<$field>$value</$field>";

        return $xml;
    }

    /**
     * Formats data for XML record display.
     *
     * @param  string $value - Data to format
     *
     * @return mixed - Processed data
     */
    public function processLegacyData($value) {
        return null;
    }

    /**
     * Takes data from a mass assignment operation and applies it to an individual field.
     *
     * @param  Form $form - Form model
     * @param  string $flid - Field ID
     * @param  String $formFieldValue - The value to be assigned
     * @param  Request $request
     * @param  bool $overwrite - Overwrite if data exists
     */
    public function massAssignRecordField($form, $flid, $formFieldValue, $request, $overwrite=0) {
        $month = $request->input('month_'.$formFieldValue,'');
        $day = $request->input('day_'.$formFieldValue,'');
        $year = $request->input('year_'.$formFieldValue,'');

        if(!self::validateDate($month,$day,$year))
            $date = null;
        else
            $date = "$year-$month-$day";

        $recModel = new Record(array(),$form->id);
        if($overwrite)
            $recModel->newQuery()->update([$flid => $date]);
        else
            $recModel->newQuery()->whereNull($flid)->update([$flid => $date]);
    }

    /**
     * Takes data from a mass assignment operation and applies it to an individual field for a set of records.
     *
     * @param  Form $form - Form model
     * @param  string $flid - Field ID
     * @param  String $formFieldValue - The value to be assigned
     * @param  Request $request
     * @param  array $kids - The KIDs to update
     */
    public function massAssignSubsetRecordField($form, $flid, $formFieldValue, $request, $kids) {
        $month = $request->input('month_'.$formFieldValue,'');
        $day = $request->input('day_'.$formFieldValue,'');
        $year = $request->input('year_'.$formFieldValue,'');

        if(!self::validateDate($month,$day,$year))
            $date = null;
        else
            $date = "$year-$month-$day";

        $recModel = new Record(array(),$form->id);
        $recModel->newQuery()->whereIn('kid',$kids)->update([$flid => $date]);
    }

    /**
     * Provides an example of the field's structure in an export to help with importing records.
     *
     * @param  string $slug - Field nickname
     * @param  string $expType - Type of export
     * @return mixed - The example
     */
    public function getExportSample($slug,$type) {
        switch($type) {
            case "XML":
                $xml = '<' . $slug . '>';
                $xml .= 'YYYY-MM-DD';
                $xml .= '</' . $slug . '>';

                return $xml;
                break;
            case "JSON":
                $fieldArray[$slug] = 'YYYY-MM-DD';

                return $fieldArray;
                break;
        }
    }

    /**
     * Performs a keyword search on this field and returns any results.
     *
     * @param  string $flid - Field ID
     * @param  string $arg - The keywords
     * @param  Record $recordMod - Model to search through
     * @param  boolean $negative - Get opposite results of the search
     * @return array - The RIDs that match search
     */
    public function keywordSearchTyped($flid, $arg, $recordMod, $negative = false) {
        if($negative)
            $param = 'NOT LIKE';
        else
            $param = 'LIKE';

        return $recordMod->newQuery()
            ->select("id")
            ->where($flid, $param,"%$arg%")
            ->pluck('id')
            ->toArray();
    }

    /**
     * Updates the request for an API search to mimic the advanced search structure.
     *
     * @param  array $data - Data from the search
     * @return array - The update request
     */
    public function setRestfulAdvSearch($data) {
        $request = [];

        if(isset($data->begin_month) && is_int($data->begin_month))
            $request['begin_month'] = $data->begin_month;
        if(isset($data->begin_day) && is_int($data->begin_day))
            $request['begin_day'] = $data->begin_day;
        if(isset($data->begin_year) && is_int($data->begin_year))
            $request['begin_year'] = $data->begin_year;

        if(isset($data->end_month) && is_int($data->end_month))
            $request['end_month'] = $data->end_month;
        if(isset($data->end_day) && is_int($data->end_day))
            $request['end_day'] = $data->end_day;
        if(isset($data->end_year) && is_int($data->end_year))
            $request['end_year'] = $data->end_year;

        return $request;
    }

    /**
     * Build the advanced query for a text field.
     *
     * @param  $flid, field id
     * @param  $query, contents of query.
     * @param  Record $recordMod - Model to search through
     * @param  boolean $negative - Get opposite results of the search
     * @return array - The RIDs that match search
     */
    public function advancedSearchTyped($flid, $query, $recordMod, $negative = false) {
        $from = date($query['begin_year'].'-'.$query['begin_month'].'-'.$query['begin_day']);
        $to = date($query['end_year'].'-'.$query['end_month'].'-'.$query['end_day']);

        $return = $recordMod->newQuery()
            ->select("id");

        if($negative)
            $return->whereNotBetween($flid, [$from, $to]);
        else
            $return->whereBetween($flid, [$from, $to]);

        return $return->pluck('id')
            ->toArray();
    }

    ///////////////////////////////////////////////END ABSTRACT FUNCTIONS///////////////////////////////////////////////

    /**
     * Formatted display of a date field value.
     *
     * @param  string $date - Date string
     * @param  array $field - Field data
     * @return string - The formatted string
     */
    public function displayDate($date, $field) {
        $date = explode('-',$date);
        $dateString = '';

        if($field['options']['Format']=='MMDDYYYY')
            $dateString .= $date[1].'-'.$date[2].'-'.$date[0];
        else if($field['options']['Format']=='DDMMYYYY')
            $dateString .= $date[2].'-'.$date[1].'-'.$date[0];
        else if($field['options']['Format']=='YYYYMMDD')
            $dateString .= $date[0].'-'.$date[1].'-'.$date[2];

        return $dateString;
    }
}
