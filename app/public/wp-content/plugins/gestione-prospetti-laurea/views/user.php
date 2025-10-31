<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") 
{
    $action = $_POST["action"] ?? "";

    $corso_di_laurea = $_POST["corso-di-laurea"] ?? "";
    $data  = $_POST["data"] ?? "";
    $matricole = $_POST["matricole"] ?? "";

    switch($action)
    {
        case "crea":
            require_once plugin_dir_path(dirname(__FILE__)) . "classes/crea-prospetti.php";
            CreaProspetti::creaProspetti($corso_di_laurea, $data, $matricole);
            break;
            
        case "apri":
            require_once plugin_dir_path(dirname(__FILE__)) . "classes/visualizza-prospetti.php";
            VisualizzaProspetti::visualizzaProspetti($corso_di_laurea, $data, $matricole);
            break;

        case "invia":
            require_once plugin_dir_path(dirname(__FILE__)) . "classes/invia-prospetti.php";
            InviaProspetti::inviaProspetti($corso_di_laurea, $data, $matricole);
            break;
    }
} 
else
{
    $corso_di_laurea = $data  = $matricole = "";
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Gestione prospetti laurea</title>
        <!-- ottieni stylesheet locale al plugin -->
        <link rel="stylesheet" href="<?php echo plugin_dir_url(dirname(__FILE__)) . "public/css/style.css";?>"/>
    </head>
    <body>
        <div class="wrap-main">
            <h1>Gestione prospetti laurea</h1>
            <form action="" method="post">
                <div class="main-box">
                    <div class="input-box">
                        <p>
                            <label>Corso di laurea</label>
                            <select name="corso-di-laurea">
                                <option value="" disabled selected>Scegli un corso di laurea</option>
                            </select>
                        </p>
                        <p>
                            <label>Data</label>
                            <input type="date" id="data" name="data" value="<?= esc_attr($data) ?>"/>
                        </p>
                        <p>
                            <a href="/admin">Configurazione</a>
                        </p>
                    </div>
                    <div class="matricole-box">
                        <p>
                            <label>Matricole</label>
                            <textarea name="matricole" rows="10" cols="30" style="resize: none"><?= esc_textarea($matricole) ?>
                            </textarea>
                        </p>
                    </div>
                    <div class="output-box">
                        <p>
                            <label>Prospetti</label>
                            <button type="submit" name="action" value="crea">Crea prospetti</button>
                            <button class="button-link" type="submit" name="action" value="apri">Visualizza prospetti</button>
                        </p>
                        <p>
                            <label>Studenti</label>
                            <button type="submit" name="action" value="invia">Invia prospetti</button>
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </body>
</html>