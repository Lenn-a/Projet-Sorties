<?php

namespace App\Services;

use App\Form\Model\OutingSearch;
use App\Repository\OutingRepository;

class OutingSearchService
{
    public function __construct(private OutingRepository $outingRepository) {
        $this->outingRepository = $outingRepository;
    }

    public function refineOutingSearch(OutingSearch $outingSearch) {
        if ($outingSearch->getCampus()) {
            $this->outingRepository->findOutingsByCampus($outingSearch->getCampus());
        }
    }


}
