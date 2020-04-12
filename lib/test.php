<?php

namespace Payhere;

require_once "Inpayments.php";

class Test
{
    public function getToken()
    {
        $coll = Inpayments::getAuthentication();
        echo $coll;
    }
}


if (!debug_backtrace()) {
    $obj = new Test();
    $obj;
}
