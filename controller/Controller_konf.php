<?php
/**
 * Kontrolér pro vytváření účtu
 */
class Controller_konf extends Controller {
    
    /* hodnost uživatele*/
    protected $rank;
    
    /**
     * při vytvoření kontroleru se i automaticky nastaví view, které se má použít
     */    
    public function __construct() {
      $this->view = "konf"; 
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
        
        /**
         * U autora pokud bude chtít vymazat svůj příspěvek
         */
        if (isset($_POST['deleteContribution'])) {
          Konf::deleteEvaluationByIDEvaluation($_POST['deleteContribution']);
        }
        
        /**
         * 3 podmínky, první zajištuje přijetí příspěvků
         * druhá zajištuje nepřijetí příspěvku
         * třetí přiděluje recenzeta příspěvkům
         */
        if (isset($_POST['ButtPrijmout'])) {
          Konf::updateContributionStav($_POST['idPrispevek'], "2");
        }
        
        if (isset($_POST['ButtNeprijmout'])) {
          Konf::updateContributionStav($_POST['idPrispevek'], "3");
        }
        
        if (isset($_POST['ButtPridelit'])) {   
          if (isset($_POST['recenzentiSelect'])) {
            $evaluationExist = Konf::getEvaluationByRecenzentAndContribution($_POST['recenzentiSelect'], $_POST['idPrispevek']);
            if (empty($evaluationExist)) {
              $last = Konf::getLastIDEvaluation();
              $last = array_shift($last);
              $lastID = $last['MAX(ID_HODNOCENI)'] + 1; 
              Konf::insertIntoEvaluationsForEvaluation($lastID, $_POST['idPrispevek'], $_POST['recenzentiSelect']);
            }
          }
        } 
    }
    
    
    //--------------------AUTOR--------------------------------
    /** 
     * Vykreslení tabulky u autora
     **/
    if ($this->rank['nazev'] == "Autor") {
      self::createTableAutor();
      
      if (isset($_POST['deleteContribution'])) {
        $row = Konf::getContributionsByID($_SESSION['uzivatel']['ID_UZIVATEL']);
        foreach($row as $r) {
          if ($r['ID_PRISPEVEK'] == $_POST['deleteContribution']) {
            unlink($r['soubor']);
             Konf::deleteEvaluationsByContribution($_POST['deleteContribution']);
            Konf::deleteContribution($_POST['deleteContribution']);
            self::createTableAutor();
          } 
        }
      }
      
    }
    //--------------------KONEC AUTOR--------------------------
    
    
    //--------------------RECENZENT--------------------------------
    /** 
     * Vykreslení tabulky u recenzenta
     **/
    if ($this->rank['nazev'] == "Recenzent") {
      self::createTableRecenzent();
    
    }
    //--------------------KONEC RECENZENT--------------------------
    
    
    //--------------------ADMIN--------------------------------
    /** 
     * Vykreslení tabulky u admina
     **/
    if ($this->rank['nazev'] == "Admin" || $this->rank['nazev'] == "HAdmin") {
      $recenzenti = Konf::getAllRecenzenty();
      
      $_SESSION['recenzenti'] = self::createSelectBoxRecenzenti($recenzenti, null);
    
    
      if ( (isset($_GET['stav']) && $_GET['stav'] == 2) || (isset($_GET['stav']) && $_GET['stav'] == 3) ) { self::createTableAdmin();} 
      else {self::createTableAdmin1();}
    
    }
    //--------------------KONEC ADMIN--------------------------
    
        //--------------------NEPŘIHLÁŠEN--------------------------------
    /** 
     * Vykreslení tabulky u neprihlasenyho
     **/
    if ($_SESSION['prihlasen'] == false) {
      self::createTableNeprihlasen();    
    }
    //--------------------KONEC NEPŘIHLÁŠEN--------------------------
    
  }
  
  /** 
   * Vytvoří tabulku pro autora
   */
  public function createTableAutor() {
    $row = Konf::getContributionsByID($_SESSION['uzivatel']['ID_UZIVATEL']);
    
    $res = "";
    foreach($row as $r) {
      $evaluations = Evaluation::getEvaluation($r['ID_PRISPEVEK']);
      $evaluationsValue = array_column($evaluations, 'hodnoceni');
      $contributionStav = Konf::getNameOfStav($r['stav']);
      $contributionStav = array_shift($contributionStav);
      $res .= "<tr>";
      $res .= "<td><a href='index.php?page=contribution&id=$r[ID_PRISPEVEK]'><label class='labelZpet'>$r[nazev]</label></a></td>";
      $res .= "<td>$r[autori]</td>";                          
      $res .= "<td style='width: 55px; text-align: center;'>"; 
      if (isset($evaluationsValue) && count($evaluationsValue) > 0) { $res .= $evaluationsValue[0]; }
      $res .= "</td>";
      $res .= "<td style='width: 55px; text-align: center;'>"; 
      if (isset($evaluationsValue) && count($evaluationsValue) > 1) { $res .= $evaluationsValue[1]; }
      $res .= "</td>";
      $res .= "<td style='width: 55px; text-align: center;'>"; 
      if (isset($evaluationsValue) && count($evaluationsValue) > 2) { $res .= $evaluationsValue[2]; }
      $res .= "</td>";
      $res .= "<td>$contributionStav[stav]</td>";
      $res .="<td><div class='xButton'><input type='submit' name='deleteContribution' value='$r[ID_PRISPEVEK]'></div></td>"; 
      $res .= "</tr>";
    }
    $_SESSION['autor'] = $res;
  }
  
  /** 
   * Vytvoří tabulku pro recenzenta
   */
  public function createTableRecenzent() {
    $row = Konf::getContributionsForEvaluation($_SESSION['uzivatel']['ID_UZIVATEL']); 
    $res = "";
    foreach($row as $r) {
      $contribution_eva = Konf::getContributionByContributionID($r['Prispevek_ID_PRISPEVEK']);
      $contribution_eva = array_shift($contribution_eva);
            
      $contributionStav = Konf::getNameOfStav($contribution_eva['stav']);
      $contributionStav = array_shift($contributionStav);      
      $res .= "<tr>";
      $res .= "<td><a href='index.php?page=evaluation&id=$r[ID_HODNOCENI]&IDC=$contribution_eva[ID_PRISPEVEK]'><label class='labelZpet'>$contribution_eva[nazev]</label></a></td>";
      $res .= "<td>$r[hodnoceni]</td>";
      $res .= "<td>$contributionStav[stav]</td>"; 
      $res .= "</tr>";
    }
    $_SESSION['recenzent'] = $res;
  }
  
  /**
   * Vytvoří tabulku pro admina (příspěvky, které jsou ještě ve stavu čekám)
   */
  public function createTableAdmin1() {
    $contribution = Konf::getAllContributions();
    
    $recenzentiID = Konf::getAllRecenzenty();
    $recenzentiID = array_column($recenzentiID, 'ID_UZIVATEL');
    
    $res = "";
    $i = 0;
    $h = 0;
    
    foreach($contribution as $c) {
      $res .= "<tr>";
      $res .= "<td rowspan='3'><a href='index.php?page=contribution&id=$c[ID_PRISPEVEK]'><input type='hidden' name='idPrispevku' value='$c[ID_PRISPEVEK]'><label class='cursorPointer'>$c[nazev]</label></a></td>";
      $res .= "<td rowspan='3'>$c[autori]</td>";
      $evaluations = Evaluation::getEvaluation($c['ID_PRISPEVEK']);
      $evaluationsValue = array_column($evaluations, 'hodnoceni');
      $evaluationsID = array_column($evaluations, 'Recenzent_ID');
      for ($i = 0; $i < 3; $i++) {    
        if (!empty($evaluationsID[$i])) {
          $evaluation = Konf::getEvaluationByRecenzentAndContribution($evaluationsID[$i], $c['ID_PRISPEVEK']);
          $evaluation = array_shift($evaluation);
          $recenzentName = Admin::getUserByID($evaluationsID[$i]);
          $recenzentName = array_shift($recenzentName);
          $res .= "<td>$recenzentName[jmeno]</td>";
          $res .= "<td>$evaluation[Originalita_ID_ORIGINALITA]</td>";
          $res .= "<td>$evaluation[Tema_ID_TEMA]</td>";
          $res .= "<td>$evaluation[Technicka_kvalita_ID_TECHNICKA_KVALITA]</td>";
          $res .= "<td>$evaluation[Jazykova_kvalita_ID_JAZYKOVA_KVALITA]</td>";
          $res .= "<td>$evaluation[Doporuceni_ID_DOPORUCENI]</td>";
          $res .= "<td>$evaluation[hodnoceni]</td>";
          $res .= "<td style='width: 55px; '><form method='post'><div class='xButton'><input type='submit' name='deleteContribution' value='$evaluation[ID_HODNOCENI]'></div></form></td>";
        }
        else {
          $res .= "<form method='post'><td><select name='recenzentiSelect' class='form-control btn-primary BlueBackground WhiteText maxWidthTDAdmin'>". $_SESSION['recenzenti']."</select></td>";
          $res .= "<input type='hidden' name='idPrispevek' value='$c[ID_PRISPEVEK]'>";
          $res .= "<td colspan='7' style='text-align: center;'><button type='submit' class='BlueText' name='ButtPridelit' style='cursor: pointer; font-size: 18px; padding: 0; border: none; background: none;'>Přidělit k recenzi</button></form></td>";
        }
          if ($i == 0) $res .= "<td rowspan='3'> <form method='post'><input type='hidden' name='idPrispevek' value='$c[ID_PRISPEVEK]'><button type='submit' class='BlueText' name='ButtPrijmout' style='cursor: pointer; font-size: 14px; padding: 0; border: none; background: none;'>Přijmuto</button>
           / <button type='submit' class='BlueText' name='ButtNeprijmout' style='cursor: pointer; font-size: 14px; padding: 0; border: none; background: none;'>Nepřijmuto</button></form></td>";
          $res .= "</tr>"; 
      } 
      $h = $h + 1;
    }
          $_SESSION['countContributions'] = $h;
          $_SESSION['admin'] = $res;  
  } 
  
  /**
   * Vytvoří select box u admina pro výběr recenzentů k hodnocení příspěvků
   */
  public function createSelectBoxRecenzenti($recenzenti,$selected){
    
    $res = '<option disabled selected>Vyberte recenzenta</option>';
    foreach($recenzenti as $r){
        if($selected!=null && $selected==$r['ID_UZIVATEL']){ 
            $res .= "<option id= '".$r['ID_UZIVATEL']."' value='".$r['ID_UZIVATEL']."' selected>$r[jmeno]</option>";    
        } else { 
            $res .= "<option id= '".$r['ID_UZIVATEL']."' value='".$r['ID_UZIVATEL']."'>$r[jmeno]</option>";    
        }        
    }
    return $res;
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
  
  /**
   * Vytvoří tabulku pro admina (příspěvky, které už jsou přijaté/nepřijaté
   */
  public function createTableAdmin() {
    $contribution = Konf::getContributionsByStav($_GET['stav']);
    
    $res = "";
    $i = 0;
    $h = 0;
    
    foreach($contribution as $c) {
      $res .= "<tr>";
      $res .= "<td rowspan='3'><a href='index.php?page=contribution&id=$c[ID_PRISPEVEK]'><input type='hidden' name='idPrispevku' value='$c[ID_PRISPEVEK]'><label class='cursorPointer'>$c[nazev]</label></a></td>";
      $res .= "<td rowspan='3'>$c[autori]</td>";
      $evaluations = Evaluation::getEvaluation($c['ID_PRISPEVEK']);
      $evaluationsValue = array_column($evaluations, 'hodnoceni');
      $evaluationsID = array_column($evaluations, 'Recenzent_ID');
      for ($i = 0; $i < 3; $i++) {    
        if (!empty($evaluationsID[$i])) {
          $evaluation = Konf::getEvaluationByRecenzentAndContribution($evaluationsID[$i], $c['ID_PRISPEVEK']);
          $evaluation = array_shift($evaluation);
          $recenzentName = Admin::getUserByID($evaluationsID[$i]);
          $recenzentName = array_shift($recenzentName);
          $res .= "<td>$recenzentName[jmeno]</td>";
          $res .= "<td>$evaluation[Originalita_ID_ORIGINALITA]</td>";
          $res .= "<td>$evaluation[Tema_ID_TEMA]</td>";
          $res .= "<td>$evaluation[Technicka_kvalita_ID_TECHNICKA_KVALITA]</td>";
          $res .= "<td>$evaluation[Jazykova_kvalita_ID_JAZYKOVA_KVALITA]</td>";
          $res .= "<td>$evaluation[Doporuceni_ID_DOPORUCENI]</td>";
          $res .= "<td>$evaluation[hodnoceni]</td>";
        }
          $res .= "</tr>"; 
      } 
      $h = $h + 1;
    }
          $_SESSION['countContributions'] = $h;
          $_SESSION['admin'] = $res;  
  } 
  
}