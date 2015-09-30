@if($field->type == 'Text')
    @include('records.fieldInputs.text-edit', ['text' => \App\TextField::where('rid', '=', $record->rid)->where('flid', '=', $field->flid)->first()])
@elseif($field->type == 'Rich Text')
    @include('records.fieldInputs.richtext-edit', ['richtext' => \App\RichTextField::where('rid', '=', $record->rid)->where('flid', '=', $field->flid)->first()])
@elseif($field->type == 'Number')
    @include('records.fieldInputs.number-edit', ['number' => \App\NumberField::where('rid', '=', $record->rid)->where('flid', '=', $field->flid)->first()])
@elseif($field->type == 'List')
    @include('records.fieldInputs.list-edit', ['list' => \App\ListField::where('rid', '=', $record->rid)->where('flid', '=', $field->flid)->first()])
@elseif($field->type == 'Multi-Select List')
    @include('records.fieldInputs.mslist-edit', ['mslist' => \App\MultiSelectListField::where('rid', '=', $record->rid)->where('flid', '=', $field->flid)->first()])
@elseif($field->type == 'Generated List')
    @include('records.fieldInputs.genlist-edit', ['genlist' => \App\GeneratedListField::where('rid', '=', $record->rid)->where('flid', '=', $field->flid)->first()])
@elseif($field->type == 'Date')
    @include('records.fieldInputs.date-edit', ['date' => \App\DateField::where('rid', '=', $record->rid)->where('flid', '=', $field->flid)->first()])
@elseif($field->type == 'Schedule')
    @include('records.fieldInputs.schedule-edit', ['schedule' => \App\ScheduleField::where('rid', '=', $record->rid)->where('flid', '=', $field->flid)->first()])
@elseif($field->type == 'Geolocator')
    @include('records.fieldInputs.geolocator-edit', ['geolocator' => \App\GeolocatorField::where('rid', '=', $record->rid)->where('flid', '=', $field->flid)->first()])
@elseif($field->type == 'Documents')
    @include('records.fieldInputs.documents-edit', ['documents' => \App\DocumentsField::where('rid', '=', $record->rid)->where('flid', '=', $field->flid)->first()])
@elseif($field->type == 'Gallery')
    @include('records.fieldInputs.gallery-edit', ['gallery' => \App\GalleryField::where('rid', '=', $record->rid)->where('flid', '=', $field->flid)->first()])
@elseif($field->type == 'Playlist')
    @include('records.fieldInputs.playlist-edit', ['playlist' => \App\PlaylistField::where('rid', '=', $record->rid)->where('flid', '=', $field->flid)->first()])
@elseif($field->type == 'Video')
    @include('records.fieldInputs.video-edit', ['video' => \App\VideoField::where('rid', '=', $record->rid)->where('flid', '=', $field->flid)->first()])
@elseif($field->type == '3D-Model')
    @include('records.fieldInputs.3dmodel-edit', ['model' => \App\ModelField::where('rid', '=', $record->rid)->where('flid', '=', $field->flid)->first()])
@endif