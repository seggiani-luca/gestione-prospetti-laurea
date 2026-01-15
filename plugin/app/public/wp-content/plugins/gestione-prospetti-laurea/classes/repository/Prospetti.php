<?php
namespace GestioneProspettiLaurea\repository;

if (!defined("ABSPATH")) {
    exit();
}

/*
 * Gestisce i percorsi dei prospetti generati dal sistema, all0interno della directory reports/.
 */
class Prospetti
{
    // percorso relativo dei prospetti
    private const PATH_PROSPETTI = "reports/";

    // nome da dare al prospetto commissione
    private const FILE_COMMISSIONE = "commissione.pdf";

    // nome da dare alla tabella laureandi
    private const FILE_TABELLA = "laureandi.txt";

    /*
     * Restituisce il percorso della directory dei prospetti corrispondenti ad un corso e ad una data.
     */
    public static function percorsoDirectoryProspetti($corso)
    {
        return PLUGIN_BASE_PATH . self::PATH_PROSPETTI . $corso . "/";
    }

    /*
     * Restituisce il percorso del prospetto commissione corrispondente ad un corso e ad una data.
     */
    public static function percorsoProspettoCommissione($corso)
    {
        return self::percorsoDirectoryProspetti($corso) . self::FILE_COMMISSIONE;
    }

    /*
     * Restituisce l'URL del prospetto commissione corrispondente ad un corso e ad una data.
     */
    public static function urlProspettoCommissione($corso)
    {
        return SNIPPETS_BASE_URL . "reports/" . $corso . "/" . self::FILE_COMMISSIONE;
    }

    /*
     * Restituisce il percorso del prospetto studente corrispondente ad un corso, una data e una matricola.
     */
    public static function percorsoProspettoStudente($corso, $matricola)
    {
        return self::percorsoDirectoryProspetti($corso) . $matricola . ".pdf";
    }

    /*
     * Restituisce il percorso della tabella laureandi corrispondente ad un corso e ad una data.
     */
    public static function percorsoTabellaLaureandi($corso)
    {
        return self::percorsoDirectoryProspetti($corso) . self::FILE_TABELLA;
    }

    /*
     * Prepara la directory che conterrà i prospetti, creandola se non esiste o ripulendola se esiste. Una volta
     * preparata, ne restituisce il precorso.
     */
    public static function preparaDirectoryProspetti($corso)
    {
        // prepara la directory
        $dest = Prospetti::percorsoDirectoryProspetti($corso);

        if (!is_dir($dest)) {
            mkdir($dest, 0777, true);
        } else {
            array_map("unlink", glob("$dest/*"));
        }

        return $dest;
    }
}
