<?php
/**
 * QueueData Class as Eloquent Model
 *
 * @author rutoru
 * @package Runa-CCA
 */
namespace Runa_CCA\Model\Database;

class QueueData extends \Illuminate\Database\Eloquent\Model{
    
/*
CREATE TABLE queue_data
(
	`CallSid` CHAR(34) PRIMARY KEY NOT NULL UNIQUE,
	`From` VARCHAR(255),
	`To` VARCHAR(255),
	`CallStatus` VARCHAR(15),
	`ApiVersion` CHAR(10),
	`Direction` VARCHAR(15),
	`ForwardedFrom` VARCHAR(255),
	`CallerName` VARCHAR(255),
	`QueueSid` CHAR(34),
	`QueueTime` INT,
	`DequeingCallSid` CHAR(34),
        `updated_at` DATETIME,
        `created_at` DATETIME,
         INDEX cst_idx(CallStatus),
         INDEX qu_idx(QueueSid),
         INDEX dqu_idx(DequeingCallSid)
)
ENGINE InnoDB;

 */

    public $table = 'queue_data';
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
                                'QueueSid',
                                'QueueTime',
                                'DequeingCallSid',
                            ];
    

}
