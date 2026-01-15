<?php
namespace GestioneProspettiLaurea\services;

use GestioneProspettiLaurea\repository\Prospetti;

if (!defined("ABSPATH")) {
    exit();
}

/*
 * Implementa il caso d'uso CreaProspetti, e quindi si occupa di gestire la visualizzazione dei prospetti creati
 * offrendoli come allegati di tipo PDF al browser.
 */
class VisualizzaProspetti
{
    /*
     * Visualizza il prospetto commissione relativo ad un appello di laurea, individuato da corso di laurea
     * dell'appello.
     */
    public function visualizzaProspetti($corso)
    {
        if (empty($corso)) {
            throw new \Exception("Fornite opzioni invalide per visualizzazione prospetti");
        }

        // ottieni percorso prospetto commissione
        $path = Prospetti::percorsoProspettoCommissione($corso);
        $url = Prospetti::urlProspettoCommissione($corso);

        if (!file_exists($path)) {
            throw new \Exception("Cercando di visualizzare prospetti relativi ad un appello di laurea inesistente");
        }

        // invialo al browser
        echo '<script>window.open("' . esc_js($url) . '", "_blank");</script>';
    }
}
