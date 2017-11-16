<?php

/**
 * Model pro ohodnocování
 */
class Evaluation {
  
  /**
   * Vrací všechny hodnoty originality
   */
  public static function getOriginalita() {
    return database::allValuesOriginalita();
  }
  
  /**
   * Vrací všechny hodnoty tématu
   */
  public static function getTema() {
    return database::allValuesTema();
  }
  
  /**
   * Vrací všechny hodnoty technický kvality
   */
  public static function getTechnicka() {
    return database::allValuesTechnicka();
  }
  
  /**
   * Vrací všechny hodnoty jazykový kvality
   */
  public static function getJazykova() {
    return database::allValuesJazykova();
  }
  
  /**
   * Vrací všechny hodnoty doporučení
   */
  public static function getDoporuceni() {
    return database::allValuesDoporuceni();
  }
  
  /**
   * Vrací všechna hodnocení příspěvku podle jeho idčka
   */
  public static function getEvaluation($IDC) {
    return database::getEvaluationEvaluation($IDC);
  }
  
  /**
   * Úprava hodnocení příspěvku
   */
  public static function updateEvaluation($id, $originalita, $tema, $technicka, $doporuceni, $jazykova, $text, $prumer) {
    return database::updateEvaluationEvaluation($id, $originalita, $tema, $technicka, $doporuceni, $jazykova, $text, $prumer);
  } 
}
?>