<?php
/**
 * TwiMLApp Class
 *
 * @author rutoru
 * @package Runa-CCA
 */
namespace Runa_CCA\Model;

class TwiMLApp {

    /**
     * Object Variables
     */
    private $response;
        
    /**
     * Constructor
     * 
     */
    public function __construct(){       

        // Twiml Object
        $this->response = new \Services_Twilio_Twiml();
        
    }
    
    /**
     * createTwiMLApp
     * 
     * @param Array $params Post Parameter
     * @return \Twilio $response Twilio Object
     */
    public function createTwiMLApp($params){

        // Sanitize because we cannot use autoescape tag in the twig
        $number  = \Base\Conf::h($params['PhoneNumber']);
        
        // DB Connection
        $dbConn = \Runa_CCA\Model\DB::getIlluminateConnection();

        // Check if the queue exists.
        $queueObj = \Runa_CCA\Model\Database\Queue::where('queue_id', '=', $number)->first();
        
        // PhoneNumber is a queue.
        if (isset($queueObj->queue_name)){

            // Use Queue verb.
            $this->response->say($queueObj->queue_name."キューにはいりました。", ['language' => \Base\Conf::LANG]);
            $dial = $this->response->dial();
            $dial->queue($number,
                        ['url' => $queueObj->guidance_url]);
        
        // PhoneNumber is a phone number.
        } elseif (preg_match("/^[\d\+\-\(\) ]+$/", $number)) {
            
            // Use Number verb.
            $dial = $this->response->dial(NULL, ['callerId' => \Base\Conf::ACCOUNT_CALLID]);
            $dial->number($number);
        
        // PhoneNumber is another client.
        } else {
            
            // Use Client verb.
            $dial = $this->response->dial(NULL, ['callerId' => \Base\Conf::ACCOUNT_CALLID]);
            $dial->client($number);

        }

        // TwiML
        return $this->response;       
    }
    
}
