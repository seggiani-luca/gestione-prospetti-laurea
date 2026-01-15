<?php
namespace GestioneProspettiLaurea\representation;

if (!defined("ABSPATH")) {
    exit();
}

/*
 * Rappresenta l'anagrafica di uno studente, estratto da GestioneCarrieraStudente.
 */
class Anagrafica
{
    // matricola dello studente
    public readonly string $matricola;

    // nome dello studente
    public readonly string $nome;

    // cognome dello studente
    public readonly string $cognome;

    // email di ateneo dello studente
    public readonly string $mail;

    // anno di immatricolazione dello studente
    public readonly int $anno_immatricolazione;

    // corso dello studente
    public readonly string $corso;

    /*
     * Costruisce l'anagrafica di uno studente prendendone la matricola, nome, cognome, indirizzo e-mail di ateneo, anno
     * di immatricolazione e corso.
     */
    private function __construct($matricola, $nome, $cognome, $mail, $anno_immatricolazione, $corso)
    {
        $this->matricola = $matricola;
        $this->nome = $nome;
        $this->cognome = $cognome;
        $this->mail = $mail;
        $this->anno_immatricolazione = $anno_immatricolazione;
        $this->corso = $corso;
    }

    /*
     * Fabbrica che ottiene l'anagrafica di uno studente a partire dalla matricola e i dati raw ottenuti da
     * GestioneCarrieraStudente.
     */
    public static function daRaw($matricola, $anagrafica_raw, $carriera_raw)
    {
        // ottieni data di immatricolazione
        $anno_immatricolazione = $carriera_raw["Esame"][0]["ANNO_IMM"];

        // ottieni corso studente
        $corso = $carriera_raw["Esame"][0]["CORSO"];

        $anagrafica = new Anagrafica(
            $matricola,
            $anagrafica_raw["nome"],
            $anagrafica_raw["cognome"],
            $anagrafica_raw["email_ate"],
            $anno_immatricolazione,
            $corso,
        );

        return $anagrafica;
    }
}
