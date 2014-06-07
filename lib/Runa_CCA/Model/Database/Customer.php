<?php
/**
 * Customer Class as Eloquent Model
 *
 * @author rutoru
 * @package Runa-CCA
 */
namespace Runa_CCA\Model\Database;

class Customer extends \Illuminate\Database\Eloquent\Model{
    
/*
CREATE TABLE customer
(
`customer_id` varchar(20) PRIMARY KEY NOT NULL UNIQUE,
`last_name` varchar(32),
`first_name` varchar(32),
`telnum` varchar(20),
`contact_record` text,
INDEX (telnum)
)
ENGINE InnoDB;

 */

    public $table = 'customer';
    public $primaryKey = 'customer_id';
    public $timestamps = false;
    protected $fillable =   [
                                'customer_id',
                                'last_name',
                                'first_name',
                                'telnum',
                                'contact_record',
                            ];
       
}
