<?php
/**
 * Twilio Password Class as Eloquent Model
 *
 * @author rutoru
 * @package Runa-CCA
 */
namespace Runa_CCA\Model;

class TwilioUser extends \Illuminate\Database\Eloquent\Model{
    
/*
CREATE TABLE twilio_user
(
`twilio_id` varchar(20) PRIMARY KEY NOT NULL UNIQUE,
`password` varchar(255)
)
ENGINE InnoDB;

 */
    
    public $table = 'twilio_user';
    public $primaryKey = 'twilio_id';
    public $timestamps = false;

}
