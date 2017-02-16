<?php
namespace App;
use Eloquent;

class SubTask extends Eloquent {

	protected $fillable = [
							'task_id',
							'title',
                            'description'
						];
	protected $primaryKey = 'id';
	protected $table = 'sub_tasks';

	protected function task()
    {
        return $this->belongsTo('App\Task');
    }

	protected function subTaskRating()
    {
        return $this->hasMany('App\SubTaskRating');
    }

}
