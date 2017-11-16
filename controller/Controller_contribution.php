<?php
/**
 * Kontrolér pro manipulaci s příspěvkem
 */
class Controller_contribution extends Controller {
    /* hodnost uživatele*/
    protected $rank;
    
    /**
     * při vytvoření kontroleru se i automaticky nastaví view, které se má použít
     */
    public function __construct() {
      $this->view = "contribution"; 
    }
    
    /** 
     * Při stisku tlačítka logout se uživatel odhlásí
     * Předvyplní jeho hodnost z důvodu vyobrazení jeho hodnosti 
     * Pokud v url adrese budeme mít nějaké ID a vytvářeli jsme tento příspěvek, pak předvyplníme všechny textboxy údaji z databáze, jinak budou prázdný
     * Při vytváření nového příspěvku nejdříve zkontrolujeme povinná pole, potom zda soubor existuje či ne a podle toho upravujeme/vytváříme příspěvek
     */
    public function DoIt() {
    if (isset($_SESSION['uzivatel'])) {
        if (!empty($_SESSION['uzivatel'])) {
        $this -> rank = Konf::getRank($_SESSION['uzivatel']['Hodnost_ID_HODNOST']);
        $this -> rank = array_shift($this -> rank);
        }
      }
        if (isset($_POST['ButtLogout'])) {
          Konf::logout();
        }
        if (isset($_GET['id'])) {
          $contribution = Contribution::getContribution($_GET['id']);
          $contribution = array_shift($contribution);
          if ($contribution['stav'] == 2 || isset($contribution['Uzivatel_ID_UZIVATEL'])) { 
            $_SESSION['contribution']['nazev'] = $contribution['nazev'];
            $_SESSION['contribution']['autor'] = $contribution['autori'];
            $_SESSION['contribution']['text'] = $contribution['text'];
            $_SESSION['contribution']['Uzivatel_ID_UZIVATEL'] = $contribution['Uzivatel_ID_UZIVATEL'];
            $_SESSION['contribution']['pdf'] = substr($contribution['soubor'], 4);
            $_SESSION['contribution']['stav'] = $contribution['stav'];
            $_SESSION['contribution']['update'] = true;
          }                                            
          else {
            $_SESSION['contribution']['nazev'] = "";
            $_SESSION['contribution']['Uzivatel_ID_UZIVATEL'] = "";
            $_SESSION['contribution']['autor'] = "";
            $_SESSION['contribution']['text'] = "";
            $_SESSION['contribution']['pdf'] = "";
            $_SESSION['contribution']['stav'] = "";
            $_SESSION['contribution']['update'] = false;                    
          }  
        }
        else {
          $_SESSION['contribution']['nazev'] = "";
          $_SESSION['contribution']['Uzivatel_ID_UZIVATEL'] = "new";
          $_SESSION['contribution']['autor'] = "";
          $_SESSION['contribution']['text'] = "Popis: ***";
          $_SESSION['contribution']['pdf'] = "";
          $_SESSION['contribution']['stav'] = "";
          $_SESSION['contribution']['update'] = false;
        } 
    
    if (isset($_POST['ButtnewContribution'])) {
        if ($_POST['nazev_prispevku'] == "" || $_POST['autori'] == "" || $_POST['popis'] == "") {
          $_SESSION['error'] = "Vyplňte všechna potřebná pole!!";
        }
        else {
          $_SESSION['error'] = "";
          $target_dir = "pdf/";
          $target_file = $target_dir . basename($_FILES['pdf']['name']);
          $fileType = pathinfo($target_file, PATHINFO_EXTENSION);
          if (file_exists($target_file) && strlen($target_file) > 4) {
            $_SESSION['error'] = "Tento soubor už existuje!!";
          }
          else {
            if ($fileType != "pdf" && strlen($target_file) > 4) {
            $_SESSION['error'] = "Soubor není formátu typu pdf !!";
            }
            else {
              $contribution = Contribution::getLastIDContribution();
              $contribution = array_shift($contribution);
              $idContribution = $contribution['MAX(ID_PRISPEVEK)'] + 1;
              $contribution = Contribution::getContribution($_GET['id']);
              $contribution = array_shift($contribution);
              
              $nazev = $_POST['nazev_prispevku'];
              $idAutor = $_SESSION['uzivatel']['ID_UZIVATEL'];
              $autori = $_POST['autori']; 
              $text = $_POST['popis'];
              if (strlen($target_file) > 4) $soubor = $target_file;
              else $soubor = $contribution['soubor'];
              if (isset($_GET['id'])) {
                if ($_SESSION['contribution']['update'] == true) {
                  unlink($contribution['soubor']);
                  move_uploaded_file($_FILES['pdf']['tmp_name'], $target_file);                  
                  Contribution::updateContribution($nazev, $idAutor, $autori, $text, $soubor, $_GET['id']);  
                }
                             
              } 
              else {
                Contribution::insertContribution($idContribution, $nazev, $idAutor, $autori, $text, $soubor);
                move_uploaded_file($_FILES['pdf']['tmp_name'], $target_file);
              }
              header("Location: index.php?page=konf");
            }
          }
        } 
      }   
    }
}


/* //KONTROLA VELIKOSTI SOUBORU - POKUD BUDE TŘEBA
$size = (1024*1024)*50; //50 MB
if ($_FILES["fileToUpload"]["size"] > $size) {
    $_SESSION['error'] = "Soubor je moc velký (max 50 MB) !!";
}
*/