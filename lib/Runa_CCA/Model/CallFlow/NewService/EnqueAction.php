<?php
/**
 * EnqueAction Class
 * 
 * @author rutoru
 * @package Runa-CCA
 */
namespace Runa_CCA\Model\CallFlow\NewService;

class EnqueAction {

    /**
     * Object Variables
     */
    private $response;
    
    /**
     * Constructor
     * 
     */
    public function __construct()
    {
        // Twiml Object
        $this->response = new \Services_Twilio_Twiml();
        
    }

    /**
     * insert
     * 
     * @param Array $params Post Parameter
     * @return \Twilio $response Twilio Object
     */
    public function insert($params){

        // DB Connection
        \Runa_CCA\Model\DB::registerIlluminate();
        
        $enqueue = new \Runa_CCA\Model\EnqueueData();

        // This parameter is set when the IncomingPhoneNumber 
        // that received the call has had its VoiceCallerIdLookup value set to true ($0.01 per look up).
        // from Twilio Site
        if(isset($params["CallerName"])){
            $enqueue->CallerName = $params["CallerName"];
        }else{
            $enqueue->CallerName = null;
        }
        
        $enqueue->CallSid       = $params["CallSid"];
        $enqueue->From          = $params["From"];
        $enqueue->To            = $params["To"];
        $enqueue->CallStatus    = $params["CallStatus"];
        $enqueue->ApiVersion    = $params["ApiVersion"];
        $enqueue->Direction     = $params["Direction"];
        $enqueue->ForwardedFrom = $params["ForwardedFrom"];
        $enqueue->QueueResult   = $params["QueueResult"];
        $enqueue->QueueSid      = $params["QueueSid"];
        $enqueue->QueueTime     = $params["QueueTime"];
        
        $enqueue->save();
        
        // TwiML
        // If there is no response, Twilio sends an error message when the operator side hang up the call.
        return $this->response;
    }

}