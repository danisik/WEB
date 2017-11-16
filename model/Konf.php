<?php

/**
 * Model pro zobrazování příspěvků podle ranků
 */
class Konf {
  
  /**
   * Vrací nám údaje o hodnosti podle id hodnosti, slouží pro výpis názvu hodnosti
   */
  public static function getRank($id) {
    return database::getRankDB($id);
  }
  
  /**
   * Vykoná odhlášení - změní pár sessionů, unsetuje uživatele
   */
  public function logout() {
    unset($_SESSION['uzivatel']);
    unset($_SESSION['contribution']);
    $_SESSION['prihlasen'] = false;
    header('Location: index.php?page=login');
  }
  
  /**
   * Vrací všechny údaje o příspěvku(příspevkách) uživatele
   */
  public function getContributionsByID($id) {
    return database::getContributionKonf($id);
  }
  
  /**
   * Vymažeme příspěvek, podle jeho IDčka
   */
  public function deleteContribution($id) {
    return database::deleteContributionKonf($id);
  }
  
  /**
   * Vymaže hodnocení příspěvku
   */
  public function deleteEvaluationsByContribution($id) {
    return database::deleteEvaluationsByIDContribution($id);
  }
  
  /**
   * Vrací všechny příspěvky, které recenzent ohodnotil
   */
  public function getContributionsForEvaluation($id) {
    return database::getContributionsEvaluationKonf($id);
  }
  
  /**
   * Vrací všechny hodnoty příspěvku podle ID příspěvku
   */
  public function getContributionByContributionID($id) {
    return database::getContributionsByContributionKonf($id);
  
  }
  
  /**
   * Vybere všechny příspěvky, které ještě nebyly přijaty/nepřijaty
   */
  public function getAllContributions() {
    return database::getAllContributionsKonf();
  }
  
  /**
   * Vybere všechny recenzenty z tabulky uživatelů
   */
  public function getAllRecenzenty() {
    return database::getAllRecenzentyKonf();
  }
  
  /**
   * Vybere dané hodnocení, kde recenzent ohodnotil daný příspěvek
   */
  public function getEvaluationByRecenzentAndContribution($idUzivatel, $idPrispevek) {
    return database::getAllEvaluationsKonf($idUzivatel, $idPrispevek);
  }
  
  /**
   * Vytvoří základní hodnocení - když přidáváme recenzenta pomocí admina
   */
  public function insertIntoEvaluationsForEvaluation($idHodnoceni, $idPrispevek, $idRecenzent) {
    return database::insertIntoEvaluationsForEvaluationKonf($idHodnoceni, $idPrispevek, $idRecenzent);
  }
  
  /**
   * Vybere maximální ID z hodnocení
   */
  public function getLastIDEvaluation() {
    return database::lastIdEvaluation();
  }
  
  /**
   * Vymaže hodnocení podle jeho IDčka
   */
  public function deleteEvaluationByIDEvaluation($id) {
    return database::deleteEvaluationsByID($id);
  }
  
  /**
   * Úprava příspěvku, kde nastavujeme jeho stav - (0 - přijat, 1 - čekám, 2 - nepřijat)
   */
  public function updateContributionStav($id, $stav) {
    return database::updateContributionStavKonf($id, $stav);
  }
  
  /**
   * Vybere všechny příspěvky, které jsou v zadaném stavu
   */
  public function getContributionsByStav($stav) {
    return database::getContributionsByStavKonf($stav);
  }
  
  /**
   * Získá název stavu příspěvku
   */
  public function getNameOfStav($stav) {
    return database::nameOfStav($stav);
  }
  
}
?>