<?php
namespace GestioneProspettiLaurea\representation;

if (!defined("ABSPATH")) {
    exit();
}

/*
 * Rappresenta un prospetto commissione, cioè un aggregazione di prospetti studente corredata da informazioni
 * sull'appello di laurea (corso e data).
 */
class ProspettoCommissione
{
    // corso dell'appello di laurea
    public readonly string $corso;

    // data dell'appello di laurea
    public readonly string $data;

    // lista dei prospetti studente
    public readonly array $prospetti_studente;

    /*
     * Costruisce un prospetto commissione prendendone il corso, la data dell'appello e la lista dei prospetti studente.
     */
    public function __construct($corso, $data, $prospetti_studente)
    {
        $this->corso = $corso;
        $this->data = $data;
        $this->prospetti_studente = $prospetti_studente;
    }

    /*
     * Ottiene la lista delle anagrafiche di tutti gli studenti dell'appello di laurea.
     */
    public function ottieniStudenti()
    {
        $studenti = [];

        // inserisci le anagrafiche
        foreach ($this->prospetti_studente as $prospetto) {
            $studenti[] = $prospetto->anagrafica;
        }

        return $studenti;
    }
}
