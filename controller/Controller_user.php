<?php
/**
 * Kontrolér pro správu uživatele 
 */
class Controller_user extends Controller {
    
    /* hodnost uživatele*/    
    protected $rank;
    
    /**
     * při vytvoření kontroleru se i automaticky nastaví view, které se má použít
     */
    public function __construct() {
      $this->view = "user"; 
    }
    
    /**
     * Přepisuje hodnotu ze "sešny" updatu, a podle toho se pohled řídí, zda má vykreslit pouze info, nebo formulář pro úpravu info
     * Při stisku úpravy se textboxy předvyplní údaji z databáze až na heslo
     * Při poslání požadavku pro úpravu info se nejdříve zkontroluje vše, co je třeba a poté se upraví údaje
     */
    public function DoIt() {                                 
      if (!empty($_SESSION['uzivatel'])) {
        $this -> rank = Konf::getRank($_SESSION['uzivatel']['Hodnost_ID_HODNOST']);
        $this -> rank = array_shift($this -> rank);
      } 
      if (isset($_POST['ButtLogout'])) {
        Konf::logout();
      }
      if (isset($_POST['change'])) {
        $_SESSION['update'] = true;
      }
      if (isset($_POST['changeInfo'])) {
        if ($_POST['jmenoInfo'] != "" && $_POST['emailInfo']){
        
         $jmeno = $_POST['jmenoInfo'];
         if ($_POST['hesloInfo'] == "") $heslo = $_SESSION['uzivatel']['heslo'];
         else {
          $heslo = $_POST['hesloInfo'];
          $options = ['cost' => 12,];
          $heslo = password_hash("$heslo", PASSWORD_BCRYPT, $options);
         }
         $email = $_POST['emailInfo'];
         $organizace = $_POST['organizaceInfo'];
         $hodnost = $_SESSION['uzivatel']['Hodnost_ID_HODNOST'];
         try {      
          Admin::updateInfo($heslo, $jmeno, $email, $organizace, $hodnost, $_SESSION['uzivatel']['uzivatel_jmeno'], $_SESSION['uzivatel']['stav']);
          $row = Login::getUserLogin($_SESSION['uzivatel']['uzivatel_jmeno']);
          $row = array_shift($row);
          $_SESSION['uzivatel'] = $row;
          
          $_SESSION['update'] = false;                                   
          $_SESSION['error'] = ""; 
         }
         catch (Exception $e) {
          //echo $e->getMessage(), "\n";
         }
        }
        else {
         $_SESSION['error'] = "Nejsou zadány všechny důležité informace!! ";
        }
      }
      if (isset($_POST['backInfo'])) {
        $_SESSION['update'] = false;
      } 
    
  }
}