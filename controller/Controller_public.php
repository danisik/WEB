<?php
/**
 * Kontrolér pro vytváření účtu
 */
class Controller_public extends Controller {
    
    /* hodnost uživatele*/
    protected $rank;
    
    /**
     * při vytvoření kontroleru se i automaticky nastaví view, které se má použít
     */    
    public function __construct() {
      $this->view = "public"; 
    }
    
    /**
     * Vykreslení tabulek podle hodnosti uživatele
     * Možnost odlognutí 
     */
    public function DoIt() {
    
    if (isset($_SESSION['uzivatel'])) {
        if (!empty($_SESSION['uzivatel'])) {
          $this -> rank = Konf::getRank($_SESSION['uzivatel']['Hodnost_ID_HODNOST']);
          $this -> rank = array_shift($this -> rank);
          $_SESSION['update'] = false;
        }
        if (isset($_POST['ButtLogout'])) {
          Konf::logout();
        }
      }
      self::createTableNeprihlasen();
    }
  
  /** 
   * Vytvoří tabulku pro neprihlasenyho
   */
  public function createTableNeprihlasen() {
    $row = Konf::getContributionsByStav(2); 
    $res = "";
    foreach($row as $r) {
      $res .= "<tr>";
      $res .= "<td><a href='index.php?page=contribution&id=$r[ID_PRISPEVEK]'><label class='labelZpet'>$r[nazev]</label></a></td>";
      $res .= "<td>$r[autori]</td>";                           
      $res .= "</tr>";
    }
    $_SESSION['neprihlasen'] = $res;
  }   
}