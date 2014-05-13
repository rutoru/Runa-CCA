<?php
/**
 * StatusCallbackData Class as Eloquent Model
 *
 * @author rutoru
 * @package Runa-CCA
 */
namespace Runa_CCA\Model\Database;

class StatusCallbackData extends \Illuminate\Database\Eloquent\Model{
    
/*
CREATE TABLE statuscallback_data
(
	`CallSid` CHAR(34) PRIMARY KEY NOT NULL UNIQUE,
	`From` VARCHAR(255),
	`To` VARCHAR(255),
	`CallStatus` VARCHAR(15),
	`ApiVersion` CHAR(10),
	`Direction` VARCHAR(15),
	`ForwardedFrom` VARCHAR(255),
	`CallerName` VARCHAR(255),
	`CallDuration` INT,
	`RecordingUrl` VARCHAR(255),
	`RecordingSid` CHAR(34),
        `RecordingDuration` INT,
        `updated_at` DATETIME,
        `created_at` DATETIME,
         INDEX cst_idx(CallStatus),
         INDEX rsid_idx(RecordingSid)
)
ENGINE InnoDB;
 */

    public $table = 'statuscallback_data';
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
                                'CallDuration',
                                'RecordingUrl',
                                'RecordingSid',
                                'RecordingDuration',
                            ];
    

}
