<?php
namespace Payhere;

require_once("Inpayment.php");

class Test{
    function  getToken(){
        $coll = Inpayment::getToken();
        echo $coll;
    }
}


if (!debug_backtrace()) {

    $obj = new Test();
     $obj->getToken();
}
