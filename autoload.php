<?php

function load($namespace)
{
    $namespace = str_replace("\\", DIRECTORY_SEPARATOR, $namespace);
    $caminhoAbsoluto = __DIR__ . DIRECTORY_SEPARATOR . $namespace . ".php";

    return include_once $caminhoAbsoluto;
}

spl_autoload_register(__NAMESPACE__ . "\load");
