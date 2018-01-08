<?php
require_once 'funkce-clanky.php';
$id = filter_input(INPUT_GET, 'idclanku', FILTER_VALIDATE_INT);
if(smazClanekById($id)) {
  $_SESSION['msg'] = ['class' => 'success', 
                      'text' => 'Článek '.$id.' smazán.'];  
} else {
  $_SESSION['msg'] = ['class' => 'danger', 
                      'text' => 'Článek nebyl smazán.'];    
}
header('Location: index.php');