<?php

class Validate {

    private $_passed = false,
            $_errors = array(),
            $_db = null;

    public function __construct() {

        $this->_db = DB::getInstance();
    }   


    //list thorough the rules (items) that we have defined,
    //for each of them we want to check against the source

    
    public function check($source, $items = array()) {


        //$rules is the array that governs the rules for the particular item

        foreach($items as $item => $rules) {
            foreach($rules as $rule => $rule_value) {
                
               //source is either $_GET or $_POST     


                $value = trim($source[$item]);
                $item = escape($item);
               
                //is required: if the value is missing there is no need to go on
                //validating anything else

                if($rule === 'required' && empty($value)) {

                   $this->addError("{$item} is required");
               
                } else if(!empty($value)){
                    
                    switch($rule) {
                        

                        //check if string length of the value is less than the rule_value
                        //that has been defined

                        case 'min': 
                            if(strlen($value) < $rule_value) {
                                $this->addError("{$item} must be a minimum of {$rule_value} characters.");
                                
                            }
                        break;
                        case 'max':
                             if(strlen($value) > $rule_value) {
                                $this->addError("{$item} must be a maximum of {$rule_value} characters.");                              
                            }
                            
                        break;

                            //passwords must match

                        case 'matches':
                            if($value != $source[$rule_value]) {
                                $this->addError("{$rule_value} must match {$item}" );
                            }

                        break;


                        case 'unique':
                            $check = $this->_db->get($rule_value,array($item,'=',$value));
                            if($check->count()) {

                                $this->addError("{$item} already exists");
                            }

                        break;
                    
                    }
                }
            }

           
            }
             if(empty($this->_errors)) {

                $this->_passed = true;

        }

    }
    private function addError($error) {
        $this->_errors[] = $error;

    }

    public function errors() {
        return $this->_errors;
    }


    public function passed() {

        return $this->_passed;


    }
}

?>