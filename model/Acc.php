<?php

/**
 * Model pro vytvoření účtu
 */
class Acc {

  /**
   * funkce pro vytvoření účtu
   */
  public static function insertIntoDBAcc($id, $uzivatel_jmeno, $heslo, $jmeno, $email, $organizace, $hodnost) {
    return database::insertIntoAcc($id, $uzivatel_jmeno, $heslo, $jmeno, $email, $organizace, $hodnost);
  }
  
  /**
   * Získání posledního ID uživatele
   */
  public static function getLastIDUserAcc() {
    return Database::lastIDAcc();
  }
  
  /**
   * Slouží pro testování, zda v databázi existuje toto uživatelské jméno, pokud ne, lze toto uživatelské jméno použít
   */
  public static function getNameAcc($name) {
    return Database::returnAllNameAcc($name);
  }
  
  /**
   * Slouží pro testování, zda v databázi existuje tento, pokud ne, lze tento email použít
   */
  public static function getEmailAcc($email) {
    return Database::returnAllEmailAcc($email);
  }
}
?>