<?php

namespace Fauzannurhidayat\PhpMvc\Login\App {

    function header(string $value)
    {
        echo $value;
    }
}

namespace Fauzannurhidayat\PhpMvc\Login\Service {

    function setcookie(string $name, string $value)
    {
        echo "$name : $value";
    }
}

