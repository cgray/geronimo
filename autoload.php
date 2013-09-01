<?php
$autoload_namespaces = array(
    "Alert" => "/vendor/rdlowrey/Alert/src",
    "Amp"=> "/vendor/rdlowrey/Amp/src",
    "Artax"=>"/vendor/rdlowrey/Artax/src",
    "Geronimo"=>"/src"
);

spl_autoload_register(function($class) use ($autoload_namespaces) {
    
    $namespace = substr($class, 0, strpos($class, "\\"));
    
    if (array_key_exists($namespace, $autoload_namespaces)) {
        $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);

        $file = __DIR__ . $autoload_namespaces[$namespace].DIRECTORY_SEPARATOR."$class.php";
        if (file_exists($file)) {
            require $file;
        }
    }
});
