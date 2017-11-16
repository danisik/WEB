<?php
/**
 * Kontrolér pro hlavní stránku
 */
class Controller_main extends Controller {

    /* nadpis textu*/
    protected $b_title;
    /* titulek strany*/
    protected $h_title;
    /* text*/
    protected $text;
    
    /**
     * při vytvoření kontroleru se i automaticky nastaví view, které se má použít
     */    
    public function __construct() {
      $this->view = "main";
    }
    
    /**
     * podle infa z url vybírá texty z databáze a vypisuje je
     */
    public function DoIt() {
      if (isset($_GET['info'])) {
        $row = Main::getValuesMain($_GET['info']);
      }
      else {
        $row = Main::getValuesMain("main");
      }
      $row = array_shift($row);
      $this->h_title = $row['h_titles'];
      $this->b_title = $row['b_titles'];
      $this->text = $row['text'];    
    }
}