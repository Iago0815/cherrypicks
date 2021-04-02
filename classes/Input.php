<?php 

class Input {

    //checks if input has been provided, type is set default to POST

    public static function exists($type = 'post') {

        switch($type) {
            case 'post':
                return (!empty($_POST)) ? true : false;
            break;
            
            case 'get':
                 return (!empty($_GET)) ? true : false;
            break;
            
            default:
              return false;
            break;

        }
    }

    // retrieving an item

    public static function get($item) {
        if(isset($_POST[$item])) {

            return $_POST[$item];
        }

        if(isset($_GET[$item])) {

            return $_GET[$item];
        }

        return '';

    }
}

?>