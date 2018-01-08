<?php
// načítá soubor config.php, pokud se soubor nepodaří načíst, skript skončí
// varianta include, include_once, require_once
require 'config.php';
/**
 * Funkce pridejClanek
 * 
 * Funkce přidává článek do tabulky clanky
 *
 * Použití:
 * $idclanku = pridejClanek('Název článku', 'Zvučný text', 'Autor textu');
 * echo $idclanku; // 1
 * 
 * @param string $titulek
 * @param string $clanek
 * @param string $autor
 * @return int id vloženého článku
 */
function pridejClanek($titulek, $clanek, $autor) {
    // Volání funkce connection ze souboru config.php, vrací PDO objekt
    $db = connection(); 
    // Příprava SQL příkazu INSERT
    // Jménné parametry (všechno co má před sebou : - např. :titulek)
    // Protože nevkládáme konkrétní záznam do tabulky, ale pouze připravujeme
    // příkaz, který do naší tabulky dokáže vložit cokoliv, potřebujeme nějak určit
    // na jaká místa se bude co ukládat viz. jmenné parametry.
    // Slouží to jako ochrana proti SQL injection, všechny vložené hodnoty jsou escapovaný
    // a připraveny na bezpečnou interakci s databází
    // Bonus: Lze používat i znak otazníku (?), místo jmenných parametrů (méně přehledné)
    $sql = 'INSERT INTO clanky (titulek, clanek, autor)
            VALUES (:titulek, :clanek, :autor);';
    // Volání $db->prepare slouží k přípravě SQL dotazu před zpracováním
    // Provede escaping potenciálně nebezpečných znaků (např apostrofy), 
    // Pokud se povede dotaz připravit, vrátí PDOStatement objekt, pokud ne tak hodnotu false
    // Bonus: $stmt vytváříme protože, $db->prepare vrací PDOStatement objekt, cože je něco jiného než PDO objekt
    //        PDO má metodu query, která provádí dotaz, ale nemá možnosti proti SQL injection
    $stmt = $db->prepare($sql);
    // V případě, že se prepare SQL dotazu povede, je čas spárovat jmenné parametry a konkrétní hodnoty
    // Tyto hodnoty pochází z parametru této funkce, nicméně mohou pocházet odkudkoliv
    // Execute tedy do SQL dotazu dodá konkrétní hodnoty (ošetřené proti SQL injection)
    // a potom ten dotazy vykoná v databázi.
    $stmt->execute([':titulek' => $titulek,
                    ':clanek' => $clanek,
                    ':autor' => $autor]);
    // Pokud chceme vědět ID právě vloženého záznamu, má pro nás PDO připravenou metodu lastInsertId()
    // Ta vratí hodnotu posledního vloženého ID do databáze (nutno mít nastavený Auto Increment)
    // Použijeme například, pokud vkládáme do více tabulek najednou a ve druhém vložení, potřebujeme id předchozího vloženého záznamu do jiné tabulky
    $id = $db->lastInsertId();
    // Ukončení spojení a zahození PDO objektu
    // Není to zcela důležité ani povinné, nicméně je to užitečné v případě, že má databázový server omezený počet připojení do databáze
    // PDO objekt standartně zaniká přirozeně při ukončení skriptu (načtení celé stránky do prohlížeče)
    $db = null;
    // Návratová hodnota funkce - v tomto případě vrací ID právě vloženého článku, pokud by vracela cokoliv jiného, něco se nepovedlo
    return $id;
}
/**
 * Funkce upravuje článek v tabulce clanky
 * 
 * Lze upravovat všechny sloupce u záznamu
 * (Popis stejný jako u INSERT)
 * 
 * Použití:
 * $upraveno = upravClanek('Nový název', 'Upravený text', 'upravné jméo autora', 1);
 * echo 'Počet upravených záznamů: ' . $upraveno; // Počet upravených záznamů: 1
 *
 * @param string $titulek
 * @param string $clanek
 * @param string $autor
 * @param int $id_clanku
 * @return int počet ovlivněných záznamů
 */
function upravClanek($titulek, $clanek, $autor, $id_clanku) {
    $db = connection(); // Vrátí hotový PDO objekt
    $sql = 'UPDATE clanky SET titulek = :titulek, clanek = :clanek,
            autor = :autor WHERE id = :id;';
    $stmt = $db->prepare($sql);
    $stmt->execute([':id' => $id_clanku,
                    ':titulek' => $titulek,
                    ':clanek' => $clanek,
                    ':autor' => $autor]);
    // Vrátí počet záznamů (řádků), který byl SQL nějakým způsobem ovlivněn.
    // Pokud je vrácena hodnota 0 - nebyl ovlivněn žádný řádek tabulky                
    $rows = $stmt->rowCount();
    $db = null;
    return $rows;
}
/**
 * Funkce smazClanekById
 * 
 * Funkce maže články z tabulky clanky podle ID (pouze jeden článek najednou).
 * 
 * Použití:
 * $smazanych = smazClanekById(1);
 * echo 'Počet smazaných záznamů: ' . $smazanych;
 *
 * @param int $id_clanku
 * @return int počet smazaných záznamů
 */
function smazClanekById($id_clanku) {
    $db = connection(); // Vrátí hotový PDO objekt
    $sql = 'DELETE FROM clanky WHERE id = :id';
    $stmt = $db->prepare($sql);
    $stmt->execute([':id' => $id_clanku]);
    $rows = $stmt->rowCount();
    $db = null;
    return $rows;
}
/**
 * Funkce smazClanek
 * 
 * Funkce maže články z tabulky clanky podle libovolné podmínky.
 * Podmínku určuje programátor, jako druhý parametr používá pole.
 * 
 * Použití:
 * $smazanych = smazClanek('autor = :autor AND datum < :datum', [':autor' => $autor, 'datum' => '2016-01-01 00:00:00']);
 * echo 'Počet smazaných záznamů: ' . $smazanych;
 *
 * @param string $condition SQL podmínka
 * @param array $arr pole jmenných parametrů
 * @return int počet smazaných záznamů
 */
function smazClanek($condition, $arr) {
    $db = connection(); // Vrátí hotový PDO objekt
    // SQL příkaz maže články z tabulky, nicméně přidává podmínku z parametru funkce
    $sql = 'DELETE FROM clanky WHERE ' . $condition;
    $stmt = $db->prepare($sql);
    // Execute přijímá pole jako parametr
    $stmt->execute($arr);
    $rows = $stmt->rowCount();
    $db = null;
    return $rows;
}
/**
 * ukazJedenClanek
 * 
 * Provede SQL dotaz a vrátí jeden článek s libovolným ID.
 * 
 * Použití:
 * $clanek = ukazJedenClanek(1);
 * echo 'Název článku: '. $clanek['titulek'];
 *
 * @param int $id_clanku
 * @return array asociativní pole s článkem
 */
function ukazJedenClanek($id_clanku) {
    $db = connection(); // Vrátí hotový PDO objekt
    $sql = 'SELECT * FROM clanky WHERE id = :id';
    $stmt = $db->prepare($sql);
    $stmt->execute([':id' => $id_clanku]);
    // Pokud do databáze pouze data posíláme, stačí nám jen informace o tom, jak operace proběhla
    // Pokud však chceme vytáhnout data, abychom je mohli použít, musíme použít metodu Fetch nebo FetchAll
    // Fetch vrátí jeden následující záznam v sadě výsledků a příjímá jeden parametr, který upravuje v jakém formátu se mají data vrátit
    // 
    // PDO::FETCH_BOTH - vrací asociativní (PDO::FETCH_ASSOC) a číselné pole (PDO::FETCH_NUM) (výchozí)
    // $clanky = $stmt->fetch();
    // ['titulek' => 'Název článku', 0 => 'Název článku', 'clanek' => 'Text článku', 1 => 'Text článku',...]
    // echo $clanky[0] . $clanky['clanek'];
    // PDO::FETCH_ASSOC - vrací asociativní pole
    // $clanky = $stmt->fetch(PDO::FETCH_ASSOC);
    // ['titulek' => 'Název článku', 'clanek' => 'Text článku', ...]
    // PDO::FETCH_OBJ - vrací objekt
    // $clanky = $stmt->fetch(PDO::FETCH_OBJ);
    // echo $clanky->titulek . ' ' . $clanky->nazev; 

    $clanky = $stmt->fetch(PDO::FETCH_ASSOC);
    $db = null;
    return $clanky;
}
/**
 * UkazVšechnyClanky
 *
 * @return array všechny články
 */
function ukazVsechnyClanky() {
    $db = connection(); // Vrátí hotový PDO objekt
    $sql = 'SELECT * FROM clanky;';
    $stmt = $db->prepare($sql);
    $stmt->execute();
    // Stejně jako Fetch, s rozdílem, že nevrací jeden záznam, ale všechny
    // Bonus:
    // PDO::FETCH_KEY_PAIR
    // $stmt = $db->prepare('SELECT id, titulek FROM clanky');
    // $clanky = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    // ['id' => 'titulek'] 
    // [1 => 'Nový článek', 2 => 'Další článek', 3 => 'Ještě další článek']
    // Vhodné použití např. checkbox, radio, select nebo odkazy
    $clanky = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $db = null;
    return $clanky;
}