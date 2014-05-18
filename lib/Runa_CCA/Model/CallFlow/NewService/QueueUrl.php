<?php
/**
 * QueueUrl Class
 * 
 * @author rutoru
 * @package Runa-CCA
 */
namespace Runa_CCA\Model\CallFlow\NewService;

class QueueUrl {

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
        $dbConn = (new \Runa_CCA\Model\DB())->getIlluminateConnection();
        
        $queue = new \Runa_CCA\Model\Database\QueueData();
        
        // This parameter is set when the IncomingPhoneNumber 
        // that received the call has had its VoiceCallerIdLookup value set to true ($0.01 per look up).
        // from Twilio Site
        if(isset($params["CallerName"])){
            $queue->CallerName = $params["CallerName"];
        }else{
            $queue->CallerName = null;
        }
        
        $queue->CallSid         = $params["CallSid"];
        $queue->From            = $params["From"];
        $queue->To              = $params["To"];
        $queue->CallStatus      = $params["CallStatus"];
        $queue->ApiVersion      = $params["ApiVersion"];
        $queue->Direction       = $params["Direction"];
        $queue->ForwardedFrom   = $params["ForwardedFrom"];
        // This parameter is set when the IncomingPhoneNumber 
        // that received the call has had its VoiceCallerIdLookup value set to true ($0.01 per look up).
        // from Twilio Site
        // $enqueue->CallerName    = $params["CallerName"];
        $queue->QueueSid        = $params["QueueSid"];
        $queue->QueueTime       = $params["QueueTime"];
        $queue->DequeingCallSid = $params["DequeingCallSid"];
        
        $queue->save();

        // Play the dequeue message.
        $this->response->say("オペレータにおつなぎします。",
                        ['language' => \Base\Conf::LANG]);
        
        // TwiML
        return $this->response;
    }

}
