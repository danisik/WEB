<?php
/**
 * Kontrolér pro admin panel
 */
class Controller_admin extends Controller {
    
    /* hodnost uživatele*/
    protected $rank;
    
    /**
     * při vytvoření kontroleru se i automaticky nastaví view, které se má použít
     */
    public function __construct() {
      $this->view = "admin"; 
    }
    
    /** 
     * Nejdříve zkontroluje, zda v "sešnu" je hodnota true, pokud ano = uživatel je přihlášený
     * Dále pokud se stiskne logout, zajistí odlognutí uživatele
     * Při stistku tlačítka změnit údaje nám nastaví do "sešny" hodnotu true a zobrazí se nám formulář pro úpravu
     * 
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
        if ($_POST['jmenoChange'] != "" && $_POST['emailChange'] && isset($_POST['userSelect'])){
         $jmeno = $_POST['jmenoChange'];
         if ($_POST['hesloChange'] == "") $heslo = $_SESSION['uzivatelChange']['heslo'];
         else {
            $heslo = $_POST['hesloChange'];
            $options = ['cost' => 12,];
            $heslo = password_hash("$heslo", PASSWORD_BCRYPT, $options);
          }
         $email = $_POST['emailChange'];
         $organizace = $_POST['organizaceChange'];
         $hodnost = $_POST['hodnostSelect'];
         $stav = $_POST['stavSelect'];
         try {
          Admin::updateInfo($heslo, $jmeno, $email, $organizace, $hodnost, $_SESSION['uzivatelChange']['uzivatel_jmeno'], $stav);
          self::displayInfo();                              
          $_SESSION['error'] = "";
          $_SESSION['good'] = "Úprava uživatele proběhla úspěšně"; 
         }
         catch (Exception $e) {
          
         }
        }
        else {
         $_SESSION['error'] = "Nejsou zadány všechny důležité informace!! ";
        }
      }
      
      if (isset($_POST['submitUser'])) {
      self::displayInfo();
      }
      else {
        $_SESSION['uzivatelChange']['uzivatel_jmeno'] = "";
        $_SESSION['uzivatelChange']['jmeno'] = "";
        $_SESSION['uzivatelChange']['email'] = "";
        $_SESSION['uzivatelChange']['organizace'] = "";
        $_SESSION['selectBox']['values'] = self::createSelectBoxValues(Admin::getValues(),null);
        $_SESSION['selectBox']['users'] = self::createSelectBoxUsers(Admin::getUsers(),null);
        $_SESSION['selectBox']['stav'] = self::createSelectBoxStav(Admin::getStav(), null);
      }   
  }
  
  /**
   * Vytvoří select box, který obsahuje všechny hodnosti
   */
    public function createSelectBoxValues($rights,$selected){
    $res = '<option value="" disabled selected>Vyberte hodnost</option>';
    foreach($rights as $r){
        if($selected!=null && $selected==$r['ID_HODNOST']){
            $res .= "<option id= '".$r['ID_HODNOST']."' value='".$r['ID_HODNOST']."' selected>$r[nazev]</option>";    
        } else { 
            $res .= "<option id= '".$r['ID_HODNOST']."' value='".$r['ID_HODNOST']."'>$r[nazev]</option>";    
        }        
    }
    return $res;
  }
  
  /**
   * Vytvoří select box, který obsahuje všechny uživatele
   */
    public function createSelectBoxUsers($rights,$selected){
    $res = '<option value="" disabled selected>Vyberte uživatele</option>';
    foreach($rights as $r){
        if($selected!=null && $selected==$r['ID_UZIVATEL']){ 
            $res .= "<option id= '".$r['ID_UZIVATEL']."' value='".$r['ID_UZIVATEL']."' selected>$r[jmeno] - $r[uzivatel_jmeno]</option>";    
        } else { 
            $res .= "<option id= '".$r['ID_UZIVATEL']."' value='".$r['ID_UZIVATEL']."'>$r[jmeno] - $r[uzivatel_jmeno]</option>";    
        }        
    }
    return $res;
  }


  /**
   * Vytvoří select box, který obsahuje všechny stavy
   */
    public function createSelectBoxStav($rights,$selected){
    $res = '<option value="" disabled selected>Vyberte uživatele</option>';
    foreach($rights as $r){
        if($selected!=null && $selected==$r['ID_STAV']){ 
            $res .= "<option id= '".$r['ID_STAV']."' value='".$r['ID_STAV']."' selected>$r[nazev]</option>";    
        } else { 
            $res .= "<option id= '".$r['ID_STAV']."' value='".$r['ID_STAV']."'>$r[nazev]</option>";    
        }        
    }
    return $res;
  }
  
    
  /**
   * Při úpravě informací předvyplní textboxy aktuálními daty
   */
  public function displayInfo() {
    $row = Admin::getUserByID($_POST['userSelect']);
    $_SESSION['uzivatelChange'] = array_shift($row);
        
    $_SESSION['selectBox']['values'] = self::createSelectBoxValues(Admin::getValues(),$_SESSION['uzivatelChange']['Hodnost_ID_HODNOST']);
    $_SESSION['selectBox']['users'] = self::createSelectBoxUsers(Admin::getUsers(),$_SESSION['uzivatelChange']['ID_UZIVATEL']);
    $_SESSION['selectBox']['stav'] = self::createSelectBoxStav(Admin::getStav(), $_SESSION['uzivatelChange']['stav']);
  }
}