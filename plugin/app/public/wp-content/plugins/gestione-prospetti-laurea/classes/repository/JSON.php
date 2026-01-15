<?php
namespace GestioneProspettiLaurea\repository;

if (!defined("ABSPATH")) {
    exit();
}

/*
 * Classe helper che standardizza la logica di accesso ai JSON.
 */
class JSON
{
    /*
     * Restituisce i dati in un JSON ad un dato percorso. L'argomento array specifica se il JSON va letto come array
     * associativa o come oggetto.
     */
    public static function leggiJSON($path, $array = false)
    {
        // il valore di errore è null
        if (!file_exists($path)) {
            return null;
        }

        // carica dati dal JSON
        $json = file_get_contents($path);
        if ($json === false) {
            throw new \Exception("Impossibile leggere il file JSON a " . self::$path);
        }

        // decodifica il JSON
        $dati = json_decode($json, $array);
        if ($dati === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("JSON non valido in " . $path);
        }

        return $dati;
    }

    /*
     * Scrive i dati forniti in un JSON ad un dato percorso.
     */
    public static function scriviJSON($path, $dati)
    {
        // codifica il JSON
        $json = json_encode($dati, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            throw new \Exception("Impossibile codificare JSON");
        }

        // salva i dati nel file
        if (file_put_contents($path, $json) === false) {
            throw new \Exception("Errore durante il salvataggio del file JSON a " . $path);
        }
    }
}
