<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Record extends Model {

    protected $fillable = [
        'pid',
        'fid',
        'owner',
        'kid'
    ];

    protected $primaryKey = "rid";

    public function form(){
        return $this->belongsTo('App\Form');
    }

    public function textfields(){
        return $this->hasMany('App\TextField', 'rid');
    }

}