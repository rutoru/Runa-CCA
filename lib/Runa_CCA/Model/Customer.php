<?php
/**
 * Customer Class
 *
 * @author rutoru
 * @package Runa-CCA
 */
namespace Runa_CCA\Model;

class Customer {

    /**
     * Object Variables
     */
    public $customer_id;
    public $last_name;
    public $first_name;
    public $telnum;
    public $queue_id;
    public $dnis;
    public $contact_record;
    
    /**
     * Constructor
     * 
     * @param String $customer_id customer_id
     * @param String $last_name last_name
     * @param String $first_name first_name
     * @param String $telnum telnum
     * @param String $queue_id queue_id
     * @param String $dnis dnis
     * @param String $contact_record contact_record
     */
    public function __construct($customer_id, 
                                $last_name, 
                                $first_name, 
                                $telnum,
                                $queue_id,
                                $dnis,
                                $contact_record){
        
        $this->customer_id    = $customer_id;
        $this->last_name      = $last_name;
        $this->first_name     = $first_name;
        $this->telnum         = $telnum;
        $this->queue_id       = $queue_id;
        $this->dnis           = $dnis;
        $this->contact_record = $contact_record;
        
    }
        
}
