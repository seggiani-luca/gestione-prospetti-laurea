<?php
namespace GestioneProspettiLaurea\services;

use GestioneProspettiLaurea\representation\ProspettoCommissione;
use GestioneProspettiLaurea\representation\ProspettoStudente;
use GestioneProspettiLaurea\representation\Anagrafica;
use GestioneProspettiLaurea\representation\Esame;

use GestioneProspettiLaurea\repository\Prospetti;
use GestioneProspettiLaurea\repository\GestioneCarrieraStudente;
use GestioneProspettiLaurea\repository\Configurazione;

if (!defined("ABSPATH")) {
    exit();
}

/*
 * Implementa il caso d'uso CreaProspetti, e quindi si occupa di gestire la creazione dei prospetti interrogando
 * GestioneCarrieraStudente, creando appropiate classi di rappresentazione (ProspettoCommissione, che incapsula
 * ProspettoStudente, Anagrafica e Esame), e renderizzandole via FPDF sfruttando la classe di servizio DisegnaProspetti.
 */
class CreaProspetti
{
    /*
     * Crea il prospetto relativo all'appello di laurea rappresentato da corso, data e matricole dei laureandi.
     * Restituisce il percorso a cui è stato creato il prospetto.
     */
    public function creaProspetti($corso, $data, $matricole)
    {
        if (empty($corso) || empty($data) || empty($matricole)) {
            throw new \Exception("Fornite opzioni invalide per creazione prospetti");
        }

        // ottieni la configurazione del corso
        $conf = Configurazione::ottieniConfigurazione();
        $conf_corso = Configurazione::ottieniConfigurazioneCorso($conf, $corso);

        // crea il prospetto commissione
        $prospetto_commissione = $this->ottieniProspettoCommissione($matricole, $corso, $data, $conf_corso);

        // per scopi di debug, stampa il prospetto generato
        // $prospetto_enc = json_encode($prospetto_commissione, JSON_PRETTY_PRINT);
        // echo "<pre>" . $prospetto_enc . "</pre>";

        // prepara directory prospetti
        $dest = Prospetti::preparaDirectoryProspetti($corso);

        // inizializza disegnatore prospetti
        $disegna_prospetti = new DisegnaProspetti($prospetto_commissione, $conf_corso);

        // genera prospetto commissione
        $path_commissione = Prospetti::percorsoProspettoCommissione($corso, $data);
        $disegna_prospetti->generaProspettoCommissione($path_commissione);

        // genera i prospetti laureandi
        $disegna_prospetti->generaProspettiStudente(
            fn($matricola) => Prospetti::percorsoProspettoStudente($corso, $matricola),
        );

        // crea la tabella laureandi
        $tab_laureandi = Prospetti::percorsoTabellaLaureandi($corso, $data);
        $this->creaTabellaLaureandi($prospetto_commissione, $tab_laureandi);

        return $dest;
    }

    /*
     * Helper che ottiene il prospetto di commissione relativo all'appello di laurea rappresentato da corso, data e
     * matricole dei laureandi.
     */
    public function ottieniProspettoCommissione($matricole, $corso, $data, $conf)
    {
        // ottieni i prospetti studente
        $prospetti_studente = [];
        foreach ($matricole as $matricola) {
            // ottieni anagrafica raw da GestioneCarrieraStudente
            $anagrafica_raw = GestioneCarrieraStudente::restituisciAnagraficaStudente($matricola);
            if ($anagrafica_raw == null) {
                throw new \Exception("Prospetti non creati: anagrafica matricola " . $matricola . " inesistente");
            }

            // ottieni carriera raw da GestioneCarrieraStudente
            $carriera_raw = GestioneCarrieraStudente::restituisciCarrieraStudente($matricola);
            if ($carriera_raw == null) {
                throw new \Exception("Prospetti non creati: carriera matricola " . $matricola . " inesistente");
            }

            // interpreta i dati in un prospetto studente
            $prospetto_studente = ProspettoStudente::daRaw(
                $matricola,
                $anagrafica_raw,
                $carriera_raw,
                $corso,
                $data,
                $conf,
            );

            // effettua la simulazione di voto
            $simula_voto = new SimulaVoto($prospetto_studente, $conf);
            $prospetto_studente->simulazione = $simula_voto->ottieniSimulazioneVoto();

            $prospetti_studente[] = $prospetto_studente;
        }

        // crea il prospetto commissione
        $prospetto_commissione = new ProspettoCommissione(Configurazione::nomeCompleto($conf), $data, 
            $prospetti_studente, $conf);

        return $prospetto_commissione;
    }

    /*
     * Crea un file temporaneo, cioè la tabella laureandi, che contiene la lista dei laureandi con e loro e-mail di
     * ateneo e il prospetto studente ancora da inviare.
     */
    private function creaTabellaLaureandi($prospetto_commissione, $tab)
    {
        // ottieni dati
        $corso = $prospetto_commissione->corso;
        $data = $prospetto_commissione->data;

        $entrate = "";

        // genera tabella
        foreach ($prospetto_commissione->prospetti_studente as $prospetto_studente) {
            $matricola = $prospetto_studente->anagrafica->matricola;
            $mail = $prospetto_studente->anagrafica->mail;

            $entrate .=
                $matricola .
                " " .
                $mail .
                " " .
                " \"" .
                Prospetti::percorsoProspettoStudente($corso, $matricola) .
                "\"\n";
        }

        // scrivi tabella
        file_put_contents($tab, $entrate);
    }
}
