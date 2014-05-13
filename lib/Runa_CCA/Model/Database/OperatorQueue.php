<?php
/**
 * OperatorQueue Class as Eloquent Model
 *
 * @author rutoru
 * @package Runa-CCA
 */
namespace Runa_CCA\Model\Database;

class OperatorQueue extends \Illuminate\Database\Eloquent\Model{
    
/*
CREATE TABLE operator_queue
(
`id` INT PRIMARY KEY NOT NULL UNIQUE AUTO_INCREMENT,
`operator_id` varchar(20) NOT NULL,
`queue_id` varchar(32) NOT NULL,
FOREIGN KEY(`operator_id`) REFERENCES operator(`operator_id`),
FOREIGN key(`queue_id`) REFERENCES queue(`queue_id`)
)
ENGINE InnoDB;

 */

    public $table = 'operator_queue';
    public $timestamps = false;
    protected $fillable = ['operator_id','queue_id'];
    

}
