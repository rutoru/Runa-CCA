<?php
/**
 * Twiml Class
 *
 * @author rutoru
 * @package Runa-CCA
 */
namespace Runa_CCA\View;

class Twiml {

    /**
     * Object Variables
     */
    private $app;
 
    /**
     * Constructor
     * 
     * @param Slim Object 
     */
    public function __construct($app = NULL){
        
        $this->app = $app;
        
    }
    
    /**
     * createOperatorTwiml
     * 
     * @param \Runa_CCA\Model\TwiMLApp $twilioObj Twilio Object
     */    
    public function createOperatorTwiml($twilioObj){
        
        $this->app->render(
                        'Twiml/operator_twiml.twig', 
                        $twilioObj->getOperator()
                    );
        
    }
 
    /**
     * createTwiMl
     * 
     * @param \Twilio $twilioObj Twilio Object
     */    
    public function createTwiml($twilioObj){
        
        print $twilioObj;
        
    }

    
}