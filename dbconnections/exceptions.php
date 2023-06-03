<?php

namespace Markt;
class MarktExceptions extends \Exception{
    /**
     * Determines the type of error to send
     * @param mixed $errorcase
     * @return mixed
     */
    function error(){
        echo "Some Error";
    }
}
?>