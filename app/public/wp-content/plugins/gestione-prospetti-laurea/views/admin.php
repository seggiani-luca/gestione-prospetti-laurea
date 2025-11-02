<?php
use GestioneProspettiLaurea\Configurazione;

require_once plugin_dir_path(dirname(__FILE__)) . "vendor/autoload.php";

// carica informazioni sui corsi di laurea dal file di configurazione
$conf = new Configurazione();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // prendi informazioni dalla richiesta POST sull'azione richiesta
    $action = $_POST["action"] ?? "";
    $nome_corso = $_POST["corso"] ?? "";

    // se si è richiesta un'azione, effettuala
    switch ($action) {
        case "elimina-esami":
            // ottieni array esami da rimuovere
            $esami_selezionati = $_POST["esami-selezionati"] ?? [];

            $conf->eliminaEsami($nome_corso, $esami_selezionati);
            break;

        case "aggiungi-esame":
            // ottieni codice e nome nuovo esame
            $COD = $_POST["codice-nuovo-esame"] ?? "";
            $DES = $_POST["nome-nuovo-esame"] ?? "";

            $conf->aggiungiEsame($nome_corso, $COD, $DES);
            break;

        case "aggiorna-corso":
            // ottieni opzioni
            $opzioni = [];
            $opzioni["formula-di-voto"] = $_POST["formula-di-voto"] ?? "";
            $opzioni["t-min"] = $_POST["t-min"] ?? "";
            $opzioni["t-max"] = $_POST["t-max"] ?? "";
            $opzioni["t-step"] = $_POST["t-step"] ?? "";
            $opzioni["c-min"] = $_POST["c-min"] ?? "";
            $opzioni["c-max"] = $_POST["c-max"] ?? "";
            $opzioni["c-step"] = $_POST["c-step"] ?? "";
            $opzioni["valore-lode"] = $_POST["valore-lode"] ?? "";
            $opzioni["bonus"] = isset($_POST["bonus"]);

            $conf->aggiornaCorso($nome_corso, $opzioni);
            break;

        case "elimina-corso":
            $conf->eliminaCorso($nome_corso);
            break;

        case "aggiungi-corso":
            // ottieni nome nuovo corso
            $nome_nuovo_corso = $_POST["nome-nuovo-corso"] ?? "";

            $conf->aggiungiCorso($nome_nuovo_corso);
            break;
    }
}

// carica informazioni sui corsi di laurea dal file di configurazione
$corsi = $conf->ottieniCorsi();
?>
<div class="wrap">
    <h1>Configurazione gestione prospetti laurea</h1>
    <h2>Corsi di laurea</h2>
    <?php foreach ($corsi as $corso): ?>
    <div class="card" style="max-width: none">
        <form action="" method="post">
            <input 
                type="hidden"
                name="corso"
                value="<?= esc_attr($corso->{"nome"}) ?>"
            >
            <h1>
                <?= esc_html($corso->{"nome"}) ?>
            </h1>
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
                    <?php foreach ($corso->{"esami-in-media"} as $esame): ?>
                    <tr>
                        <td>
                            <strong
                                ><?= esc_html($esame->{"COD"}) ?>
                            </strong>
                        </td>
                        <td>
                            <strong>
                                <?= esc_html($esame->{"DES"}) ?>
                            </strong>
                        </td>
                        <td style="text-align: right">
                            <input 
                                type="checkbox" 
                                name="esami-selezionati[]" 
                                id="esame-selezionato" 
                                value="<?= esc_html($esame->{"COD"}) ?>"
                            >
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="tablenav">
                <button 
                    class="button"
                    style="color: red; border-color: red"
                    type="submit"
                    name="action"
                    value="elimina-esami"
                >
                        Elimina esami selezionati
                </button>
            </div>
            <h2 style="margin-top: 30px">Aggiungi nuovi esami</h2>
            <table class="wp-list-table widefat">
                <tr>
                    <th>
                        <label for="codice-nuovo-esame">Codice nuovo esame</label>
                    </th>
                    <td>
                        <input 
                            class="regular-text"
                            type="text"
                            id="codice-nuovo-esame"
                            name="codice-nuovo-esame"
                        />
                    </td>
                    <th>
                        <label for="nome-nuovo-esame">Nome nuovo esame</label>
                    </th>
                    <td>
                        <input
                            class="regular-text"
                            type="text"
                            id="nome-nuovo-esame"
                            name="nome-nuovo-esame"
                        />
                    </td>
                </tr>
            </table>
            <div class="tablenav">
                <button
                    class="button"
                    type="submit"
                    name="action"
                    value="aggiungi-esame"
                >
                    Aggiungi nuovo esame
                </button>
            </div>
            <h2 style="margin-top: 30px">Calcolo voto</h2>
            <?php $calcolo = $corso->{"calcolo-voto"}; ?>
            <table class="form-table">
                <tr>
                    <th>
                        <label for="formula-di-voto">Formula di voto</label>
                    </th>
                    <td>
                        <input
                            class="regular-text"
                            type="text"
                            id="formula-di-voto"
                            name="formula-di-voto"
                            value="<?= esc_html($calcolo->{"formula-di-voto"}) ?>"
                        />
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="t-min">Minimo voto tesi</label>
                    </th>
                    <td>
                        <input
                            class="regular-text"
                            type="number"
                            id="t-min"
                            name="t-min"
                            value="<?= esc_html($calcolo->{"t-min"}) ?>"
                        />
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="t-max">Massimo voto tesi</label>
                    </th>
                    <td>
                        <input
                            class="regular-text"
                            type="number"
                            id="t-max"
                            name="t-max"
                            value="<?= esc_html($calcolo->{"t-max"}) ?>"
                        />
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="t-step">Passo voto tesi</label>
                    </th>
                    <td>
                        <input
                            class="regular-text"
                            type="number"
                            id="t-step"
                            name="t-step"
                            value="<?= esc_html($calcolo->{"t-step"}) ?>"
                        />
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="c-min">Minimo voto commissione</label>
                    </th>
                    <td>
                        <input
                            class="regular-text" 
                            type="number" 
                            id="c-min" 
                            name="c-min" 
                            value="<?= esc_html($calcolo->{"c-min"}) ?>"
                        />
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="c-max">Massimo voto commissione</label>
                    </th>
                    <td>
                        <input
                            class="regular-text"
                            type="number" 
                            id="c-max" 
                            name="c-max" 
                            value="<?= esc_html($calcolo->{"c-max"}) ?>"
                        />
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="c-step">Passo voto commissione</label>
                    </th>
                    <td>
                        <input
                            class="regular-text"
                            type="number"
                            id="c-step"
                            name="c-step"
                            value="<?= esc_html($calcolo->{"c-step"}) ?>"
                        />
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="valore-lode">Valore lode</label>
                    </th>
                    <td>
                        <input
                            class="regular-text"
                            type="number"
                            id="valore-lode"
                            name="valore-lode"
                            value="<?= esc_html($calcolo->{"valore-lode"}) ?>"
                        />
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="bonus">Bonus</label>
                    </th>
                    <td>
                        <input 
                            type="checkbox"
                            id="bonus"
                            name="bonus" 
                            value=1 
                            <?= !empty($calcolo->{"bonus"}) ? "checked" : "" ?>
                        />
                    </td>
                </tr>
            </table>
            <div class="tablenav">
                <button
                    class="button"
                    type="submit"
                    name="action"
                    value="aggiorna-corso"
                >
                    Aggiorna corso
                </button>
                <button
                    class="button"
                    style="margin-left: 10px; color: red; border-color: red"
                    type="submit"
                    name="action"
                    value="elimina-corso"
                >
                    Elimina corso
                </button>
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
                    <input
                        class="regular-text"
                        type="text"
                        id="nome-nuovo-corso"
                        name="nome-nuovo-corso"
                    />
                </td>
            </tr>
        </table>
        <div class="tablenav">
            <button 
                class="button"
                type="submit"
                name="action"
                value="aggiungi-corso"
            >
                Aggiungi nuovo corso
            </button>
        </div>
    </form>
</div>