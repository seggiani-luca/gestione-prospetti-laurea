<?php
namespace GestioneProspettiLaurea\representation;

if (!defined("ABSPATH")) {
    exit();
}
/*
 * Una carriera è intesa come un array di istanze di Esame. Questa classe contiene fabbriche per l'inizializzazione di
 * tali array estratte da GestioneCarrieraStudente.
 */
class Carriera
{
    /*
     * Fabbrica che ottiene la carriera di uno studente a partire dai dati raw ottenuti da GestioneCarrieraStudente.
     */
    public static function daRaw($carriera_raw, $anagrafica, $bonus, $conf)
    {
        // inizializza array esami
        $carriera = [];

        // popolala con gli esami
        foreach ($carriera_raw["Esame"] as $esame_raw) {
            // solo se passati
            if (!Esame::isPassato($esame_raw)) {
                continue;
            }

            // aggiungi esame
            $esame = Esame::daRaw($esame_raw, $conf);
            $carriera[] = $esame;
        }

        // ordina carriera per data
        usort($carriera, function ($a, $b) {
            return $a->data <=> $b->data;
        });

        // filtra carriera
        $carriera = self::filtraCarriera($carriera, $anagrafica, $bonus, $conf);

        return $carriera;
    }

    /*
     * Filtra la carriera di uno studente, eliminando gli esami esclusi, e inserendo i campi "in-media" e
     * "in-sottomedia".
     */
    private static function filtraCarriera($carriera, $anagrafica, $bonus, $conf)
    {
        // ottieni filtri
        $filtri = $conf->{"filtri"};
        $sottomedia = $conf->{"calcolo-voto"}->{"esami-sottomedia"};

        // filtra carriera eliminando gli esami esclusi
        $carriera = array_values(
            array_filter($carriera, function ($esame) use ($filtri, $anagrafica) {
                return self::inLista($esame, $anagrafica, $filtri, false);
            }),
        );

        // aggiorna campo "in-media": se è filtrato dalla media impostalo a falso
        foreach ($carriera as $i => $esame) {
            $carriera[$i]->in_media &= self::inMedia($esame, $anagrafica, $filtri, true);
        }

        // aggiorna campo "in-sottomedia"
        foreach ($carriera as $i => $esame) {
            $carriera[$i]->in_sottomedia = self::inSottomedia($esame, $sottomedia);
        }

        // se è previsto il bonus, rimuovi l'esame peggiore
        if (count($carriera) > 1 && $bonus) {
            $voto_min = $carriera[0]->voto;
            $cfu_min = $carriera[0]->cfu;
            $indice_min = 0;

            // trova l'esame peggiore
            foreach ($carriera as $i => $esame) {
                if (!$esame->in_media) {
                    continue;
                }

                if ($esame->voto < $voto_min || ($esame->voto == $voto_min && $esame->cfu > $cfu_min)) {
                    $voto_min = $esame->voto;
                    $cfu_min = $esame->cfu;
                    $indice_min = $i;
                }
            }

            // rimuovilo dalla media
            $carriera[$indice_min]->in_media = false;
        }

        return $carriera;
    }

    /*
     * Helper per il filtraggio degli esami. $controlla_media determina se si controlla la presenza dell'esame nella
     * media. Sotto si definiscono due helper dai nomi semanticamente più appropiati.
     */
    private static function filtra($esame, $anagrafica, $filtri, $controlla_media)
    {
        foreach ($filtri as $filtro) {
            // solo filtri relativi a questo studente
            if ($filtro->{"studente"} != "*" && $filtro->{"studente"} != $anagrafica->{"matricola"}) {
                continue;
            }

            // controlla sempre se escluso
            foreach ($filtro->{"esclusi"} as $esame_filtro) {
                if ($esame_filtro->{"COD"} == $esame->{"codice"}) {
                    return false;
                }
            }

            // controlla se fuori media
            if ($controlla_media) {
                foreach ($filtro->{"fuori-media"} as $esame_filtro) {
                    if ($esame_filtro->{"COD"} == $esame->{"codice"}) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /*
     * Helper che valuta se un esame è in lista (non è escluso).
     */
    private static function inLista($esame, $anagrafica, $filtri)
    {
        return self::filtra($esame, $anagrafica, $filtri, false);
    }

    /*
     * Helper che valuta se un esame è in media.
     */
    private static function inMedia($esame, $anagrafica, $filtri)
    {
        return self::filtra($esame, $anagrafica, $filtri, true);
    }

    /*
     * Helper che valuta se un esame è in sottomedia.
     */
    private static function inSottomedia($esame, $sottomedia)
    {
        foreach ($sottomedia as $esame_sottomedia) {
            if ($esame_sottomedia->{"COD"} == $esame->{"codice"}) {
                return true;
            }
        }

        return false;
    }
}
