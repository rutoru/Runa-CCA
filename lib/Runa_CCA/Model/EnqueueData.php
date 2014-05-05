<?php
/**
 * EnqueueData Class as Eloquent Model
 *
 * @author rutoru
 * @package Runa-CCA
 */
namespace Runa_CCA\Model;

class EnqueueData extends \Illuminate\Database\Eloquent\Model{
    
/*
CREATE TABLE enqueue_data
(
	`CallSid` CHAR(34) PRIMARY KEY NOT NULL UNIQUE,
	`From` VARCHAR(255),
	`To` VARCHAR(255),
	`CallStatus` VARCHAR(15),
	`ApiVersion` CHAR(10),
	`Direction` VARCHAR(15),
	`ForwardedFrom` VARCHAR(255),
	`CallerName` VARCHAR(255),
	`QueueResult` VARCHAR(15),
	`QueueSid` CHAR(34),
	`QueueTime` INT,
        `updated_at` DATETIME,
        `created_at` DATETIME,
         INDEX cst_idx(CallStatus),
         INDEX rqu_idx(QueueResult),
         INDEX qu_idx(QueueSid)
)
ENGINE InnoDB;

 */

    public $table = 'enqueue_data';
    public $primaryKey = 'CallSid';
    //public $timestamps = false;
    protected $fillable =   [
                                'CallSid',
                                'From',
                                'To',
                                'CallStatus',
                                'ApiVersion',
                                'Direction',
                                'ForwardedFrom',
                                'CallerName',
                                'QueueResult',
                                'QueueSid',
                                'QueueTime',
                            ];
    

}
