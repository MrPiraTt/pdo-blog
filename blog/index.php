<?php
require_once 'funkce-clanky.php';
$clanky = ukazVsechnyClanky();
include_once 'hlavicka.php';
?>

  <h1>Blog</h1>
  <?php if(count($clanky) > 0) { ?>
  <?php foreach($clanky as $clanek) { ?>
  <article class="clanek">
    <h2>
      <a href="clanek.php?idclanku=<?php echo $clanek['id']; ?>"><?php echo $clanek['titulek']; ?></a>
    </h2>
    <p class="lead">
      <?php echo strip_tags(htmlspecialchars_decode(substr($clanek['clanek'], 0, 250))); ?>
    </p>
    <a href="clanek.php?idclanku=<?php echo $clanek['id']; ?>" class="btn btn-primary">Přejít na článek</a>
    <a href="uprav-clanek.php?idclanku=<?php echo $clanek['id']; ?>" class="btn btn-warning">Upravit článek</a>
    <a href="smaz-clanek.php?idclanku=<?php echo $clanek['id']; ?>" class="btn btn-danger">Smazat článek</a>
  </article>
  <?php } ?>
  <?php } else { ?>
  <p class="alert alert-warning">Bohužel do blogu ještě nikdo nepřidal žádný článek. Buďte první.</p>
  <a href="vloz-clanek.php" class="btn btn-success">Vložit nový článek</a>
  <?php } ?>

  <?php include_once 'paticka.php'; ?>