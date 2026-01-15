<?php
namespace GestioneProspettiLaurea\services;

use GestioneProspettiLaurea\repository\Configurazione;

if (!defined("ABSPATH")) {
    exit();
}

/*
 * Implementa il caso d'uso GestisciConfigurazione, e quindi si occupa di mantenere una classe di configurazione,
 * permetterne la modifica e la finalizzazione tramite salvataggio su JSON.
 */
class GestisciConfigurazione
{
    // classe di configurazione corrente
    private $conf;

    /*
     * Costruttore, inizializza con la classe di configurazione corrente.
     */
    public function __construct()
    {
        $this->conf = Configurazione::ottieniConfigurazione();
    }

    /*
     * Helper che ottiene l'indice del corso con un certo nome.
     */
    public function ottieniIndicePerNome($nome_corso)
    {
        $corsi = Configurazione::ottieniCorsi($this->conf);

        // cerca il corso richiesto fra quelli definiti
        if (!empty($nome_corso)) {
            foreach ($corsi as $i => $corso_di_laurea) {
                if ($corso_di_laurea->{"nome-corto"} == $nome_corso) {
                    // restituisci l'indice fra i corsi
                    return $i;
                }
            }
        }

        // se non hai trovato nulla, è errore
        throw new \Exception("Nessun corso con nome " . $nome_corso);
    }

    /*
     * Elimina un insieme di esami dai filtri della configurazione.
     */
    public function eliminaEsami($id, $esami_selezionati)
    {
        // ottieni il corso
        $corso = &$this->conf->{"corsi-di-laurea"}[$id];

        // ottieni lista esami da eliminare
        $da_eliminare = [];
        foreach ($esami_selezionati as $esame_sel) {
            [$studente, $codice, $tipo] = explode("|", $esame_sel);
            $da_eliminare[$studente][$tipo][$codice] = true;
        }

        // cicla ed elimina esami
        foreach ($corso->{"filtri"} as $filtro) {
            foreach (["fuori-media", "esclusi"] as $campo) {
                $studente = $filtro->{"studente"};

                if (isset($da_eliminare[$studente][$campo])) {
                    // elimina l'esame
                    $filtro->{$campo} = array_values(
                        array_filter(
                            $filtro->{$campo},
                            fn($esame) => !isset($da_eliminare[$studente][$campo][$esame->COD]),
                        ),
                    );
                }
            }
        }

        // ripulisci matricole vuote
        foreach ($corso->{"filtri"} as $i => $filtro) {
            if (count($filtro->{"fuori-media"}) == 0 && count($filtro->{"esclusi"}) == 0) {
                unset($corso->{"filtri"}[$i]);
            }
        }

        $corso->{"filtri"} = array_values($corso->{"filtri"});
    }

    /*
     * Aggiunge un esame ai filtri della configurazione.
     */
    public function aggiungiEsame($id, $studente, $COD, $DES, $in_media)
    {
        // se il codice o il nome sono vuoti, non aggiungere
        if (empty($studente) || empty($COD) || empty($DES)) {
            return;
        }

        // ottieni il corso
        $corso = &$this->conf->{"corsi-di-laurea"}[$id];

        // controlla se il filtro esiste
        $filtro_sel = null;
        $filtri = &$corso->{"filtri"};

        foreach ($filtri as $i => $filtro) {
            if ($filtro->{"studente"} == $studente) {
                $filtro_sel = &$filtri[$i];
                break;
            }
        }

        // se non esiste crealo
        if ($filtro_sel === null) {
            $filtro_sel = new \stdClass();
            $filtro_sel->{"studente"} = $studente;
            $filtro_sel->{"fuori-media"} = [];
            $filtro_sel->{"esclusi"} = [];

            $filtri[] = $filtro_sel;
            $filtroIndex = array_key_last($filtri);
            $filtro_sel = &$filtri[$filtroIndex];
        }

        // crea il nuovo esame
        $nuovo_esame = new \stdClass();

        $nuovo_esame->{"COD"} = $COD;
        $nuovo_esame->{"DES"} = $DES;

        // aggiungi l'esame al corso
        if ($in_media) {
            $filtro_sel->{"fuori-media"}[] = $nuovo_esame;
        } else {
            $filtro_sel->{"esclusi"}[] = $nuovo_esame;
        }
    }

    /*
     * Aggiorna le informazioni di un corso (principalmente riguardo a calcolo medie e testo mail).
     */
    public function aggiornaCorso($id, $opzioni)
    {
        // ottieni il corso
        $corso = &$this->conf->{"corsi-di-laurea"}[$id];

        // aggiorna configurazione calcolo
        $calcolo = &$corso->{"calcolo-voto"};

        $calcolo->{"formula-di-voto"} = $opzioni["formula-di-voto"];
        $calcolo->{"t-min"} = (int) $opzioni["t-min"];
        $calcolo->{"t-max"} = (int) $opzioni["t-max"];
        $calcolo->{"t-step"} = (int) $opzioni["t-step"];
        $calcolo->{"c-min"} = (int) $opzioni["c-min"];
        $calcolo->{"c-max"} = (int) $opzioni["c-max"];
        $calcolo->{"c-step"} = (int) $opzioni["c-step"];
        $calcolo->{"valore-lode"} = (int) $opzioni["valore-lode"];
        $calcolo->{"cfu-richiesti"} = (int) $opzioni["cfu-richiesti"];
        $calcolo->{"bonus"} = (bool) $opzioni["bonus"];
        $calcolo->{"info-voto-finale"} = $opzioni["info-voto-finale"];
        $calcolo->{"itera-t-c"} = (bool) $opzioni["itera-t-c"];

        // aggiorna configurazione mail
        $mail = &$corso->{"mail"};
        $mail->{"oggetto"} = $opzioni["oggetto-mail"];
        $mail->{"corpo"} = $opzioni["corpo-mail"];
    }

    /*
     * Elimina un intero corso.
     */
    public function eliminaCorso($id)
    {
        // elimina il corso
        unset($this->conf->{"corsi-di-laurea"}[$id]);

        $this->conf->{"corsi-di-laurea"} = array_values($this->conf->{"corsi-di-laurea"});
    }

    /*
     * Aggiunge un nuovo corso, popolandolo con informazioni di default.
     */
    public function aggiungiCorso($nome_nuovo_corso, $nome_corto_nuovo_corso)
    {
        // se il nome è vuoto, non aggiungere
        if (empty($nome_nuovo_corso) || empty($nome_corto_nuovo_corso)) {
            return;
        }

        // crea il nuovo corso
        $nuovo_corso = clone $this->conf->{"corso-di-laurea-default"};
        $nuovo_corso->{"nome"} = $nome_nuovo_corso;
        $nuovo_corso->{"nome-corto"} =  $nome_corto_nuovo_corso;

        $this->conf->{"corsi-di-laurea"}[] = $nuovo_corso;
    }

    /*
     * Finalizza le modifiche effettuate, salvando la configurazione su JSON.
     */
    public function finalizza()
    {
        Configurazione::salvaConfigurazione($this->conf);
    }
}
