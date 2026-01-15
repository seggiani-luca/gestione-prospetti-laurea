<?php
use GestioneProspettiLaurea\repository\Configurazione;
?>
<!-- Vista della normale interfaccia utente principale. -->
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Gestione prospetti laurea</title>
        <!-- ottieni stylesheet locale al plugin -->
        <link 
            rel="stylesheet" 
            href="<?php echo PLUGIN_BASE_URL . "public/css/style.css"; ?>"
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
																<?php foreach ($corsi as $corso): ?>
																		<option 
																				value="<?= htmlspecialchars($corso->{"nome-corto"}) ?>"
																				<?= $corso->{"nome-corto"} == $corso_di_laurea ? "selected" : "" ?>
																		>
																				<?= htmlspecialchars(Configurazione::nomeCompleto($corso)) ?>
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
