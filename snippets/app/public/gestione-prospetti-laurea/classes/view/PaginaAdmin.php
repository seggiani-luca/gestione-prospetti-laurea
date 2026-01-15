<?php
namespace GestioneProspettiLaurea\view;

use GestioneProspettiLaurea\repository\Configurazione;
use GestioneProspettiLaurea\services\GestisciConfigurazione;

if (!defined("ABSPATH")) {
    exit();
}

/*
 * Classe boundary usata per definire le pagine per la modifica della configurazione. In particolare si occupa di
 * redirezionare alla per la modifica della configurazione e gestirne le richieste POST.
 */
class PaginaAdmin
{

    /*
     * Mostra la pagina vera e propria, gestendo eventuali richieste post, e ottenendo la configurazione corrente.
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
        include SNIPPETS_BASE_PATH . "/views/admin.php";
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
