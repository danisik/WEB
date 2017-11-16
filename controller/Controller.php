<?php
/**
 * Abstraktní třída kontroleru 
 */
abstract class Controller {

	protected $view; 

  /**
   * Funkce pro nainkludování šablony
   */
	public function Display()
	{
		if($this->view != ""){
			require ("view/template/template.phtml");
		}
	}
  
  /**
   * abstraktní funkce, každý kontroler bude vykonávat něco jiného
   */
  abstract function DoIt();

}