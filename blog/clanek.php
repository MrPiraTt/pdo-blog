<?php
require_once 'funkce-clanky.php';
$id = filter_input(INPUT_GET, 'idclanku', FILTER_VALIDATE_INT);
$clanek = ukazJedenClanek($id);
if(!$clanek) {
  $_SESSION['msg'] = ['class' => 'danger', 
                      'text' => 'Takový článek u nás nemáme'];    
  header('Location: index.php');
}
include_once 'hlavicka.php';
?>
<article class="clanek">
  <h1><?php echo htmlspecialchars($clanek['titulek']); ?></h1>
  <div class="clanek-meta">
    <em><?php echo htmlspecialchars($clanek['vytvoreno']); ?></em>
    <span class="badge badge-secondary"><?php echo htmlspecialchars($clanek['autor']); ?></span>
  </div>
  <div class="clanek-obsah">
    <p class="lead">
    <?php echo htmlspecialchars_decode($clanek['clanek']); ?>
    </p>
  </div>
</article>
<a href="index.php" class="btn btn-warning">Zpět na články</a>

<?php include_once 'paticka.php'; ?>
