<?php

/**
 * Model pro vytváření/úpravu příspěvků
 */
class Contribution {

  /**
   * Zjistí maximální ID, které se teď nachází v tabulce příspěvek
   */
  public static function getLastIDContribution() {                                        
    return Database::lastIDContribution();
  }
  
  /**
   * Vytvoření příspěvku
   */
  public static function insertContribution($id, $nazev, $idAutor, $autori, $text, $soubor) {
    return database::insertIntoContribution($id, $nazev, $idAutor, $autori, $text, $soubor);
  }
  
  /**
   * Vrací příspěvek podle jeho idčka
   */
  public static function getContribution($id) {
    return database::getContributionContribution($id);
  }
  
  /**
   * Úprava příspěvku podle id příspěvku
   */
  public static function updateContribution($nazev, $idAutor, $autori, $text, $soubor, $idPrispevek) {
    return database::updateContributionContribution($nazev, $idAutor, $autori, $text, $soubor, $idPrispevek);
  }
  
  
  
}
?>