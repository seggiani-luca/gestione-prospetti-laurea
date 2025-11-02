<?php
use GestioneProspettiLaurea\Configurazione;

require_once plugin_dir_path(dirname(__FILE__)) . "vendor/autoload.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // prendi informazioni dalla richiesta POST sulle opzioni selezionate
    $action = $_POST["action"] ?? "";

    $corso_di_laurea = $_POST["corso-di-laurea"] ?? "";
    $data = $_POST["data"] ?? "";
    $matricole = $_POST["matricole"] ?? "";

    // se si è richiesta un'azione, effettuala
    switch ($action) {
        case "crea":
            CreaProspetti::creaProspetti($corso_di_laurea, $data, $matricole);
            break;

        case "apri":
            VisualizzaProspetti::visualizzaProspetti($corso_di_laurea, $data, $matricole);
            break;

        case "invia":
            InviaProspetti::inviaProspetti($corso_di_laurea, $data, $matricole);
            break;
    }
} else {
    // la richiesta non è POST, lascia opzioni vuote
    $corso_di_laurea = $data = $matricole = "";
}

// carica informazioni sui corsi di laurea dal file di configurazione
$conf = new Configurazione();
$nomi_corsi = $conf->ottieniNomiCorsi();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Gestione prospetti laurea</title>
        <!-- ottieni stylesheet locale al plugin -->
        <link 
            rel="stylesheet" 
            href="<?php echo plugin_dir_url(dirname(__FILE__)) . "public/css/style.css"; ?>"
        />
    </head>
    <body>
        <div class="wrap-main">
            <h1>Gestione prospetti laurea</h1>
            <form action="" method="post">
                <div class="main-box">
                    <div class="input-box">
                        <!-- input corso di laurea -->
                        <p>
                            <label for="corso-di-laurea">Corso di laurea</label>
                            <select id="corso-di-laurea" name="corso-di-laurea">
                                <!-- opzione di default -->
                                <option 
                                    value="" 
                                    disabled <?= $corso_di_laurea ? "" : "selected" ?>
                                >
                                    Scegli un corso di laurea
                                </option>
                                <!-- opzioni configurabili -->
                                <?php foreach ($nomi_corsi as $nome): ?>
                                    <option 
                                        value="<?= htmlspecialchars($nome) ?>"
                                        <?= $nome == $corso_di_laurea ? "selected" : "" ?>
                                    >
                                        <?= htmlspecialchars($nome) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </p>
                        <!-- input data -->
                        <p>
                            <label for="data">Data</label>
                            <input
                                type="date"
                                id="data"
                                name="data"
                                value="<?= esc_attr($data) ?>"
                            />
                        </p>
                        <!-- link configurazione -->
                        <p>
                            <a href="/admin">Configurazione</a>
                        </p>
                    </div>
                    <div class="matricole-box">
                        <!-- input matricole -->
                        <p>
                            <label for="matricole">Matricole</label>
                            <textarea 
                                id="matricole"
                                name="matricole"
                                rows="10"
                                cols="30"
                                style="resize: none"
                            ><?= esc_textarea($matricole) ?></textarea>
                        </p>
                    </div>
                    <div class="output-box">
                        <p>
                            <label>Prospetti</label>
                            <!-- crea prospetti -->
                            <button
                                type="submit"
                                name="action"
                                value="crea"
                            >
                                Crea prospetti
                            </button>
                            <!-- visualizza prospetti -->
                            <button
                                class="button-link"
                                type="submit"
                                name="action"
                                value="apri"
                            >
                                Visualizza prospetti
                            </button>
                        </p>
                        <p>
                            <label>Studenti</label>
                            <!-- invia prospetti -->
                            <button
                                type="submit"
                                name="action"
                                value="invia"
                            >
                                Invia prospetti
                            </button>
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </body>
</html>