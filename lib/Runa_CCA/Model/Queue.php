<?php
/**
 * Queue Class as Eloquent Model
 *
 * @author rutoru
 * @package Runa-CCA
 */
namespace Runa_CCA\Model;

class Queue extends \Illuminate\Database\Eloquent\Model{
    
/*
CREATE TABLE queue
(
`queue_id` varchar(20) PRIMARY KEY NOT NULL UNIQUE,
`queue_name` varchar(32) NOT NULL,
`action_url` varchar(128),
`wait_url` varchar(128),
`guidance_url` varchar(128),
`twilio_queue_id` varchar(128),
INDEX (twilio_queue_id)
)
ENGINE InnoDB;

 */
    
    public $table = 'queue';
    public $primaryKey = 'queue_id';
    public $timestamps = false;
    protected $fillable =   [
                                'queue_id',
                                'queue_name',
                                'action_url',
                                'wait_url',
                                'guidance_url',
                                'twilio_queue_id',
                            ];

    /**
     * operator
     * 
     * @return Operator Object
     */

    public function operator()
    {
        return $this->belongsToMany('\Runa_CCA\Model\Operator');
    }

    
}
