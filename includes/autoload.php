<?php 

# autoload include
spl_autoload_register(function ($class_name) {
    $class_file = PATH_CLASSES."$class_name.php";
    if(file_exists($class_file)) {
        require($class_file);
    } else {
        error_log("class $class_name ($class_file) not found!");
        set_alert("error", "Hubo un problema, favor de intentar nuevamente!");
        redirect("./");
    }
});

?>