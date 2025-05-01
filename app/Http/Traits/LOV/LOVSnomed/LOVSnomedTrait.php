<?php

namespace App\Http\Traits\LOV\LOVSnomed;

use App\Http\Traits\SATUSEHAT\SnomedTrait;

trait LOVSnomedTrait
{
    use SnomedTrait;

    public array $dataSnomedLov = [];
    public int $dataSnomedLovStatus = 0;
    public string $dataSnomedLovSearch = '';
    public int $selecteddataSnomedLovIndex = 0;
    public array $collectingMySnomed = [];

    public array $LOVParent = [
        [
            'field'      => 'lovRJKeluhanUtama',
            // hanya cari clinical findings untuk chief complaint
            'valueSetId' => 'condition-code',
        ],
        [
            'field'      => 'lovRJRiwayatPenyakitSekarangUmum',
            // riwayat penyakit lama juga termasuk finding
            'valueSetId' => 'condition-code',
        ],
        [
            'field'      => 'lovRJRiwayatPenyakitDahulu',
            // riwayat penyakit lama juga termasuk finding
            'valueSetId' => 'condition-code',
        ],
        [
            'field'      => 'lovRJAlergi',
            // untuk alergi, pakai daftar substansi
            'valueSetId' => 'allergyintolerance-code',
        ],
    ];
    public string $LOVParentStatus = '';

    /////////////////////////////////////////////////
    // Lov dataSnomedLov //////////////////////
    ////////////////////////////////////////////////

    public function updateddataSnomedLovsearch()
    {

        // Reset index of LoV
        $this->reset(['selecteddataSnomedLovIndex', 'dataSnomedLov']);
        // Variable Search
        $search = $this->dataSnomedLovSearch;
        $this->initializeTxFhir();

        $dataSnomedLov = $this->getSnomedConcept($search);
        if (!empty($dataSnomedLov['display'])) {
            // set Snomed sep
            $this->addSnomed(
                $dataSnomedLov['code'],
                $dataSnomedLov['display']
            );

            $this->resetdataSnomedLov();
        } else {

            // if there is no id found and check (min 1 char on search)
            if (strlen($search) < 3) {
                $this->dataSnomedLov = [];
            } else {
                $this->initializeTxFhir();
                $lovConfig = collect($this->LOVParent)->firstWhere('field', $this->LOVParentStatus);
                $valueSetId = $lovConfig['valueSetId'] ?? null;
                $this->dataSnomedLov = $this->searchSnomedConcepts($search, 10, $valueSetId);
            }
            $this->dataSnomedLovStatus = true;
            // set doing nothing
        }
    }
    // /////////////////////
    // LOV selected start
    public function setMydataSnomedLov($id)
    {
        if (isset($this->dataSnomedLov[$id]['code'])) {
            $this->addSnomed(
                $this->dataSnomedLov[$id]['code'],          // snomedCode
                $this->dataSnomedLov[$id]['display'],        // snomedDisplay
            );

            $this->resetdataSnomedLov();
        } else {
            $this->emit('toastr-error', "Kode belum tersedia.");
            return;
        }
    }

    public function resetdataSnomedLov()
    {
        $this->reset(['dataSnomedLov', 'dataSnomedLovStatus', 'dataSnomedLovSearch', 'selecteddataSnomedLovIndex', 'LOVParentStatus',]);
    }

    public function selectNextdataSnomedLov()
    {
        if ($this->selecteddataSnomedLovIndex === "") {
            $this->selecteddataSnomedLovIndex = 0;
        } else {
            $this->selecteddataSnomedLovIndex++;
        }

        if ($this->selecteddataSnomedLovIndex === count($this->dataSnomedLov)) {
            $this->selecteddataSnomedLovIndex = 0;
        }
    }

    public function selectPreviousdataSnomedLov()
    {

        if ($this->selecteddataSnomedLovIndex === "") {
            $this->selecteddataSnomedLovIndex = count($this->dataSnomedLov) - 1;
        } else {
            $this->selecteddataSnomedLovIndex--;
        }

        if ($this->selecteddataSnomedLovIndex === -1) {
            $this->selecteddataSnomedLovIndex = count($this->dataSnomedLov) - 1;
        }
    }

    public function enterMydataSnomedLov($id)
    {
        // dd($this->dataSnomedLov);
        // $this->checkRjStatus();
        // jika JK belum siap maka toaster error
        if (isset($this->dataSnomedLov[$id]['code'])) {
            $this->addSnomed(
                $this->dataSnomedLov[$id]['code'],          // snomedCode
                $this->dataSnomedLov[$id]['display'],        // snomedDisplay
            );

            $this->resetdataSnomedLov();
        } else {
            $this->emit('toastr-error', "Kode belum tersedia.");
            return;
        }
    }


    private function addSnomed($SnomedCode, $SnomedDisplay): void
    {
        $this->collectingMySnomed = [
            'SnomedCode'         => $SnomedCode,
            'SnomedDisplay'       => $SnomedDisplay,
        ];
        $this->emit($this->LOVParentStatus, $SnomedCode, $SnomedDisplay);
    }



    // LOV selected end
    /////////////////////////////////////////////////
    // Lov dataSnomedLov //////////////////////
    ////////////////////////////////////////////////
}
