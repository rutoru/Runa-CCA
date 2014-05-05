<?php
/**
 * TwilioToken Class
 *
 * @author rutoru
 * @package Runa-CCA
 */
namespace Runa_CCA\Model;

class TwilioToken {

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
     * Verify
     * 
     * @param String $id TwiML Access ID
     * @param String $password TwiML Access Password
     * @return boolean Result of verification
     */
    public function verify($id,$password){

        // DB Connection
        \Runa_CCA\Model\DB::registerIlluminate();
        
        $twilioId = \Runa_CCA\Model\TwilioUser::where('twilio_id', '=', $id)->first();

        // Go to main page if 'id' exists and 'password' is verified.
        if(isset($twilioId) && password_verify($password, $twilioId->password)){

            return true;

        }else{
            
            return false;

        }
        
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

    
}
