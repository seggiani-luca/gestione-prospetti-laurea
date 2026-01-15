<?php
namespace GestioneProspettiLaurea\view;

use GestioneProspettiLaurea\repository\Configurazione;
use GestioneProspettiLaurea\services\GestisciConfigurazione;

if (!defined("ABSPATH")) {
    exit();
}

/*
 * Classe boundary usata per definire le pagine accessibili dall'amministratore. In particolare si occupa di
 * redirezionare la pagina di configurazione principale a quella del plugin, gestirne le richieste POST, e
 * nascondere alcune pagine di configurazione inutili al plugin.
 */
class PaginaAdmin
{
    /*
     * Modifica le pagina admin, caricando la pagina aggacciandosi all'hook "admin_menu" offerto da WordPress. Inoltre,
     * si aggancia un metodo all'hook "admin_init" per redirezionare dalla dashboard di default di WordPress a quella
     * del plugin.
     */
    public static function init()
    {
        // in fase di inizializzazione della pagina di configurazione, mostra solo pagine relative al plugin
        add_action("admin_menu", [__CLASS__, "modificaPaginaAdmin"]);

        // in fase di acceseso alla dashboard, redireziona alla pagina di configurazione del plugin
        add_action("admin_init", [__CLASS__, "redirezionaDashboard"]);
    }

    /*
     * La routine che viene agganciata all'hook "admin_menu", inizializza la pagina di configurazione del plugin, e
     * rimuove alcune pagine non inerenti al sistema.
     */
    public static function modificaPaginaAdmin()
    {
        // inizializza pagina di configurazione del plugin
        add_menu_page(
            "Gestione prospetti laurea",
            "Gestione prospetti laurea",
            "manage_options",
            "gestione-prospetti-laurea",
            [__CLASS__, "mostraPaginaAdmin"],
            "dashicons-admin-generic",
        );

        // rimuovi alcune pagine inutili
        remove_menu_page("index.php");
        remove_menu_page("edit.php");
        remove_menu_page("upload.php");
        remove_menu_page("edit.php?post_type=page");
        remove_menu_page("edit-comments.php");
        remove_menu_page("tools.php");

        // rimuovi alcune pagine di configurazione inutili
        remove_submenu_page("options-general.php", "options-writing.php");
        remove_submenu_page("options-general.php", "options-reading.php");
        remove_submenu_page("options-general.php", "options-discussion.php");
        remove_submenu_page("options-general.php", "options-media.php");
        remove_submenu_page("options-general.php", "options-permalink.php");
        remove_submenu_page("options-general.php", "options-privacy.php");
    }

    /*
     * La routine che viene agganciata all'hook "admin_init", redireziona dalla dashboard di default di WordPress a
     * quella del plugin.
     */
    public static function redirezionaDashboard()
    {
        // se si sta cercando di accedere all'index dell'amministratore (dashboard), redireziona
        global $pagenow;
        if ($pagenow === "index.php") {
            wp_redirect("admin.php?page=gestione-prospetti-laurea");
            exit();
        }
    }

    /*
     * Mostra la pagina admin vera e propria (cioè la pagina di configurazione del plugin), gestendo eventuali richieste
     * post, e ottenendo la configurazione corrente.
     */
    public static function mostraPaginaAdmin()
    {
        try {
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                // gestisci la richiesta POST aggiornando la configurazione
                self::gestisciPOST();
            }

            // carica informazioni dal file di configurazione
            $corsi = self::ottieniConfigurazione();
        } catch (\Exception $e) {
            echo "<pre>" . $e->getMessage() . "</pre>";
            die();
        }

        // mostra la pagina
        include PLUGIN_BASE_PATH . "/views/admin.php";
    }

    /*
     * Gestisce eventuali richieste POST presenti al caricamento della pagina, delegando l'azione richiesta alla classe
     * di servizio GestisciConfigurazione.
     */
    private static function gestisciPOST()
    {
        $gest_conf = new GestisciConfigurazione();

        // prendi informazioni dalla richiesta POST sull'azione richiesta
        $action = $_POST["action"] ?? "";
        if (isset($_POST["corso"])) {
            $nome_corso = $_POST["corso"];
            $id = $gest_conf->ottieniIndicePerNome($nome_corso);
        }

        // se si è richiesta un'azione, effettuala
        switch ($action) {
            case "elimina-esami":
                // ottieni array esami da rimuovere
                $esami_selezionati = $_POST["esami-selezionati"] ?? [];

                $gest_conf->eliminaEsami($id, $esami_selezionati);
                break;

            case "aggiungi-esame":
                // ottieni codice e nome nuovo esame
                $codice_studente = $_POST["nuovo-codice-studente"] ?? "";
                $codice_esame = $_POST["nuovo-codice-esame"] ?? "";
                $nome_esame = $_POST["nuovo-nome-esame"] ?? "";
                $tipo_esame = $_POST["nuovo-tipo-esame"] ?? "";

                $gest_conf->aggiungiEsame($id, $codice_studente, $codice_esame, $nome_esame, $tipo_esame);
                break;

            case "aggiorna-corso":
                // ottieni opzioni calcolo voto
                $opzioni = [];
                $opzioni["formula-di-voto"] = wp_unslash($_POST["formula-di-voto"] ?? "");
                $opzioni["t-min"] = $_POST["t-min"] ?? "";
                $opzioni["t-max"] = $_POST["t-max"] ?? "";
                $opzioni["t-step"] = $_POST["t-step"] ?? "";
                $opzioni["c-min"] = $_POST["c-min"] ?? "";
                $opzioni["c-max"] = $_POST["c-max"] ?? "";
                $opzioni["c-step"] = $_POST["c-step"] ?? "";
                $opzioni["valore-lode"] = $_POST["valore-lode"] ?? "";
                $opzioni["cfu-richiesti"] = $_POST["cfu-richiesti"] ?? "";
                $opzioni["bonus"] = isset($_POST["bonus"]);
                $opzioni["info-voto-finale"] = $_POST["info-voto-finale"] ?? "";
                $opzioni["itera-t-c"] = $_POST["itera-t-c"] ?? "";

                // ottieni opzioni mail
                $opzioni["oggetto-mail"] = wp_unslash($_POST["oggetto-mail"] ?? "");
                $opzioni["corpo-mail"] = wp_unslash($_POST["corpo-mail"] ?? "");

                $gest_conf->aggiornaCorso($id, $opzioni);
                break;

            case "elimina-corso":
                $gest_conf->eliminaCorso($id);
                break;

            case "aggiungi-corso":
                // ottieni nomi nuovo corso
                $nome_nuovo_corso = $_POST["nome-nuovo-corso"] ?? "";
                $nome_corto_nuovo_corso = $_POST["nome-corto-nuovo-corso"] ?? "";

                $gest_conf->aggiungiCorso($nome_nuovo_corso, $nome_corto_nuovo_corso);
                break;
        }

        $gest_conf->finalizza();
    }

    /*
     * Carica la configurazione corrente, arricchendola con una lista di esami estratti dai filtri per la
     * visualizzazione tabulare.
     */
    private static function ottieniConfigurazione()
    {
        $conf = Configurazione::ottieniConfigurazione();
        $corsi = Configurazione::ottieniCorsi($conf);

        // arricchisci ogni corso con una lista di esami per la visualizzazione tabulare
        foreach ($corsi as $i => $corso) {
            $corsi[$i]->{"esami"} = [];

            foreach ($corsi[$i]->{"filtri"} as $filtro) {
                foreach (["fuori-media" => true, "esclusi" => false] as $campo => $tipo) {
                    foreach ($filtro->{$campo} as $es) {
                        // crea un piccolo oggetto esame che contenga matricola, codice, descrizione e tipo di filtro
                        $esame = new \stdClass();

                        $esame->{"codice-studente"} = $filtro->{"studente"};
                        $esame->{"codice-esame"} = $es->{"COD"};
                        $esame->{"nome-esame"} = $es->{"DES"};
                        $esame->{"tipo"} = $tipo;

                        $corsi[$i]->{"esami"}[] = $esame;
                    }
                }
            }
        }

        return $corsi;
    }
}
