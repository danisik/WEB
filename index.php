    <?php 
    ini_set('display_errors', '1'); //výpis errorů
    session_start(); //zapnutí "sešny"
    mb_internal_encoding("UTF-8");      

    /**
     * Autoload funkce, zajištujě správné naincludování modelů a controllerů
     */
    function autoload($class) {
        if(mb_strpos($class, "Controller")===false)
            require("model/$class.php");
        else
            @require("controller/$class.php"); 
    }
    spl_autoload_register("autoload");

    /**
     * Podle page v url se zjistí, jaká instance kontroleru se má vytvořit
     */
    if(isset($_GET['page']))
    {
        $_GET['page'] = "Controller_".$_GET['page'];
        
    	$controller= new $_GET['page'];
    }
    else
    	$controller = new Controller_main;  
      
      
    $db = new Database; 
    $controller->DoIt();
    $controller->Display();
    ?> 