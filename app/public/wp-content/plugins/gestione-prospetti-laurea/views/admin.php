<?php 
// carica informazioni dal file di configurazione
$path_corsi = plugin_dir_path(dirname(__FILE__)) . "config/corsi.json";
$dati_corsi = json_decode(file_get_contents($path_corsi), true);
$corsi_di_laurea = $dati_corsi["corsi-di-laurea"] ?? [];

if ($_SERVER["REQUEST_METHOD"] === "POST") 
{    
    // prendi informazioni dalla richiesta POST sull'azione richiesta
    $action = $_POST["action"] ?? "";
    $corso = $_POST["corso"] ?? "";


    // se si è richiesta un'azione, effettuala
    switch($action)
    {
        case "elimina-esami":
            echo("Richiesto di eliminare esami da " . $corso);
            die();
            break;
        case "aggiungi-esame":
            echo("Richiesto di aggiungere esame a " . $corso);
            die();
            break;
        case "aggiorna-corso":
            echo("Richiesto di aggiornare " . $corso);
            die();
            break;
        case "aggiungi-corso":
            $nuovo_corso = $_POST["nome-nuovo-corso"];
            echo("Richiesto di aggiungere il corso " . $nuovo_corso);
            die();
            break;
    }
}

// salva informazioni nel file di configurazione
?>
<div class="wrap">
    <h1>Configurazione gestione prospetti laurea</h1>
    <h2>Corsi di laurea</h2>
    <?php foreach ($corsi_di_laurea as $corso): ?>
    <div class="card" style="max-width: none">
        <form action="" method="post">
            <input type="hidden" name="corso" value="<?= esc_attr($corso["nome"] ?? ''); ?>">
            <h1><?= esc_html($corso["nome"] ?? ""); ?></h1>
            <h2>Esami in media</h2>
            <table class="wp-list-table widefat">
                <thead>
                    <tr>
                        <th>
                            Codice
                        </th>
                        <th>
                            Nome
                        </th>
                        <th style="text-align: right">
                            Eliminare?
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($corso["esami-in-media"] ?? [] as $esame): ?>
                    <tr>
                        <td>
                            <strong><?= esc_html($esame["COD"] ?? ""); ?></strong>
                        </td>
                        <td>
                            <strong><?= esc_html($esame["DES"] ?? ""); ?></strong>
                        </td>
                        <td style="text-align: right">
                            <input type="checkbox" name="esame-selezionato" id="esame-selezionato" value="<?= esc_html($esame["COD"] ?? ""); ?>">
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="tablenav">
                <button class="button" type="submit" name="action" value="elimina-esami">Elimina esami selezionati</button>
            </div>
            <h2 style="margin-top: 30px">Aggiungi nuovi esami</h2>
            <table class="wp-list-table widefat">
                <tr>
                    <th>
                        <label for="codice-nuovo-esame">Codice nuovo esame</label>
                    </th>
                    <td>
                        <input class="regular-text" type="text" id="codice-nuovo-esame" name="codice-nuovo-esame"/>
                    </td>
                    <th>
                        <label for="nome-nuovo-esame">Nome nuovo esame</label>
                    </th>
                    <td>
                        <input class="regular-text" type="text" id="nome-nuovo-esame" name="nome-nuovo-esame"/>
                    </td>
                </tr>
            </table>
            <div class="tablenav">
                <button class="button" type="submit" name="action" value="aggiungi-esame">Aggiungi nuovo esame</button>
            </div>
            <h2 style="margin-top: 30px">Calcolo voto</h2>
            <?php $calcolo = $corso["calcolo-voto"] ?>
            <table class="form-table">
                <tr>
                    <th>
                        <label for="formula-di-voto">Formula di voto</label>
                    </th>
                    <td>
                        <input class="regular-text" type="text" id="formula-di-voto" name="formula-di-voto" value="<?= esc_html($calcolo["formula-di-voto"] ?? ""); ?>"/>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="t-min">Minimo voto tesi</label>
                    </th>
                    <td>
                        <input class="regular-text" type="number" id="t-min" name="t-min" value="<?= esc_html($calcolo["t-min"] ?? ""); ?>"/>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="t-max">Massimo voto tesi</label>
                    </th>
                    <td>
                        <input class="regular-text" type="number" id="t-max" name="t-max" value="<?= esc_html($calcolo["t-max"] ?? ""); ?>"/>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="t-step">Passo voto tesi</label>
                    </th>
                    <td>
                        <input class="regular-text" type="number" id="t-step" name="t-step" value="<?= esc_html($calcolo["t-step"] ?? ""); ?>"/>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="c-min">Minimo voto commissione</label>
                    </th>
                    <td>
                        <input class="regular-text" type="number" id="c-min" name="c-min" value="<?= esc_html($calcolo["c-min"] ?? ""); ?>"/>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="c-max">Massimo voto commissione</label>
                    </th>
                    <td>
                        <input class="regular-text" type="number" id="c-max" name="c-max" value="<?= esc_html($calcolo["c-max"] ?? ""); ?>"/>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="c-step">Passo voto commissione</label>
                    </th>
                    <td>
                        <input class="regular-text" type="number" id="c-step" name="c-step" value="<?= esc_html($calcolo["c-step"] ?? ""); ?>"/>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="valore-lode">Valore lode</label>
                    </th>
                    <td>
                        <input class="regular-text" type="number" id="valore-lode" name="valore-lode" value="<?= esc_html($calcolo["valore-lode"] ?? ""); ?>"/>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="bonus">Bonus</label>
                    </th>
                    <td>
                        <input class="regular-text" type="checkbox" id="bonus" name="bonus" value="<?= esc_html($calcolo["bonus"] ?? ""); ?>"/>
                    </td>
                </tr>
            </table>
            <div class="tablenav">
                <button class="button" type="submit" name="action" value="aggiorna-corso">Aggiorna corso</button>
            </div>
        </form>
    </div>
    <?php endforeach; ?>
    <form action="" method="post">
        <h2 style="margin-top: 30px">Aggiungi nuovi corsi</h2>
        <table class="form-table">
                <th>
                    <label for="nome-nuovo-corso">Nome nuovo corso</label>
                </th>
                <td>
                    <input class="regular-text" type="text" id="nome-nuovo-corso" name="nome-nuovo-corso"/>
                </td>
            </tr>
        </table>
        <div class="tablenav">
            <button class="button" type="submit" name="action" value="aggiungi-corso">Aggiungi nuovo corso</button>
        </div>
    </form>
</div>