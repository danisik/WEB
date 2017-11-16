<?php

/**
 * Model pro admin panel
 */
class Admin {
  
  /**
   * Vrátí nám uživatele a jeho hodnoty podle jeho IDčka 
   */
  public static function getUserByID($id) {
    return database::getUsersIDAdmin($id);
  }
  
  /**
   * Upravujeme hodnoty uživatele podle zadaných údajů
   */
  public static function updateInfo($heslo, $jmeno, $email, $organizace, $hodnost, $uzivatel_jmeno, $stav) {
    return database::updateAdmin($heslo, $jmeno, $email, $organizace, $hodnost, $uzivatel_jmeno, $stav);
  } 
  
  /**
   * Vrací nám všechny hodnoty hodností
   */
  public function getValues() {
    return database::allValuesValues();
  }
  
  /**
   * Vrací nám všechny uživatele
   */
  public function getUsers() {
    return database::allValuesUsers();
  }
  
    /**
   * Vrací nám všechny stavy
   */
  public function getStav() {
    return database::allValuesStav();
  }
}
?>