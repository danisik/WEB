<?php

/**
 * Kontrolér pro manipulaci s příspěvkem
 */
class Controller_evaluation extends Controller {
    
    /* hodnost uživatele*/
    protected $rank;

    /**
     * při vytvoření kontroleru se i automaticky nastaví view, které se má použít
     */
    public function __construct() {
      $this->view = "evaluation"; 
    }
   
    /** 
     * Vybírá hodnocení z databáze a předvyplní údaje do textboxů/selectů
     * Při stisku tlačítka kontroluje zda jsou zadány všechny povinné údaje a poté pouze upravuje hodnocení, vytváření hodnocení je v konferenci u admina
     */ 
    public function DoIt() {
    
    if (isset($_SESSION['uzivatel'])) {
        if (!empty($_SESSION['uzivatel'])) {
        $this -> rank = Konf::getRank($_SESSION['uzivatel']['Hodnost_ID_HODNOST']);
        $this -> rank = array_shift($this -> rank);
        }
        if (isset($_POST['ButtLogout'])) {
          Konf::logout();
        }
        if (isset($_GET['id']))$tmp_eva = Konf::getEvaluationByRecenzentAndContribution($_SESSION['uzivatel']['ID_UZIVATEL'], $_GET['IDC']);
        if (!empty($tmp_eva)) $_SESSION['accessEvaluation'] = true;
        else $_SESSION['accessEvaluation'] = false;
        
        if (isset($_GET['id'])) {       
          $evaluation = Konf::getEvaluationByRecenzentAndContribution($_SESSION['uzivatel']['ID_UZIVATEL'], $_GET['IDC']);  
          $evaluation = array_shift($evaluation);
          
          $contribution = Konf::getContributionByContributionID($_GET['IDC']);
          $contribution = array_shift($contribution);
          $_SESSION['evaluation'] = $evaluation;
          $_SESSION['contribution'] = $contribution;
          
          if($evaluation['text'] = "") $_SESSION['evaluation']['text'] = "Poznámky:";
          
          
          if (isset($evaluation['Originalita_ID_ORIGINALITA'])) {
            $selectOriginal = $evaluation['Originalita_ID_ORIGINALITA'];
            $selectTema = $evaluation['Tema_ID_TEMA'];
            $selectTechnicka = $evaluation['Technicka_kvalita_ID_TECHNICKA_KVALITA'];
            $selectJazykova = $evaluation['Jazykova_kvalita_ID_JAZYKOVA_KVALITA'];
            $selectDoporuceni = $evaluation['Doporuceni_ID_DOPORUCENI'];
            
            $_SESSION['selectBox']['originalita']  = self::createSelectBoxOriginalita(Evaluation::getOriginalita(), $selectOriginal);
            $_SESSION['selectBox']['tema']  = self::createSelectBoxTema(Evaluation::getTema(), $selectTema);
            $_SESSION['selectBox']['technicka']  = self::createSelectBoxTechnicka(Evaluation::getTechnicka(), $selectTechnicka);
            $_SESSION['selectBox']['jazykova']  = self::createSelectBoxJazykova(Evaluation::getJazykova(), $selectJazykova);
            $_SESSION['selectBox']['doporuceni']  = self::createSelectBoxDoporuceni(Evaluation::getDoporuceni(), $selectDoporuceni);
          }
          else {
            $_SESSION['selectBox']['originalita']  = self::createSelectBoxOriginalita(Evaluation::getOriginalita(), NULL);
            $_SESSION['selectBox']['tema']  = self::createSelectBoxTema(Evaluation::getTema(), NULL);
            $_SESSION['selectBox']['technicka']  = self::createSelectBoxTechnicka(Evaluation::getTechnicka(), NULL);
            $_SESSION['selectBox']['jazykova']  = self::createSelectBoxJazykova(Evaluation::getJazykova(), NULL);
            $_SESSION['selectBox']['doporuceni']  = self::createSelectBoxDoporuceni(Evaluation::getDoporuceni(), NULL);
          } 
        }                          
        else {
          $_SESSION['selectBox']['originalita']  = "";
          $_SESSION['selectBox']['tema']  = "";
          $_SESSION['selectBox']['technicka']  = "";
          $_SESSION['selectBox']['jazykova']  = "";
          $_SESSION['selectBox']['doporuceni']  = "";
          
          $_SESSION['evaluation']['Recenzent_ID'] = "";
        }
        
        if (isset($_POST['ButtEvaluation'])) {
          if ((!isset($_POST['originalitaSelect'])) || (!isset($_POST['temaSelect'])) ||
          (!isset($_POST['technickaSelect'])) || (!isset($_POST['jazykovaSelect'])) || (!isset($_POST['doporuceniSelect']))) {
            $_SESSION['error'] = "Vyplňte prosím všechna pole!!";
          }
          else {
            $originalita = $_POST['originalitaSelect'];                            
            $tema = $_POST['temaSelect'];
            $technicka = $_POST['technickaSelect'];
            $jazykova = $_POST['jazykovaSelect'];
            $doporuceni = $_POST['doporuceniSelect'];
            $poznamky = $_POST['poznamky'];
            $prumer = ($originalita + $tema + $technicka + $jazykova + $doporuceni) / 5;
            Evaluation::updateEvaluation($_GET['id'], $originalita, $tema, $technicka, $doporuceni, $jazykova, $poznamky, $prumer);
            
            header("Location: index.php?page=konf");
          
        } 
    }   
  }
}

/** 
 * Vytvoří a select box s originalitou
 */
public function createSelectBoxOriginalita($originalita, $selected){
    $res = '<option value="" disabled selected>Vyberte originalitu</option>';
    foreach($originalita as $r){
        if($selected!=null && $selected==$r['ID_ORIGINALITA']){ 
          $res .= "<option id= '".$r['ID_ORIGINALITA']."' value='".$r['ID_ORIGINALITA']."' selected>$r[ID_ORIGINALITA] - $r[nazev]</option>";    
        } else {
          $res .= "<option id= '".$r['ID_ORIGINALITA']."' value='".$r['ID_ORIGINALITA']."'>$r[ID_ORIGINALITA] - $r[nazev]</option>";           
        }
      }
    return $res;
  }

/** 
 * Vytvoří a select box s tématama
 */  
public function createSelectBoxTema($tema, $selected){
    $res = '<option value="" disabled selected>Vyberte Téma</option>';
    foreach($tema as $r){
        if($selected!=null && $selected==$r['ID_TEMA']){ 
          $res .= "<option id= '".$r['ID_TEMA']."' value='".$r['ID_TEMA']."' selected>$r[ID_TEMA] - $r[nazev]</option>";    
        } else {
          $res .= "<option id= '".$r['ID_TEMA']."' value='".$r['ID_TEMA']."'>$r[ID_TEMA] - $r[nazev]</option>";           
        }
      }
    return $res;
  }

/** 
 * Vytvoří a select box s technickou kvalitou
 */  
public function createSelectBoxTechnicka($technicka, $selected){
    $res = '<option value="" disabled selected>Vyberte technickou kvalitu</option>';
    foreach($technicka as $r){
        if($selected!=null && $selected==$r['ID_TECHNICKA_KVALITA']){ 
          $res .= "<option id= '".$r['ID_TECHNICKA_KVALITA']."' value='".$r['ID_TECHNICKA_KVALITA']."' selected>$r[ID_TECHNICKA_KVALITA] - $r[nazev]</option>";    
        } else {
          $res .= "<option id= '".$r['ID_TECHNICKA_KVALITA']."' value='".$r['ID_TECHNICKA_KVALITA']."'>$r[ID_TECHNICKA_KVALITA] - $r[nazev]</option>";           
        }
      }
    return $res;
  }

/** 
* Vytvoří a select box s jazykovou kvalitou
*/
public function createSelectBoxJazykova($jazykova, $selected){
    $res = '<option value="" disabled selected>Vyberte jazykovou kvalitu</option>';
    foreach($jazykova as $r){
        if($selected!=null && $selected==$r['ID_JAZYKOVA_KVALITA']){ 
          $res .= "<option id= '".$r['ID_JAZYKOVA_KVALITA']."' value='".$r['ID_JAZYKOVA_KVALITA']."' selected>$r[ID_JAZYKOVA_KVALITA] - $r[nazev]</option>";    
        } else {
          $res .= "<option id= '".$r['ID_JAZYKOVA_KVALITA']."' value='".$r['ID_JAZYKOVA_KVALITA']."'>$r[ID_JAZYKOVA_KVALITA] - $r[nazev]</option>";           
        }
      }
    return $res;
  }

/** 
 * Vytvoří a select box s doporučením
 */  
public function createSelectBoxDoporuceni($doporuceni, $selected){
    $res = '<option value="" disabled selected>Vyberte doporučení</option>';
    foreach($doporuceni as $r){
        if($selected!=null && $selected==$r['ID_DOPORUCENI']){ 
          $res .= "<option id= '".$r['ID_DOPORUCENI']."' value='".$r['ID_DOPORUCENI']."' selected>$r[ID_DOPORUCENI] - $r[nazev]</option>";    
        } else {
          $res .= "<option id= '".$r['ID_DOPORUCENI']."' value='".$r['ID_DOPORUCENI']."'>$r[ID_DOPORUCENI] - $r[nazev]</option>";           
        }
      }
    return $res;
  }



  
}