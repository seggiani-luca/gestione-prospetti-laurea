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
                            <input type="date" id="data" name="data"/>
                        </p>
                        <p>
                            <a href="/admin">Configurazione</a>
                        </p>
                    </div>
                    <div class="matricole-box">
                        <p>
                            <label>Matricole</label>
                            <textarea name="matricole" rows="10" cols="30" style="resize: none"></textarea>
                        </p>
                    </div>
                    <div class="output-box">
                        <p>
                            <label>Prospetti</label>
                            <button type="submit" name="action" value="crea">Crea prospetti</button>
                            <button class="button-link" type="submit" name="action" value="apri">Apri prospetti</button>
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