<?php

class Config {

    public static function get($path = null) {

        if($path)  {

            $config = $GLOBALS['config'];
            $path = explode('/',$path);

            foreach($path as $bit) {

                if(isset($config[$bit])) {        //1.)loop:  $GLOBALS['config']['mysql']
            //   2.)loop: $GLOBALS['config']['mysql']['host']

                  $config = $config[$bit];
                }
            }

            return $config;
          
        }

        return false;
    }

}


?>