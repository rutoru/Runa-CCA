<?php
/**
 * StatusCallback Class
 * 
 * @author rutoru
 * @package Runa-CCA
 */
namespace Runa_CCA\Model\CallFlow\NewService;

class StatusCallback {

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
        
        $statusCb = new \Runa_CCA\Model\StatusCallbackData();
        
        // This parameter is set when the IncomingPhoneNumber 
        // that received the call has had its VoiceCallerIdLookup value set to true ($0.01 per look up).
        // from Twilio Site
        if(isset($params["CallerName"])){
            
            $statusCb->CallerName = $params["CallerName"];
            
        }else{
            
            $statusCb->CallerName = null;
        
            
        }
        
        // Recording Parameters
        if(isset($params["RecordingSid"])){
            
            $statusCb->RecordingUrl      = $params["RecordingUrl"];
            $statusCb->RecordingSid      = $params["RecordingSid"];
            $statusCb->RecordingDuration = $params["RecordingDuration"];
        
        }else{
            
            $statusCb->RecordingUrl      = null;
            $statusCb->RecordingSid      = null;
            $statusCb->RecordingDuration = null;   
            
        }
        
        $statusCb->CallSid           = $params["CallSid"];
        $statusCb->From              = $params["From"];
        $statusCb->To                = $params["To"];
        $statusCb->CallStatus        = $params["CallStatus"];
        $statusCb->ApiVersion        = $params["ApiVersion"];
        $statusCb->Direction         = $params["Direction"];
        $statusCb->ForwardedFrom     = $params["ForwardedFrom"];
        $statusCb->CallDuration      = $params["CallDuration"];

        $statusCb->save();
        
        // TwiML
        return $this->response;
    }

}