<?php 

/**
 * Třída obsahující funkce komunikující výhradně s databází
 */
class database {

    private static $conn;
    
    /**
     * Při vytváření konstruktoru vytvoříme PDO objekt
     */
    public function __construct () {  
    $servername = "localhost";  
    $dbname = "webka"; 
    $username = "root";
    $password = "";
      try {
       self::$conn = new pdo('mysql:host='.$servername.';dbname='.$dbname.';charset=utf8', $username, $password);    
       self::$conn-> setattribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      } 
      catch(pdoexception $e) {
       echo "error: " . $e->getmessage();
      }  
               
    }
    
    /**
     * Vrací titulek stránky, titulky textů a obsah textů na hlavní stránce - informace o konferenci atp.
     * $info - podle stránky vybíráme texty z databáze
     */
    public static function returnAllMain($info) {
      $query = "select h_titles, b_titles, text from texts where info = :info"; 
    	$data = self::$conn->prepare($query);
      
      $info = htmlspecialchars($info);  
      
      $data->bindparam(':info', $info);
    	$data->execute();
      return $data->fetchall();
    }
    
    /**
     * Vrací všechny údaje o uživateli podle uživatelského jména
     * $name uživatelské jméno
     */
    public static function returnAllLogin($name) {
      $query = "select * from uzivatel where uzivatel_jmeno = :jmeno"; 
    	$data = self::$conn->prepare($query);
      
      $name = htmlspecialchars($name);
      
      $data->bindparam(':jmeno', $name);
    	$data->execute();
      return $data->fetchall();
    }
    
   /**
     * Slouží pro testování, zda v databázi existuje toto uživatelské jméno, pokud ne, lze toto uživatelské jméno použít
     * $name testované uživatelské jméno
     */
    public static function returnAllNameAcc($name) {
      $query = "select uzivatel_jmeno from uzivatel where uzivatel_jmeno = :jmeno"; 
    	$data = self::$conn->prepare($query);
      
      $name = htmlspecialchars($name);
      
      $data->bindparam(':jmeno', $name);
    	$data->execute();
      return $data->fetchall();
    }
    
    /**
     * Slouží pro testování, zda v databázi existuje tento, pokud ne, lze tento email použít
     * $name testovaný email
     */
    public static function returnAllEmailAcc($email) {
      $query = "select email from uzivatel where email = :email"; 
    	$data = self::$conn->prepare($query);
      
      $email = htmlspecialchars($email);
      
      $data->bindparam(':email', $email);
    	$data->execute();
      return $data->fetchall();
    }
    
    /**
     * Vrací nám maximální ID v tabulce uživatel
     */
    public static function lastIdAcc() {
      $query = "select MAX(ID_UZIVATEL) from uzivatel";
      $id = self::$conn->prepare($query);  
      $id->execute();
      return $id -> fetchall();
    }
    
    /**
     * Vytváříme nového uživatele
     * $id - ID uživatele, které dostane
     * $uzivatel_jmeno - uživatelské jméno
     * $heslo - heslo zašifrovaně
     * $jmeno - jeho rodné jméno a příjmení
     * $email - jeho email
     * $organizace - v jaké organizaci pracuje
     * $hodnost - při vytváření účtu defaultně nastavena na 3 (Autor)
     */
    public static function insertIntoAcc($id, $uzivatel_jmeno, $heslo, $jmeno, $email, $organizace, $hodnost) {
      $query = "insert into uzivatel(id_uzivatel, uzivatel_jmeno, heslo, jmeno, email, organizace, Hodnost_ID_HODNOST, stav) 
    values (:id,:uzivatel,:heslo,:jmeno,:email,:organizace,:hodnost, 1)";
      $data = self::$conn->prepare($query);
      
      $id = htmlspecialchars($id);
      $uzivatel_jmeno = htmlspecialchars($uzivatel_jmeno);
      $heslo = htmlspecialchars($heslo);
      $jmeno = htmlspecialchars($jmeno);
      $email = htmlspecialchars($email);
      $organizace = htmlspecialchars($organizace);
      $hodnost = htmlspecialchars($hodnost);
      
      $data->bindparam(':id', $id);
      $data->bindparam(':uzivatel', $uzivatel_jmeno);
      $data->bindparam(':heslo', $heslo);
      $data->bindparam(':jmeno', $jmeno);
      $data->bindparam(':email', $email);
      $data->bindparam(':organizace', $organizace); 
      $data->bindparam(':hodnost', $hodnost);
      $data->execute();
    }
    
    /**
     * Vrací nám údaje o hodnosti podle id hodnosti, slouží pro výpis názvu hodnosti 
     * $hodnost - zadaná id hodnosti
     */
    public static function getRankDB($hodnost) {
      $query = "select * from hodnost where ID_HODNOST = :hodnost";
      $data = self::$conn->prepare($query);
      
      $hodnost = htmlspecialchars($hodnost);
      
      $data->bindparam(':hodnost', $hodnost);  
      $data->execute();
      return $data -> fetchall();
    }
    
    /**
     * Vrací všechny údaje o příspěvku(příspevkách) uživatele
     * $id - id uživatele, pro vytřídění 
     */
    public static function getContributionKonf($id) {
      $query = "select * from prispevek where Uzivatel_ID_UZIVATEL = :id";
      $data = self::$conn->prepare($query);
      
      $id = htmlspecialchars($id);
      
      $data->bindparam(':id', $id);  
      $data->execute();
      return $data -> fetchall();
    }
    
    /**
     * Vymažeme příspěvek, podle jeho IDčka
     * $id - id příspěvku k vymazání
     */
    public static function deleteContributionKonf($id) {
      $query = "delete from prispevek where ID_PRISPEVEK = :id";
      $data = self::$conn->prepare($query);
      
      $id = htmlspecialchars($id);
      
      $data->bindparam(':id', $id);  
      $data->execute();
    }
    
    /**
     * Vrací všechny hodnosti (název + id), pro výpis do selectboxu při úpravě hodnosti
     */
    public static function allValuesValues() {
      $query = "select * from hodnost";
      $data = self::$conn->prepare($query);
      $data->execute();
      return $data->fetchall();
    }
    
    /**
     * Vrací všechny uživatele a jejich hodnoty, pro úpravu uživatelů
     */
    public static function allValuesUsers() {
      $query = "select * from uzivatel";
      $data = self::$conn->prepare($query);
      $data->execute();
      return $data->fetchall();
    } 
    
    public static function allValuesStav() {
      $query = "select * from stav";
      $data = self::$conn->prepare($query);
      $data->execute();
      return $data->fetchall();    
    }  
    
    /**
     * Vrátí nám uživatele a jeho hodnoty podle jeho IDčka
     * $id - id uživatele, kterého chceme najít
     */
    public static function getUsersIDAdmin($id) {
      $query = "select * from uzivatel where ID_UZIVATEL = :id";
      $data = self::$conn->prepare($query);
      
      $id = htmlspecialchars($id);
      
      $data->bindparam(':id', $id);  
      $data->execute();
      return $data->fetchall();
    }
    
    /**
     * Upravujeme hodnoty uživatele podle zadaných údajů
     * $heslo - jeho heslo
     * $jmeno - jeho jméno a příjmení
     * $email - jeho email
     * $hodnost - id hodnosti, kterou mu přiřazujeme
     * $uzivatel_jmeno - abychom věděli jakému uživateli máme změnit údaje
     */
    public static function updateAdmin($heslo, $jmeno, $email, $organizace, $hodnost, $uzivatel_jmeno, $stav) {
      $query = "update uzivatel set heslo = :heslo, jmeno = :jmeno, email = :email, organizace = :organizace, Hodnost_ID_HODNOST = :hodnost, stav = :stav where uzivatel_jmeno = :uzivatel";
      $data = self::$conn->prepare($query);
      
      $uzivatel_jmeno = htmlspecialchars($uzivatel_jmeno);
      $heslo = htmlspecialchars($heslo);
      $jmeno = htmlspecialchars($jmeno);
      $email = htmlspecialchars($email);
      $organizace = htmlspecialchars($organizace);
      $hodnost = htmlspecialchars($hodnost);
      
      $data->bindparam(':hodnost', $hodnost);
      $data->bindparam(':uzivatel', $uzivatel_jmeno);
      $data->bindparam(':heslo', $heslo);
      $data->bindparam(':jmeno', $jmeno);
      $data->bindparam(':email', $email);
      $data->bindparam(':organizace', $organizace);
      $data->bindparam(':stav', $stav); 
      $data->execute();
    }
    
    /**
     * Vrací příspěvek podle jeho idčka
     * $id - id pro nalezení příspěvku
     */
    public static function getContributionContribution($id) {
      $query = "select * from prispevek where ID_PRISPEVEK = :id";
      $data = self::$conn->prepare($query);
      
      $id = htmlspecialchars($id);
      
      $data->bindparam(':id', $id);  
      $data->execute();
      return $data -> fetchall();
    }
    
    /**
     * Zjistí maximální ID, které se teď nachází v tabulce příspěvek
     */
    public static function lastIDContribution() {
      $query = "select MAX(ID_PRISPEVEK) from prispevek";
      $id = self::$conn->prepare($query);  
      $id->execute();
      return $id -> fetchall();
    }
    
    /**
     * Vytvoření příspěvku 
     * $id - id příspěvku
     * $nazev - název příspěvku
     * $idautor - autorovo id
     * $autori - kdo všechno je autorem tohoto příspěvku
     * $text - popis/poznámky k příspěvku
     * $soubor - cesta k souboru
     */
    public static function insertIntoContribution($id, $nazev, $idautor, $autori, $text, $soubor) {
      $query = "insert into prispevek (ID_PRISPEVEK, nazev, Uzivatel_ID_UZIVATEl, autori, text, soubor, stav) values (:id, :nazev, :uzivatel, :autori, :text, :soubor, 1)";
      $data = self::$conn->prepare($query);
      
      $id = htmlspecialchars($id);
      $nazev = htmlspecialchars($nazev);
      $idautor = htmlspecialchars($idautor);
      $autori = htmlspecialchars($autori);
      $text = htmlspecialchars($text);
      $soubor = htmlspecialchars($soubor);
      
      $data->bindparam(':id', $id);
      $data->bindparam(':nazev', $nazev);
      $data->bindparam(':uzivatel', $idautor);
      $data->bindparam(':autori', $autori);
      $data->bindparam(':text', $text);
      $data->bindparam(':soubor', $soubor);
      $data->execute();
    }
    
    /**
     * Úprava příspěvku podle id příspěvku
     * $nazev - název příspěvku
     * $idautor - autorovo id
     * $autori - kdo všechno je autorem tohoto příspěvku
     * $text - popis/poznámky k příspěvku
     * $soubor - cesta k souboru
     * $idprispevek - id příspěvku
     */
    public static function updateContributionContribution($nazev, $idautor, $autori, $text, $soubor, $idprispevek) {
      $query = "update prispevek set nazev = :nazev, Uzivatel_ID_UZIVATEl = :uzivatel, autori = :autori, text = :text, soubor = :soubor where ID_PRISPEVEK = :id";
      $data = self::$conn->prepare($query);
      
      $nazev = htmlspecialchars($nazev);
      $idautor = htmlspecialchars($idautor);
      $idprispevek = htmlspecialchars($idprispevek);
      $autori = htmlspecialchars($autori);
      $text = htmlspecialchars($text);
      $soubor = htmlspecialchars($soubor);
      
      $data->bindparam(':nazev', $nazev);
      $data->bindparam(':uzivatel', $idautor);
      $data->bindparam(':autori', $autori);
      $data->bindparam(':text', $text);
      $data->bindparam(':soubor', $soubor); 
      $data->bindparam(':id', $idprispevek);
      $data->execute();
    }
    
    /**
     * Vrací všechny údaje z tabulky originalita - slouží pro hodnocení
     */
    public static function allValuesOriginalita() {
      $query = "select * from originalita";
      $data = self::$conn->prepare($query);
      $data->execute();
      return $data->fetchall();
    }
    
    /**
     * Vrací všechny údaje z tabulky tema - slouží pro hodnocení
     */
    public static function allValuesTema() {
      $query = "select * from tema";
      $data = self::$conn->prepare($query);
      $data->execute();
      return $data->fetchall();
    }
    
    /**
     * Vrací všechny údaje z tabulky technicka_kvalita - slouží pro hodnocení
     */
    public static function allValuesTechnicka() {
      $query = "select * from technicka_kvalita";
      $data = self::$conn->prepare($query);
      $data->execute();
      return $data->fetchall();
    }
    
    /**
     * Vrací všechny údaje z tabulky jazykova_kvalita - slouží pro hodnocení
     */
    public static function allValuesJazykova() {
      $query = "select * from jazykova_kvalita";
      $data = self::$conn->prepare($query);
      $data->execute();
      return $data->fetchall();
    }
    
    /**
     * Vrací všechny údaje z tabulky doporuceni - slouží pro hodnocení
     */
    public static function allValuesDoporuceni() {
      $query = "select * from doporuceni";
      $data = self::$conn->prepare($query);
      $data->execute();
      return $data->fetchall();
    }
    
    /**
     * Vrací všechny příspěvky, které recenzent ohodnotil
     * $id - id recenzenta
     */
    public static function getContributionsEvaluationKonf($id) {
      $query = "select * from hodnoceni where Recenzent_ID = :id";
      $data = self::$conn->prepare($query);
      
      $id = htmlspecialchars($id);
      
      $data->bindparam(':id', $id);  
      $data->execute();
      return $data -> fetchall();
    }
    
    /**
     * Vrací všechny hodnoty příspěvku podle ID příspěvku
     * $id - id příspěvku
     */
    public static function getContributionsByContributionKonf($id) {
      $query = "select * from prispevek where ID_PRISPEVEK = :id";
      $data = self::$conn->prepare($query);
      
      $id = htmlspecialchars($id);
      
      $data->bindparam(':id', $id);  
      $data->execute();
      return $data -> fetchall();
    }
    
    /**
     * Vrací všechna hodnocení příspěvku podle jeho idčka
     * $idc - id příspěvku
     */
    public static function getEvaluationEvaluation($idc) {
      $query = "select * from hodnoceni where Prispevek_ID_PRISPEVEK = :idc";
      $data = self::$conn->prepare($query);
      
      $idc = htmlspecialchars($idc);
      
      $data->bindparam(':idc', $idc);  
      $data->execute();
      return $data -> fetchall();
    }
    
    /**
     * Úprava hodnocení příspěvku
     * $id - id hodnocení
     * $originalita - jak je příspěvek originální
     * $tema - jak je příspěvek tématický
     * $technicka - jak je příspěvek technicky kvalitní
     * $jazykova - jak je příspěvek kvalitní jazykově
     * $text - poznámky
     * $prumer - vypočítaný průměr z hodnocených "hodnot"
     */
    public static function updateEvaluationEvaluation($id, $originalita, $tema, $technicka, $doporuceni, $jazykova, $text, $prumer) {
      $query = "update hodnoceni set Originalita_ID_ORIGINALITA =:originalita,
      Tema_ID_TEMA=:tema, Technicka_kvalita_ID_TECHNICKA_KVALITA=:technicka, Doporuceni_ID_DOPORUCENI=:doporuceni,
      Jazykova_kvalita_ID_JAZYKOVA_KVALITA=:jazykova, text=:text, hodnoceni = :prumer where ID_HODNOCENI = :hodnoceni";
      
      $originalita = htmlspecialchars($originalita);
      $tema = htmlspecialchars($tema);
      $technicka = htmlspecialchars($technicka);
      $doporuceni = htmlspecialchars($doporuceni);
      $jazykova = htmlspecialchars($jazykova);
      $text = htmlspecialchars($text);
      $id = htmlspecialchars($id);
      $prumer = htmlspecialchars($prumer);
      
      $data = self::$conn->prepare($query);
      $data->bindparam(':originalita', $originalita);
      $data->bindparam(':tema', $tema);
      $data->bindparam(':technicka', $technicka);
      $data->bindparam(':doporuceni', $doporuceni);
      $data->bindparam(':jazykova', $jazykova);
      $data->bindparam(':text', $text);
      $data->bindparam(':hodnoceni', $id);
      $data->bindparam(':prumer', $prumer);
      $data->execute();
    }
    
    /**
     * Vybere všechny příspěvky, které ještě nebyly přijaty/nepřijaty
     */
    public static function getAllContributionsKonf() {
      $query = "SELECT * FROM prispevek WHERE stav = '1'";
      $data = self::$conn->prepare($query);
      $data->execute();
      return $data->fetchAll();
    }
    
    /**
     * Vybere všechny recenzenty z tabulky uživatelů
     */
    public static function getAllRecenzentyKonf() {
      $query = "SELECT * FROM uzivatel WHERE Hodnost_ID_HODNOST = '2' && stav = '1'";
      $data = self::$conn->prepare($query);
      $data->execute();
      return $data->fetchAll();
    }
    
    /**
     * Vybere dané hodnocení, kde recenzent ohodnotil daný příspěvek
     * $idUzivatel - id recenzenta
     * $idPrispevek - id hodnoceného příspěvku
     */
    public static function getAllEvaluationsKonf($idUzivatel, $idPrispevek) {
      $query = "SELECT * FROM hodnoceni WHERE Recenzent_ID = :uzivatel && Prispevek_ID_PRISPEVEK = :prispevek";
      $data = self::$conn->prepare($query);
      
      $idUzivatel = htmlspecialchars($idUzivatel);
      $idPrispevek = htmlspecialchars($idPrispevek);
      
      $data->bindparam(':uzivatel', $idUzivatel);
      $data->bindparam(':prispevek', $idPrispevek);
      $data->execute();
      return $data->fetchAll();
    }
    
    /**
     * Vymaže hodnocení příspěvku
     * $id - id příspěvku
     */
    public static function deleteEvaluationsByIDContribution($id) {
      $query = "delete from hodnoceni where Prispevek_ID_PRISPEVEK = :id";
      $data = self::$conn->prepare($query);
      
      $id = htmlspecialchars($id);
      
      $data->bindparam(':id', $id);
      $data->execute();
    }
    
    /**
     * Vytvoří základní hodnocení - když přidáváme recenzenta pomocí admina
     * $idHodnocení - id hodnocení
     * $idPrispevek - id příspěvku, který chceme ohodnotit
     * $idRecenzent - id recenzenta, co má příspěvek ohodnotit
     */
    public static function insertIntoEvaluationsForEvaluationKonf($idHodnoceni, $idPrispevek, $idRecenzent) {
      $query = "INSERT INTO hodnoceni(ID_HODNOCENI, Prispevek_ID_PRISPEVEK, Recenzent_ID, Originalita_ID_ORIGINALITA, Tema_ID_TEMA, Technicka_kvalita_ID_TECHNICKA_KVALITA, Doporuceni_ID_DOPORUCENI, Jazykova_kvalita_ID_JAZYKOVA_KVALITA, text, hodnoceni)
       VALUES (:idHodnoceni,:idPrispevek,:idRecenzent,NULL,NULL,NULL,NULL,NULL,NULL,NULL)";
      $data = self::$conn->prepare($query);
      
      $idHodnoceni = htmlspecialchars($idHodnoceni);
      $idPrispevek = htmlspecialchars($idPrispevek);
      $idRecenzent = htmlspecialchars($idRecenzent);
      
      $data->bindparam(':idHodnoceni', $idHodnoceni);
      $data->bindparam(':idPrispevek', $idPrispevek);
      $data->bindparam(':idRecenzent', $idRecenzent);
      $data->execute(); 
    }
    
    /**
     * Vybere maximální ID z hodnocení
     */
    public static function lastIdEvaluation() {
      $query = "select MAX(ID_HODNOCENI) from hodnoceni";
      $id = self::$conn->prepare($query);  
      $id->execute();
      return $id -> fetchall();
    }
    
    /**
     * Vymaže hodnocení podle jeho IDčka
     * $id - id hodnocení
     */
    public static function deleteEvaluationsByID($id) {
      $query = "delete from hodnoceni where ID_HODNOCENI = :id";
      $data = self::$conn->prepare($query);
      
      $id = htmlspecialchars($id);
      
      $data->bindparam(':id', $id);
      $data->execute();
    }
    
    /**
     * Úprava příspěvku, kde nastavujeme jeho stav - (2 - přijat, 1 - čekám, 3 - nepřijat)
     */
    public static function updateContributionStavKonf($id, $stav) {
      $query = "UPDATE prispevek SET stav = :stav WHERE ID_PRISPEVEK = :id";
      $data = self::$conn->prepare($query);
      
      $id = htmlspecialchars($id);
      $stav = htmlspecialchars($stav);
      
      $data->bindparam(':id', $id);
      $data->bindparam(':stav', $stav);
      $data->execute();
    }
    
    /**
     * Vytáhne všechny příspěvky podle stavu
     */
    public static function getContributionsByStavKonf($stav) {
      $query = "SELECT * FROM prispevek WHERE stav = :stav";
      $data = self::$conn->prepare($query);
      
      $stav = htmlspecialchars($stav);
      
      $data->bindparam(':stav', $stav);
      $data->execute();
      return $data->fetchAll();      
    }
    
    /**
     * Vytáhne nám z databáze název zadaného stavu podle id
     */
    public static function nameOfStav($stav) {
      $query = "SELECT * FROM prispevek_stav WHERE ID_STAV_PRISPEVEK = :stav";
      $data = self::$conn->prepare($query);
      
      $stav = htmlspecialchars($stav);
      
      $data->bindparam(':stav', $stav);
      $data->execute();
      return $data->fetchAll();
    }
       
}

?>