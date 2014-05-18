<?php
/**
 * OperatorLevel Class
 *
 * @author rutoru
 * @package Runa-CCA
 */
namespace Runa_CCA\Model\Database;

class OperatorLevel{
    
    /**
     * Constants
     * 
     */
    const LV_SYSTEMADMIN_ID   = 1;
    const LV_SUPERVISOR_ID    = 2;
    const LV_OPERATOR_ID      = 3;
    const LV_SYSTEMADMIN_NAME = "SystemAdmin";
    const LV_SUPERVISOR_NAME  = "Supervisor";
    const LV_OPERATOR_NAME    = "Operator";

    /**
     * Object Variables
     */
    private $operatorLevels;
        
    /**
     * Constructor
     * 
     */
    public function __construct(){       

        $this->operatorLevels = [
            ["operator_level_id" => Self::LV_SYSTEMADMIN_ID, "operator_level_name" => Self::LV_SYSTEMADMIN_NAME],
            ["operator_level_id" => Self::LV_SUPERVISOR_ID,  "operator_level_name" => Self::LV_SUPERVISOR_NAME],
            ["operator_level_id" => Self::LV_OPERATOR_ID,    "operator_level_name" => Self::LV_OPERATOR_NAME]
        ];
        
    }
    
    /**
     * getOperatorLevels
     * 
     * @return Array Operator Levels
     */
    public function getOperatorLevels(){
        
        return $this->operatorLevels;
        
    }
    
    /**
     * getConfigBorder
     * 
     * @return String Border line of entering the config page
     */
    public function getConfigBorder(){
        
        return Self::LV_SUPERVISOR_ID;
        
    }

    /**
     * getOpConfigBorder
     * 
     * @return String Border line of entering the config page
     */
    public function getOpConfigBorder(){
        
        return Self::LV_SUPERVISOR_ID;
        
    }
    
    /**
     * getQueueConfigBorder
     * 
     * @return String Border line of entering the config page
     */
    public function getQueueConfigBorder(){
        
        return Self::LV_SYSTEMADMIN_ID;
        
    }

    /**
     * getReportBorder
     * 
     * @return String Border line of entering the report page
     */
    public function getReportBorder(){
        
        return Self::LV_OPERATOR_ID;
        
    }
    
}
