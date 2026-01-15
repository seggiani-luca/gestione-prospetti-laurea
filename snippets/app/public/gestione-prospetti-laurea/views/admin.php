<!-- Vista dell'interfaccia per la modifica della configurazione. -->
<?php
use GestioneProspettiLaurea\repository\Configurazione;
?>
<div id="wpwrap">
    <div id="wpcontent" style="margin-left: 0">
        <div id="wpbody">
            <div id="wpbody-content">
                <div class="wrap">
                    <h1>Configurazione gestione prospetti laurea</h1>
                    <a class="home-link" href="/">Home</a>
                    <!-- lista corsi -->
                    <h2>Corsi di laurea</h2>
                    <?php foreach ($corsi as $corso): ?>
                    <div class="card" style="max-width: none">
                        <form action="" method="post">
                            <input 
                                type="hidden"
                                name="corso"
                                value="<?= esc_attr($corso->{"nome-corto"}) ?>"
                            >
                            <h1>
                                <?= esc_html(Configurazione::nomeCompleto($corso)) ?>
                            </h1>
                            <!-- lista filtri -->
                            <h2>Filtri</h2>
                            <table class="wp-list-table widefat">
                                <thead>
                                    <tr>
                                        <th>
                                            Matricola studente
                                        </th>
                                        <th>
                                            Codice esame
                                        </th>
                                        <th>
                                            Nome esame
                                        </th>
                                        <th>
                                            Tipo
                                        </th>
                                        <th style="text-align: right">
                                            Eliminare?
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($corso->{"esami"} as $esame): ?>
                                    <tr>
                                        <td>
                                            <strong
                                                ><?= esc_html($esame->{"codice-studente"}) ?>
                                            </strong>
                                        </td>
                                        <td>
                                            <strong
                                                ><?= esc_html($esame->{"codice-esame"}) ?>
                                            </strong>
                                        </td>
                                        <td>
                                            <strong
                                                ><?= esc_html($esame->{"nome-esame"}) ?>
                                            </strong>
                                        </td>
                                        <td>
                                            <strong
                                                ><?= esc_html($esame->{"tipo"} ? "Fuori media" : "Escluso") ?>
                                            </strong>
                                        </td>
                                        <td style="text-align: right">
                                            <input 
                                                type="checkbox" 
                                                name="esami-selezionati[]" 
                                                id="esame-selezionato" 
                                                value="<?= htmlspecialchars(
                                                    $esame->{"codice-studente"} .
                                                        "|" .
                                                        $esame->{"codice-esame"} .
                                                        "|" .
                                                        ($esame->{"tipo"} ? "fuori-media" : "esclusi"),
                                                    ENT_QUOTES,
                                                    "UTF-8",
                                                ) ?>"
                                            >
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <!-- impostazioni esami -->
                            <div class="tablenav">
                                <button 
                                    class="button"
                                    style="color: red;"
                                    type="submit"
                                    name="action"
                                    value="elimina-esami"
                                >
                                        Elimina filtri selezionati
                                </button>
                            </div>
                            <h2 style="margin-top: 30px">Aggiungi nuovi filtri</h2>
                            <table class="wp-list-table widefat">
                                <tr>
                                    <th>
                                        <label for="nuovo-codice-studente">Matricola studente</label>
                                    </th>
                                    <td>
                                        <input 
                                            class="regular-text"
                                            type="text"
                                            value="*"
                                            title="* per applicare a tutti gli studenti"
                                            id="nuovo-codice-studente"
                                            name="nuovo-codice-studente"
                                        />
                                    </td>
                                    <th>
                                        <label for="nuovo-codice-esame">Codice esame</label>
                                    </th>
                                    <td>
                                        <input 
                                            class="regular-text"
                                            type="text"
                                            id="nuovo-codice-esame"
                                            name="nuovo-codice-esame"
                                        />
                                    </td>
                                <tr>
                                </tr>
                                    <th>
                                        <label for="nuovo-nome-esame">Nome esame</label>
                                    </th>
                                    <td>
                                        <input
                                            class="regular-text"
                                            type="text"
                                            id="nuovo-nome-esame"
                                            name="nuovo-nome-esame"
                                        />
                                    </td>
                                    <th>
                                        <label for="nuovo-tipo-esame">Fuori media / escluso</label>
                                    </th>
                                    <td>
                                        <input
                                            type="checkbox"
                                            checked
                                            title="Check significa fuori media, altrimenti escluso"
                                            id="nuovo-tipo-esame"
                                            name="nuovo-tipo-esame"
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
                                    Aggiungi nuovo filtro
                                </button>
                            </div>
                            <!-- impostazioni calcolo voto -->
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
                                        <label for="cfu-richiesti">CFU curricolari richiesti</label>
                                    </th>
                                    <td>
                                        <input
                                            class="regular-text"
                                            type="number"
                                            id="cfu-richiesti"
                                            name="cfu-richiesti"
                                            value="<?= esc_html($calcolo->{"cfu-richiesti"}) ?>"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <label for="info-voto-finale">Informazioni voto finale</label>
                                    </th>
                                    <td>
                                        <input
                                            class="regular-text"
                                            type="text"
                                            id="info-voto-finale"
                                            name="info-voto-finale"
                                            style="width: 100%"
                                            value="<?= esc_html($calcolo->{"info-voto-finale"}) ?>"
                                            title="Il testo mostrato dopo le tabelle di simulazione"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <label for="itera-t-c">Itera voto tesi / commissione</label>
                                    </th>
                                    <td>
                                        <input 
                                            type="checkbox"
                                            id="itera-t-c"
                                            name="itera-t-c"
                                            value=1
                                            <?= !empty($calcolo->{"itera-t-c"}) ? "checked" : "" ?>
                                            title="Check significa itera su voto tesi, altrimenti itera su voto commissione"
                                        />
                                    </td>
                                </tr>
                            </table>
                            <!-- impostazioni mail -->
                            <h2 style="margin-top: 30px">E-mail</h2>
                            <?php $mail = $corso->{"mail"}; ?>
                            <table class="form-table">
                                <tr>
                                    <th>
                                        <label for="oggetto-mail">Oggetto</label>
                                    </th>
                                    <td>
                                        <input
                                            class="regular-text"
                                            type="text"
                                            id="oggetto-mail"
                                            name="oggetto-mail"
                                            style="width: 100%"
                                            value="<?= esc_html($mail->{"oggetto"}) ?>"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <label for="corpo-mail">Corpo</label>
                                    </th>
                                    <td>
                                        <textarea 
                                            id="corpo-mail"
                                            name="corpo-mail"
                                            rows="10"
                                            cols="30"
                                            style="width: 100%"
                                        ><?= esc_textarea($mail->{"corpo"}) ?></textarea>
                                    </td>
                                </tr>
                            </table>
                            <!-- impostazioni corso -->
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
                                    style="margin-left: 10px; color: red;"
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
                    <!-- aggiungi nuovi corsi -->
                    <form action="" method="post">
                        <h2 style="margin-top: 30px">Aggiungi nuovi corsi</h2>
                        <table class="wp-list-table widefat">
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
                                <th>
                                    <label for="nome-corto-nuovo-corso">Nome corto nuovo corso</label>               
                                </th>
                                <td>
                                    <input
                                        class="regular-text"
                                        type="text"
                                        id="nome-corto-nuovo-corso"
                                        name="nome-corto-nuovo-corso"
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
            </div>
        </div>
    </div>
</div>
