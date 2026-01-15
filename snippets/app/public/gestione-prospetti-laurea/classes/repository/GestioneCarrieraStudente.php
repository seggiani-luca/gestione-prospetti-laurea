<?php
namespace GestioneProspettiLaurea\repository;

if (!defined("ABSPATH")) {
    exit();
}

/*
 * Simulazione del sistema GestioneCarrieraStudente, che provvede a fornire anagrafica e carriere di studenti in base
 * alla matricola fornita. Queste vengono caricate dai dati in JSON in data/.
 */
class GestioneCarrieraStudente
{
    // percorso relativo dei dati
    private const PATH_DATI = "data/";

    // suffisso per i JSON delle anagrafiche
    private const SUF_ANAGRAFICA = "_anagrafica.json";

    // suffisso per i JSON delle carriere
    private const SUF_CARRIERE = "_esami.json";

    /*
     * Restituisce l'anagrafica di uno studente, specificata la matricola.
     */
    public static function restituisciAnagraficaStudente($matricola)
    {
        $path = SNIPPETS_BASE_PATH . self::PATH_DATI . $matricola . self::SUF_ANAGRAFICA;
        return JSON::leggiJSON($path, true);
    }

    /*
     * Restituisce l'carriera di uno studente, specificata la matricola.
     */
    public static function restituisciCarrieraStudente($matricola)
    {
        $path = SNIPPETS_BASE_PATH . self::PATH_DATI . $matricola . self::SUF_CARRIERE;
        return JSON::leggiJSON($path, true);
    }
}
