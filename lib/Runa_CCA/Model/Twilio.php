<?php
/**
 * Twilio Class
 *
 * @author rutoru
 * @package Runa-CCA
 */
namespace Runa_CCA\Model;

class Twilio {

    /**
     * Object Variables
     */
    private $capacity;
        
    /**
     * Constructor
     * 
     */
    public function __construct(){
    }
    
    /**
     * Get Token
     * 
     * @param String $name Client Id
     * @return TwiMLApp Token
     */
    public function getTwilioToken($name){

        $this->capability = new \Services_Twilio_Capability
                            (
                                \Base\Conf::ACCOUNT_SID,
                                \Base\Conf::ACCOUNT_TOKEN
                            );

        $this->capability->allowClientOutgoing(\Base\Conf::APP_SID);

        $this->capability->allowClientIncoming($name);
            
        return $this->capability->generateToken();
       
    }
    
    /**
     * Get TwilioClient
     * 
     * @return \Services_Twilio Twilio Object
     */
    public function getTwilioClient(){

        return (new \Services_Twilio
                        (\Base\Conf::ACCOUNT_SID,
                         \Base\Conf::ACCOUNT_TOKEN
                        ))
                ;
       
    }

    /**
     * validateTwilioRequest
     * 
     * @param \Slim\Slim $app Slim Object
     * @param Array $params Input parameters
     * @return Boolean Result of the validation
     */
    public function validateTwilioRequest($app, $params){

        // Get the request.
        $req = $app->request;
        
        // Get the X-Twilio-Signature header.
        $twilioSignature = $req->headers->get('HTTP_X_TWILIO_SIGNATURE');
        
        // Get the Twilio request URL.
        $url = $req->getUrl().$req->getPath();
        
        // Create Validator
        $validator = new \Services_Twilio_RequestValidator(\Base\Conf::ACCOUNT_TOKEN);
        
        // The post variables in the Twilio request.
        // Validation needs all date of the parameters.
        $postVars = $params;
        
        // Debug
        $app->log->debug(strftime("[%Y/%m/%d %H:%M:%S]:".__FILE__.":".__LINE__));
        $app->log->debug("Twilio Signature, Requested URL and Posted variables:");
        $app->log->debug(print_r($twilioSignature,true));
        $app->log->debug(print_r($url,true));
        $app->log->debug(print_r($postVars,true));

        // Validate
        if ($validator->validate($twilioSignature, $url, $postVars)) {
            
            return true;
            
        }else{
            
            return false;

        }
       
    }
    
    
}
