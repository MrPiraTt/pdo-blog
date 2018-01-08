<?php
    require 'funkce-clanky.php';
    $id = filter_input(INPUT_GET, 'idclanku', FILTER_VALIDATE_INT);
    $clanek = ukazJedenClanek($id);
    if($clanek === false) {
        $_SESSION['msg'] = ['class' => 'danger', 
                            'text' => 'Takový článek u nás nemáme.'];
        header('Location: index.php');        
    }
    extract($clanek);
    if(isset($_POST['upravit'])) {
        $errors = [];
        $msg = '';
        $titulek = filter_input(INPUT_POST, 'titulek', FILTER_SANITIZE_STRING);
        $clanek = htmlspecialchars($_POST['clanek']);
        $autor = filter_input(INPUT_POST, 'autor', FILTER_SANITIZE_STRING);
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if(empty($titulek))
            $errors[] = 'Chybí vyplněný titulek :(';
        if(empty($clanek))
            $errors[] = 'Chybí vyplněný článek :-((((((';
        if(empty($autor))
            $errors[] = 'Chybí vyplněný autor :-/';
        if(empty($errors)) {
            $clanekid = upravClanek($titulek, $clanek, $autor, $id);
            $_SESSION['msg'] = ['class' => 'success', 'text' => 'Článek byl úspěšně upraven.'];            
        } else {
            $_SESSION['msg'] = ['class' => 'danger', 'text' => 'Formulář nebyl odeslán!'];
        }
    }
    include_once 'hlavicka.php';
?>
    <h1>Uprav článek</h1>
    <?php
            if(!empty($errors)) {
                echo '<ul>';
                foreach($errors as $error) 
                    echo '<li>' . $error . '</li>';
                echo '</ul>';
            }
        ?>
        <form action="uprav-clanek.php?idclanku=<?php echo $id; ?>" method="post">

            <div class="form-group">
                <label>Titulek</label>
                <input type="text" name="titulek" class="form-control" value="<?php echo $titulek; ?>">
            </div>
            <div class="form-group">
                <label>Článek</label>
                <textarea name="clanek" class="form-control"><?php echo htmlspecialchars_decode($clanek); ?></textarea>
            </div>
            <div class="form-group">
                <label>Autor</label>
                <input type="text" name="autor" class="form-control" value="<?php echo $autor; ?>">
            </div>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <button type="submit" name="upravit" class="btn btn-primary">Upravit článek</button>
            <a href="index.php" class="btn btn-warning">Zpět na články</a>
        </form>
        
        <?php include_once 'paticka.php'; ?>