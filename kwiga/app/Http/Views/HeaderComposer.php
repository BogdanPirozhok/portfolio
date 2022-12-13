<?php

namespace App\Http\Views;

use App\Services\Cabinet\CabinetService;
use Exception;
use Illuminate\View\View;

class HeaderComposer
{

    /**
     * ProfileComposer constructor.
     * @param CabinetService $cabinetService
     */
    public function __construct(private readonly CabinetService $cabinetService)
    {
    }

    /**
     * Bind data to the view.
     *
     * @return void
     * @throws Exception
     */
    public function compose(View $view)
    {
        $subDomain = cabinet();
        $subDomainUrl = optional(cabinet())->link;
        $activeCabinet = optional($this->cabinetService->getActiveCabinet())->getSlug() ?? null;

        $view->with(compact([
            'subDomain',
            'subDomainUrl',
            'activeCabinet',
        ]));
    }
}
