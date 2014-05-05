<?php
/**
 * Configuration Class
 *
 * @author rutoru
 * @package Runa-CCA
 */
namespace Base;

class Conf {

    /**
     * Twilio Account Information
     * 
     */
    // Set your Twilio API credentials here
    const ACCOUNT_SID    = '';
    const ACCOUNT_TOKEN  = '';
    // Set your Twilio Application Sid here
    const APP_SID        = '';
    // put a phone number you've verified with Twilio to use as a caller ID number
    const ACCOUNT_CALLID = '';
    
    /**
     * Constants
     * 
     */
    const DEBUG        = true;
    const METHOD       = "POST";
    const CHAR         = "UTF-8";
    const LANG         = "ja-jp";
    const APP_NAME     = "Runa-CCA";
    
    /**
     * Constants for Database (Used in DB Class)
     * 
     */
    const DBDRVR = "mysql";
    const DBHOST = "localhost";
    const DBNAME = "twilio";
    const DBUSER = "";
    const DBPASS = "";
    const DBCHAR = "utf8";
    const DBCOLL = "utf8_unicode_ci";
    const DBPRIF = "";
    
    /**
     * Constants for Runa-CCA System
     * 
     */
    const TEMPLATES      = "../templates";   // For Slim Framework

    /**
     * htmlspecialchars
     * 
     * @return String[]/String Sanitized String
     */
    static function h($var){
        
        if(is_array($var)){
            // Callback h function
            return array_map('h',$var);
        }else{
            return htmlspecialchars($var,ENT_QUOTES, Self::CHAR);
        }
    }

}