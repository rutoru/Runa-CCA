<?php
/**
 * Operator Class as Eloquent Model
 *
 * @author rutoru
 * @package Runa-CCA
 */
namespace Runa_CCA\Model;

class Operator extends \Illuminate\Database\Eloquent\Model{
    
/*
CREATE TABLE operator
(
`operator_id` varchar(20) PRIMARY KEY NOT NULL UNIQUE,
`password` varchar(255),
`last_name` varchar(32),
`first_name` varchar(32),
`client_name` varchar(32),
`telnum` varchar(20),
`opeartor_level_id` int,
INDEX (opeartor_level_id),
FOREIGN key(`operator_level_id`) REFERENCES operator_level(`operator_level_id`)
)
ENGINE InnoDB;

 */

    public $table = 'operator';
    public $primaryKey = 'operator_id';
    public $timestamps = false;
    protected $fillable =   [
                                'operator_id',
                                'password',
                                'last_name',
                                'first_name',
                                'client_name',
                                'telnum',
                                'operator_level_id',
                            ];
    
    /**
     * queue
     * 
     * @return Queue Object
     */
    public function queue()
    {
        return $this->belongsToMany('\Runa_CCA\Model\Queue');
    }
    
}
