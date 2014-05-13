<?php
/**
 * Validator Class
 *
 * @author rutoru
 * @package Runa-CCA
 */
namespace Runa_CCA\Model;

class Validator extends \Respect\Validation\Validator {

    /**
     * validateOperator
     * 
     * @param String[] $params User Input Data
     * @return TwiMLApp Token
     */
    static function validateOperator(array $params){

        $error_list = [];
        
        if(!static::notEmpty()->alnum()->length(1,20)->validate($params["operator_id"])){
            $error_list["operator_id"] = "20字以内の半角の英数文字のみにしてください。";
        }
        
        // Pass password check because update operators function doesn't have password parameter. 
        if(isset($params["password"])){
            if(!static::notEmpty()->charset('ASCII')->length(1,20)->validate($params["password"])){
                $error_list["password"] = "20字以内の半角の英数文字のみにしてください。";
            }
        }

        if(!static::notEmpty()->length(1,32)->validate($params["last_name"])){
            $error_list["last_name"] = "全角16字以内半角32字以内にしてください。";
        }

        if(!static::notEmpty()->length(1,32)->validate($params["first_name"])){
            $error_list["first_name"] = "全角16字以内半角32字以内にしてください。";
        }
        
        if(!static::notEmpty()->alnum()->length(1,20)->validate($params["client_name"])){
            $error_list["client_name"] = "20字以内の半角の英数文字のみにしてください。";
        }
        
        if(!static::notEmpty()->phone()->length(1,15)->validate($params["telnum"])){
            $error_list["telnum"] = "国番号付きでE.164形式ににしてください。";
        }

        // Pass password check because add operators function doesn't have tag parameter. 
        if(isset($params["tag"])){
            if(!static::notEmpty()->alnum()->length(1,10)->validate($params["tag"])){
                $error_list["tag"] = "タグがありません。";
            }
        }
        
        return $error_list;
        
    }

    /**
     * validateQueue
     * 
     * @param String[] $params User Input Data
     * @return TwiMLApp Token
     */
    static function validateQueue(array $params){

        $error_list = [];
        
        if(!static::notEmpty()->alnum()->length(1,20)->validate($params["queue_id"])){
            $error_list["queue_id"] = "20字以内の半角の英数文字のみにしてください。";
        }
        
        if(!static::notEmpty()->length(1,32)->validate($params["queue_name"])){
            $error_list["queue_name"] = "全角16字以内・半角32字以内にしてください。";
        }

        if(!static::notEmpty()->int()->max(1001)->validate($params["max_size"])){
            $error_list["max_size"] = "1,000以内にしてください。";
        }

        if(!static::notEmpty()->length(1,128)->charset('ASCII')->validate($params["action_url"])){
            $error_list["action_url"] = "128字以内の半角文字のみにしてください。";
        }

        if(!static::notEmpty()->length(1,128)->charset('ASCII')->validate($params["wait_url"])){
            $error_list["wait_url"] = "128字以内の半角文字のみにしてください。";
        }

        if(!static::notEmpty()->length(1,128)->charset('ASCII')->validate($params["guidance_url"])){
            $error_list["guidance_url"] = "128字以内の半角文字のみにしてください。";
        }

        // Pass password check because add operators function doesn't have tag parameter. 
        if(isset($params["tag"])){
            if(!static::notEmpty()->alnum()->length(1,10)->validate($params["tag"])){
                $error_list["tag"] = "タグがありません。";
            }
        }
        
        return $error_list;
        
    }

    /**
     * validateOpQueue
     * 
     * @param \Runa_CCA\Model\Database\Queue $queues Queue List
     * @param String[] $params User Input Data
     * @return TwiMLApp Token
     */
    static function validateOpQueue($queues, $params){

        foreach ($queues as $queue){

            if(! array_search($queue->queue_id, $params)){
                return false;
            }

        }

        return true;
    }
    
    /**
     * validateOpLevel
     * 
     * @param String[] $levels Operator Level List
     * @param String[] $params User Input Data
     * @return TwiMLApp Token
     */
    static function validateOpLevel($levels, $params){

        foreach ($levels as $level){

            if($params == $level["operator_level_id"]){
                return false;
            }

        }

        return true;
    }
    
    /**
     * validatePassword
     * 
     * @param String[] $params User Input Data
     * @return TwiMLApp Token
     */
    static function validatePassword(array $params){

        $error_list = [];
         
        if($params["password1"] != $params["password2"]){
            $error_list["password"] = "パスワードが一致しません。";
        }
        
        if(!static::notEmpty()->alnum()->length(1,20)->validate($params["password1"])){
            $error_list["password1"] = "20字以内の半角の英数文字のみにしてください。";
        }

        if(!static::notEmpty()->alnum()->length(1,20)->validate($params["password2"])){
            $error_list["password2"] = "20字以内の半角の英数文字のみにしてください。";
        }
        
        return $error_list;
        
    }
    
}
