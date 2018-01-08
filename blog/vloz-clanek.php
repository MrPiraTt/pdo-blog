<?php
    require 'funkce-clanky.php';
    $titulek = $clanek = $autor = "";

    if(isset($_POST['vlozit'])) {
        $errors = [];
        $msg = '';
        $titulek = filter_input(INPUT_POST, 'titulek', FILTER_SANITIZE_STRING);
        $clanek = htmlspecialchars($_POST['clanek']);
        $autor = filter_input(INPUT_POST, 'autor', FILTER_SANITIZE_STRING);
        if(empty($titulek))
            $errors[] = 'Chybí vyplněný titulek :(';
        if(empty($clanek))
            $errors[] = 'Chybí vyplněný článek :-((((((';
        if(empty($autor))
            $errors[] = 'Chybí vyplněný autor :-/';
        if(empty($errors)) {
            $clanekid = pridejClanek($titulek, $clanek, $autor);
            $_SESSION['msg'] = ['class' => 'success', 
                    'text' => 'Článek s ID ' . $clanekid . ' byl vložen do DB.']; 
            $titulek = $clanek = $autor = "";         
        } else {
            $_SESSION['msg'] = ['class' => 'danger', 'text' => 'Formulář nebyl odeslán!'];
        }
    }
    include_once 'hlavicka.php';
?>
    <h1>Vložit nový článek</h1>
    <?php

        if(!empty($errors)) {
            echo '<ul>';
            foreach($errors as $error) 
                echo '<li>' . $error . '</li>';
            echo '</ul>';
        }
      ?>
        <form action="vloz-clanek.php" method="post">

            <div class="form-group">
                <label>Titulek</label>
                <input type="text" name="titulek" class="form-control" value="<?php echo $titulek; ?>">
            </div>
            <div class="form-group">
                <label>Článek</label>
                <textarea name="clanek" class="form-control">
                    <?php echo htmlspecialchars_decode($clanek); ?>
                </textarea>
            </div>
            <div class="form-group">
                <label>Autor</label>
                <input type="text" name="autor" class="form-control" value="<?php echo $autor; ?>">
            </div>
            <button type="submit" name="vlozit" class="btn btn-primary">Vložit nový článek</button>
            <a href="index.php" class="btn btn-warning">Zpět na články</a>

        </form>
        <?php include_once 'paticka.php'; ?>