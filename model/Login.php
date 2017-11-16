<?php

/**
 * Model pro přihlašování
 */
class Login {

  /**
   * Vrací všechny údaje o uživateli podle uživatelského jména
   */
  public static function getUserLogin($name) {
    return database::returnAllLogin($name);
  }
}
?>