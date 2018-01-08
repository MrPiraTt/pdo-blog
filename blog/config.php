<?php
// Nastavuje kódování uvnitř PHP na utf-8 (just in case)
mb_internal_encoding('UTF-8');
// Startuje používání SESSIONS
session_start();
// Konfigurace databáze pro vy  tvoření PDO objektu
// Konstanty s údaji pro připojení k DB

// Data source name (DSN) - informace o databázi, serveru, jazykové sadě, portech, atd
define('DSN', 'mysql:host=localhost;dbname=blog;charset=utf8;');
// mysql - typ databázového serveru, 
// host - adresa databázové serveru, 
// dbname - název naší databáze
// charset - nastavení kódování

// Username - uživatelské jméno, které je autorizované operovat s danou DB.$_COOKIE
define('USERNAME', 'root');

// Password - heslo k uživatelskému účtu)
define('PASSWORD', '');
/*
// Jednoduchá funkce, která vrací PDO objekt. (Pro vytvoření stačí!)
function connection() {
    // $db = new PDO('mysql:host=localhost;dbname=vecek;charset=utf8;', 'root', '');
      $db = new PDO(DSN, USERNAME, PASSWORD);
      return $db;
    //return new PDO(DSN, USERNAME, PASSWORD);
}
*/
/**
 * Funkce connection
 * 
 * Pokusí se vytvořit PDO objekt $db, pokud jsou všechny údaje správné, je vrácený objekt komunikující s DB.
 * Pokud se objekt vytvořit nepodaří, je zachycena vyjímka a celý skript je ukončen funkcí die()
 *
 * @return PDO objekt, který lze použít pro interakci s databází
 */
function connection() {
    try {
        $db = new PDO(DSN, USERNAME, PASSWORD);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);        
        return $db;
    } catch (PDOException $e) {
        die('Connection failed: ' . $e->getMessage());
    }
}