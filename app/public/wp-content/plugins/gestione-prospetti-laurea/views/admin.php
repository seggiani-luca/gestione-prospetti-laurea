<?php 
// carica informazioni dal file di configurazione (per riferimento)
$path_corsi = plugin_dir_path(dirname(__FILE__)) . "config/corsi.json";
$dati_corsi = json_decode(file_get_contents($path_corsi), true);
$corsi_di_laurea = &$dati_corsi["corsi-di-laurea"];

if ($_SERVER["REQUEST_METHOD"] === "POST") 
{    
    // prendi informazioni dalla richiesta POST sull'azione richiesta
    $action = $_POST["action"] ?? "";
    $nome_corso = $_POST["corso"] ?? "";

    // cerca il corso richiesto fra quelli definiti
    if(!empty($nome_corso)) 
    {
        foreach($corsi_di_laurea as $i => &$corso_di_laurea) 
        {
            if($corso_di_laurea["nome"] == $nome_corso)
            {
                $corso = &$corso_di_laurea;
                $chiave_corso = $i;
                break;
            }
        }

        if(!isset($corso)) 
        {
            // se non hai trovato il corso, limitati a mostrare la pagina di configurazione
            unset($corsi_di_laurea);
            return;
        }
    }
    
    // se si è richiesta un'azione, effettuala
    switch($action)
    {
        case "elimina-esami":
            // prendi array esami presenti e da rimuovere
            $esami_tutti = $corso["esami-in-media"] ?? [];
            $esami_selezionati = $_POST["esami-selezionati"] ?? [];
            
            // filtra gli esami presenti prendendo solo quelli non da rimuovere
            $esami_filtrati = array_filter($esami_tutti, function ($esame) use ($esami_selezionati) {
                return !in_array($esame["COD"], $esami_selezionati);
            }); 

            // metti gli esami filtrati nel corso
            $corso["esami-in-media"] = array_values($esami_filtrati);
            break;

        case "aggiungi-esame":
            // crea il nuovo esame
            $nuovo_esame = [];

            $COD = $_POST["codice-nuovo-esame"] ?? "";
            $DES = $_POST["nome-nuovo-esame"] ?? "";

            // se il codice o il nome sono vuoti, non aggiungere
            if(empty($COD) || empty($DES)) {
                break;
            }

            $nuovo_esame["COD"] = $COD;
            $nuovo_esame["DES"] = $DES;

            // aggiungi l'esame al corso
            $corso["esami-in-media"][] = $nuovo_esame;
            break;

        case "aggiorna-corso":
            // ottieni riferimento a configurazione calcolo
            $calcolo = &$corso["calcolo-voto"];

            // aggiorna configurazione
            $calcolo["formula-di-voto"] = $_POST["formula-di-voto"] ?? "";
            $calcolo["t-min"] = $_POST["t-min"] ?? "";
            $calcolo["t-max"] = $_POST["t-max"] ?? "";
            $calcolo["t-step"] = $_POST["t-step"] ?? "";
            $calcolo["c-min"] = $_POST["c-min"] ?? "";
            $calcolo["c-max"] = $_POST["c-max"] ?? "";
            $calcolo["c-step"] = $_POST["c-step"] ?? "";
            $calcolo["valore-lode"] = $_POST["valore-lode"] ?? "";
            $calcolo["bonus"] = isset($_POST["bonus"]);

            unset($calcolo);
            break;

        case "elimina-corso":
            // elimina il corso
            unset($corsi_di_laurea[$chiave_corso]);
            break;

        case "aggiungi-corso":
            // crea il nuovo corso dal template di default
            $nome_nuovo_corso = $_POST["nome-nuovo-corso"] ?? "";

            // se il nome è vuoto, non aggiungere
            if(empty($nome_nuovo_corso)) 
            {
                break;
            }

            $path_default = plugin_dir_path(dirname(__FILE__)) . "config/default.json";
            $nuovo_corso = json_decode(file_get_contents($path_default), true);
            $nuovo_corso["nome"] = $nome_nuovo_corso;

            // aggiungi il nuovo corso
            $corsi_di_laurea[] = $nuovo_corso;
            break;
    }

    // salva informazioni nel file di configurazione
    $corsi_aggiornato = json_encode($dati_corsi, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if (file_put_contents($path_corsi, $corsi_aggiornato) === false) 
    {
        die("Errore durante il salvataggio del file JSON.");
    }

    unset($corso);
}    

unset($corsi_di_laurea);
?>
<?php
// carica informazioni dal file di configurazione (per valore)
$corsi_di_laurea = $dati_corsi["corsi-di-laurea"];
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
                            <input type="checkbox" name="esami-selezionati[]" id="esame-selezionato" value="<?= esc_html($esame["COD"] ?? ""); ?>">
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="tablenav">
                <button class="button" style="color: red; border-color: red" type="submit" name="action" value="elimina-esami">Elimina esami selezionati</button>
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
                        <input type="checkbox" id="bonus" name="bonus" value=1 <?= !empty($calcolo["bonus"]) ? "checked" : ""; ?>/>
                    </td>
                </tr>
            </table>
            <div class="tablenav">
                <button class="button" type="submit" name="action" value="aggiorna-corso">Aggiorna corso</button>
                <button class="button" style="margin-left: 10px; color: red; border-color: red" type="submit" name="action" value="elimina-corso">Elimina corso</button>
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