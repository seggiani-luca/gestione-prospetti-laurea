<?php
namespace GestioneProspettiLaurea\tests;

use GestioneProspettiLaurea\services\CreaProspetti;
use GestioneProspettiLaurea\repository\Configurazione;
use GestioneProspettiLaurea\repository\JSON;

if (!defined("ABSPATH")) {
    exit();
}

class TestCreaProspetti
{
    public const PATH_TEST = "tests/crea_prospetti.json";

    private array $tests;

    public function __construct()
    {
        $this->tests = JSON::leggiJSON(PLUGIN_BASE_PATH . self::PATH_TEST, false)->{"tests"};
    }

    public function testCreaProspetti()
    {
        echo "---- Test CreaProspetti ----\n";

        for ($i = 0; $i < count($this->tests); $i++) {
            $test = $this->tests[$i];

            echo "Test " . $i . ": ";

            $in = $test->{"in"};
            $out = $test->{"out"};

            // ottieni configurazione
            $conf = Configurazione::ottieniConfigurazione();
            $conf_corso = Configurazione::ottieniConfigurazioneCorso($conf, $in->{"corso"});

            // ottieni dati
            $crea_prospetti = new CreaProspetti();
            $prospetto_commissione = $crea_prospetti->ottieniProspettoCommissione(
                $in->{"matricole"},
                $in->{"corso"},
                $in->{"data"},
                $conf_corso,
            );

            if (($out === null) & ($prospetto_commissione !== null)) {
                echo "\n\tProspetti attesi null, ma non null";
                continue;
            }

            for ($j = 0; $j < count($out->{"prospetti-studente"}); $j++) {
                $act = $prospetto_commissione->prospetti_studente[$j];
                $exp = $out->{"prospetti-studente"}[$j];

                echo "\n\tProspetto studente $j:\n";

                // --- bonus ---
                if ($act->bonus !== $exp->bonus) {
                    echo "\t\tbonus errato: act={$act->bonus} exp={$exp->bonus}\n";
                }

                // --- carriera ---
                $act_car = $act->carriera;
                $exp_car = $exp->{"carriera"};

                if (count($act_car) !== count($exp_car)) {
                    echo "\t\tLunghezza carriera errata: act=" . count($act_car) . " exp=" . count($exp_car) . "\n";
                    continue;
                }

                for ($j = 0; $j < count($exp_car); $j++) {
                    $a = $act_car[$j];
                    $e = $exp_car[$j];

                    if ($a->codice !== $e->codice) {
                        echo "\t\tesame[$j].codice errato: act={$a->codice} exp={$e->codice}\n";
                    }
                    if ($a->cfu !== $e->cfu) {
                        echo "\t\tesame[$j].cfu errato: act={$a->cfu} exp={$e->cfu}\n";
                    }
                    if ($a->voto !== $e->voto) {
                        echo "\t\tesame[$j].voto errato: act={$a->voto} exp={$e->voto}\n";
                    }
                    if ($a->in_media !== $e->in_media) {
                        echo "\t\tesame[$j].in_media errato: act=" .
                            var_export($a->in_media, true) .
                            " exp=" .
                            var_export($e->in_media, true) .
                            "\n";
                    }
                    if ($a->in_sottomedia !== $e->in_sottomedia) {
                        echo "\t\tesame[$j].in_sottomedia errato: act=" .
                            var_export($a->in_sottomedia, true) .
                            " exp=" .
                            var_export($e->in_sottomedia, true) .
                            "\n";
                    }
                }

                // --- media-pesata ---
                if (abs($act->ottieniMediaPesata() - $exp->{"media-pesata"}) > 0.0005) {
                    echo "\t\tmedia-pesata errato: act={$act->ottieniMediaPesata()} exp={$exp->{"media-pesata"}}\n";
                }

                // --- cfu-in-media ---
                if ($act->ottieniCFU() !== $exp->{"cfu-in-media"}) {
                    echo "\t\tcfu-in-media errato: act={$act->ottieniCFU()} exp={$exp->{"cfu-in-media"}}\n";
                }

                // --- cfu-conseguiti ---
                if ($act->ottieniCFU(true) !== $exp->{"cfu-conseguiti"}) {
                    echo "\t\tcfu-conseguiti errato: act={$act->ottieniCFU(true)} exp={$exp->{"cfu-conseguiti"}}\n";
                }

                // --- voto-tesi ---
                if (0 !== $exp->{"voto-tesi"}) {
                    echo "\t\tvoto-tesi mismatch: act={$act->{"voto-tesi"}} exp={$exp->{"voto-tesi"}}\n";
                }

                // --- sottomedia ---
                if (abs($act->ottieniMediaPesata(true) - $exp->{"sottomedia"}) > 0.0005) {
                    echo "\t\tsottomedia errato: act={$act->ottieniMediaPesata(true)} exp={$exp->{"sottomedia"}}\n";
                }

                // --- simulazione ---
                $act_sim = $act->simulazione;
                $exp_sim = $exp->{"simulazione"};

                if (count($act_sim) !== count($exp_sim)) {
                    echo "\t\tLunghezza simulazione errata: act=" . count($act_sim) . " exp=" . count($exp_sim) . "\n";
                    continue;
                }

                for ($j = 0; $j < count($exp_sim); $j++) {
                    $a = $act_sim[$j];
                    $e = $exp_sim[$j];

                    if ($a->iter !== $e->iter) {
                        echo "\t\tsimulazione[$j].iter errato: act={$a->iter} exp={$e->iter}\n";
                    }
                    if (abs($a->voto - $e->voto) > 0.0005) {
                        echo "\t\tsimulazione[$j].voto errato: act={$a->voto} exp={$e->voto}\n";
                    }
                }

                echo "\t\tFine test.\n";
            }
        }
    }
}
