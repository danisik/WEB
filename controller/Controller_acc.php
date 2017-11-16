<?php
/**
 * Kontrolér pro vytváření účtu
 */
class Controller_acc extends Controller {
    /* uživatelské jméno uživatele*/
    protected $uzivatel_jmeno;
    /* heslo uživatele, bude zašifrované*/ 
    protected $heslo;
    /* pouze pro kontrolu, zda heslo co uživatel chtěl sedí*/
    protected $potvrd_heslo;
    /* jeho rodné jméno a příjmení*/
    protected $jmeno;
    /* jeho email*/
    protected $email;
    /* organizace/firma ve které pracuje */
    protected $organizace;
    /* id posledního uživatele v tabulce*/
    protected $lastID;
    
    /**
     * při vytvoření kontroleru se i automaticky nastaví view, které se má použít
     */
    public function __construct() {
      $this->view = "acc";
    }
    
    /** 
     * Nejdříve zkontroluje, zda v "sešnu" je hodnota true, pokud ano = uživatel je přihlášený
     * Dále při stistku tlačítka kontroluje, zda jsou všechny hodnoty potřebné pro vytvoření účtu zadány a potom vytvoří nový účet a rovnou ho i přihlásí
     */                                  
    public function DoIt() {
      if (isset($_SESSION['prihlasen']) && $_SESSION['prihlasen'] == true) {
        header('Location: index.php?page=konf');
      }
      if (isset($_POST['ButtnewAcc'])) { 
        $uzivatel_jmeno = $_POST['uziv_jmeno'];
        $heslo = $_POST['heslo'];
        $potvrd_heslo = $_POST['potvrzeni_hesla'];
        $jmeno = $_POST['jmeno'];
        $email = $_POST['email'];
        $organizace = $_POST['organizace'];
        
        $check_jmeno = Acc::getNameAcc($uzivatel_jmeno);
        $check_email = Acc::getEmailAcc($email); 
        
        if ($uzivatel_jmeno != "" && $heslo != "" && $potvrd_heslo != "" && $jmeno != "" && $email != "" && $organizace != "") {
          if (!empty($check_jmeno)) {
            $_SESSION['error'] = "Toto jméno už existuje!";
          } 
          else {
            if (!empty($check_email)) {
              $_SESSION['error'] = "Tento email už je zaregistrovaný";
          }
            else {
              if ($heslo != $potvrd_heslo) {
                $_SESSION['error'] = "Hesla se neshodují!";
              }
              else {            
                try {
                  
                  $options = ['cost' => 12,];
                  $heslo = password_hash("$heslo", PASSWORD_BCRYPT, $options);
                  
                  $last = Acc::getLastIDUserAcc();
                  $last = array_shift($last);
                  $lastID = $last['MAX(ID_UZIVATEL)'] + 1;
                  $hodnost = 3;
                  $stav = 1;
                  Acc::insertIntoDBAcc($lastID, $uzivatel_jmeno, $heslo, $jmeno, $email, $organizace, $hodnost);
                  $row = array( 
                      "ID_UZIVATEL" => $lastID,
                      "uzivatel_jmeno" => "$uzivatel_jmeno",
                      "heslo" => "$heslo",
                      "jmeno" => "$jmeno",
                      "email" => "$email",
                      "organizace" => "$organizace",
                      "Hodnost_ID_HODNOST" => $hodnost,
                      "stav" => $stav,
                  );
                  
                  $_SESSION['uzivatel'] = $row;
                  $_SESSION['prihlasen'] = true;
                  header('Location: index.php?page=konf');
                }
                catch (Exception $e) {
                  //echo $e->getMessage(), "\n";
                  
                  $row = array( 
                      "ID_UZIVATEL" => $lastID,
                      "uzivatel_jmeno" => "$uzivatel_jmeno",
                      "heslo" => "$heslo",
                      "jmeno" => "$jmeno",
                      "email" => "$email",
                      "organizace" => "$organizace",
                      "Hodnost_ID_HODNOST" => 3,
                      "stav" => $stav,
                  );
                  
                  $_SESSION['uzivatel'] = $row;
                  $_SESSION['prihlasen'] = true;
                  header('Location: index.php?page=konf');
                }
              } 
            }
          }
        }
        else {
          $_SESSION['error'] = "Není vyplněn celý formulář!";
        }
        
    }
  }
}