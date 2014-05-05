<?php
/**
 * Info Class
 * 
 * @author rutoru
 * @package Runa-CCA
 */
namespace Runa_CCA\Model\CallFlow\NewService;

class Info {

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
     * info
     * 
     * @return \Twilio $response Twilio Object
     */
    public function info(){

        // Information Message.
        $this->response->say(
                            "お電話ありがとうございました。".
                            "ただいま、新サービスリリース検討中です。".
                            "もうしばらくお待ちくださいね。",
                            ['language' => \Base\Conf::LANG]
                            );

        // TwiML
        return $this->response;
    }

}