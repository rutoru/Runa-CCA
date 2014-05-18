<?php
/**
 * Wallboard Class
 *
 * @author rutoru
 * @package Runa-CCA
 */
namespace Runa_CCA\Model;

class Wallboard {

    /**
     * Object Variables
     */
    public $sid;
    public $friendly_name;
    public $current_size;
    public $max_size;
    public $average_wait_time;
    public $max_wait_time;

    /**
     * Constructor
     * 
     * @param String $sid sid
     * @param String $friendly_name friendly_name
     * @param String $current_size current_size
     * @param String $max_size max_size
     * @param String $average_wait_time average_wait_time
     * @param String $max_wait_time max_wait_time
     */
    public function __construct($sid, 
                                $friendly_name, 
                                $current_size, 
                                $max_size, 
                                $average_wait_time, 
                                $max_wait_time){
        
        $this->sid               = $sid;
        $this->friendly_name     = $friendly_name;
        $this->current_size      = $current_size;
        $this->max_size          = $max_size;
        $this->average_wait_time = $average_wait_time;
        $this->max_wait_time     = $max_wait_time;
    
    }
        
}
