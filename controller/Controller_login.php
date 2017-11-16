<?php
/**
 * Kontrolér pro přihlášení uživatele do systému
 */
class Controller_login extends Controller {
    
    /* login, co uživatel zadal*/
    protected $login;
    /* heslo, co uživatel zadal*/
    protected $password;
    /* jméno z databáze, pro kontrolu zda takový uživatel existuje*/
    protected $username;
    /* heslo z databáze, pro kontrolu zda zadané heslo je správně*/
    protected $userpass;
    
    /**
     * při vytvoření kontroleru se i automaticky nastaví view, které se má použít
     */
    public function __construct() {
      $this->view = "login"; 
    }
    
    /** 
     * Pokud je uživatel už přihlášen, přesměruje na konferenci
     * Při přihlašování kontrolujeme zda uživatel existuje, heslo musíme pak ještě dešifrovat
     */
    public function DoIt() {
      if (isset($_SESSION['prihlasen']) && $_SESSION['prihlasen'] == true) {
        header('Location: index.php?page=konf');
      }
      
      if (isset($_POST['Buttprihlas'])) {  
        $this->login = $_POST['login'];
        $this->password = $_POST['heslo'];
        
        $row = Login::getUserLogin($this->login);
        $row = array_shift($row);
        
        $this->username = $row['uzivatel_jmeno']; 
        $this->userpass = $row['heslo'];
        if (empty($this->username)) { 
         $_SESSION['error'] = "Špatné uživatelské jméno";
        }
        else {
          if (!password_verify($this -> password, $this -> userpass)) {
             $_SESSION['error'] = "Špatné heslo";
          }
          else {
            if ($row['stav'] == 2) {
              $_SESSION['error'] = "Tento uživatel je zablokovaný!!";
            }
            else {
              $_SESSION['uzivatel'] = $row;
              $_SESSION['prihlasen'] = true;
              $_SESSION['update'] = false;
              $_SESSION['time'] = time();
              header('Location: index.php?page=konf');            
            }       
          }
        }
      }
    }
}