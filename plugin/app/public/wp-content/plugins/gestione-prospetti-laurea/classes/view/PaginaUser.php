<?php
namespace GestioneProspettiLaurea\view;

use GestioneProspettiLaurea\repository\Configurazione;
use GestioneProspettiLaurea\services\CreaProspetti;
use GestioneProspettiLaurea\services\VisualizzaProspetti;
use GestioneProspettiLaurea\services\InviaProspetti;

if (!defined("ABSPATH")) {
    exit();
}

/*
 * Classe boundary usata per definire le pagine accessibili dall'utente. In particolare si occupa di redirezionare la
 * pagina user alla view definita dal plugin, gestirne le richieste POST, e redirezionare tutte le altre pagine verso
 * 404.
 */
class PaginaUser
{
    /*
     * Inizializza la pagina user, caricando la pagina aggacciandosi all'hook "template_redirect" offerto da WordPress.
     */
    public static function init()
    {
        // redireziona alla pagina del plugin, o a 404, ogni volta che si cerca di accedere a una pagina del sito
        add_action("template_redirect", [__CLASS__, "modificaPaginaUser"]);
    }

    /*
     * La routine che viene agganciata all'hook "template_redirect", mostra la pagina user vera e propria se si sta
     * cercando di accedere alla front page, altrimenti mostra la pagina di 404.
     */
    public static function modificaPaginaUser()
    {
        if (is_front_page()) {
            // mostra pagina plugin
            self::mostraPaginaUser();
            exit();
        } else {
            // tutte le altre pagine vanno a 404
            self::mostra404();
        }
    }

    /*
     * Mostra la pagina user vera e propria, gestendo eventuali richieste post, e ottenendo l'insieme di corsi
     * disponibili dalla configurazione corrente.
     */
    public static function mostraPaginaUser()
    {
        try {
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                // gestisci la richiesta POST ottenendo stato ed effettuando l'azione richiesta
                extract(PaginaUser::gestisciPOST($_POST));
            } else {
                // la richiesta non è POST, lascia opzioni vuote
                $corso_di_laurea = $data = $matricole = "";
            }

            // carica informazioni sui corsi di laurea dal file di configurazione
            $corsi = PaginaUser::ottieniCorsi();
        } catch (\Exception $e) {
            echo "<pre>" . $e->getMessage() . "</pre>";
            die();
        }

        // mostra la pagina
        include PLUGIN_BASE_PATH . "views/user.php";
    }

    /*
     * Mostra la pagina di 404 del tema corrente, chiamato quando si accede a pagine diverse dalla front page.
     */
    public static function mostra404()
    {
        // imposta status 404
        global $wp_query;
        $wp_query->set_404();
        status_header(404);

        // mostra il 404 del tema corente
        get_template_part("404");
    }

    /*
     * Gestisce eventuali richieste POST presenti al caricamento della pagina, delegando l'azione richiesta
     * all'opportuna classe di servizio.
     */
    private static function gestisciPOST()
    {
        // prendi informazioni dalla richiesta POST sulle opzioni selezionate
        $corso_di_laurea = $_POST["corso-di-laurea"] ?? "";
        $data = $_POST["data"] ?? "";
        $matricole = $_POST["matricole"] ?? "";

        // spezza lista matricole su spazio bianco
        $l_matricole = preg_split("/\s+/", $matricole);

        // gestisci l'azione richiesta
        $action = $_POST["action"] ?? "";

        if (!empty($action)) {
            try {
                switch ($action) {
                    case "crea":
                        $crea_prospetti = new CreaProspetti();
                        $dest = $crea_prospetti->creaProspetti($corso_di_laurea, $data, $l_matricole);
                        self::mostraAllerta(
                            "Prospetti creati in " . $dest . ", premere su 'Visualizza prospetti' per visualizzarli",
                        );

                        break;

                    case "apri":
                        $visualizza_prospetti = new VisualizzaProspetti();
                        $visualizza_prospetti->visualizzaProspetti($corso_di_laurea, $data);

                        break;

                    case "invia":
                        $invia_prospetti = new InviaProspetti();
                        $count = $invia_prospetti->inviaProspetti($corso_di_laurea, $data);
                        self::mostraAllerta("Inviate " . $count . " e-mail di prospetti ai laureandi");

                        break;
                }
            } catch (\Exception $e) {
                self::mostraAllerta($e->getMessage());
            }
        }

        return compact("corso_di_laurea", "data", "matricole");
    }

    /*
     * Carica l'insieme dei corsi disponibili dalla configurazione corrente.
     */
    private static function ottieniCorsi()
    {
        $conf = Configurazione::ottieniConfigurazione();
        $corsi = Configurazione::ottieniCorsi($conf);

        return $corsi;
    }

    /*
     * Semplice helper che mostra messaggi di allerta al caricamento della pagina. Usato per diagnosticare errori nella
     * compilazione del form.
     */
    public static function mostraAllerta($mess)
    {
        echo '<script>alert("' . $mess . '")</script>';
    }
}
