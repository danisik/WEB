<?php

/**
 * Model pro hlavní stránku
 */
class Main {

 /**
  * Vrací titulek stránky, titulky textů a obsah textů na hlavní stránce - informace o konferenci atp.
  */
  public static function getValuesMain($info) {
    return database::returnAllMain($info);
  }
}
?>