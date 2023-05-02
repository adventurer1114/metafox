<?php

namespace MetaFox\Group\Support\Browse\Scopes\Group;

use MetaFox\Platform\Support\Browse\Scopes\PrivacyScope as Main;

class PrivacyScope extends Main
{
    /**
     * @var string|null
     */
    protected $view;

    public function setView(?string $view = null): void
    {
        $this->view = $view;
    }

    public function getView(): ?string
    {
        return $this->view;
    }

    protected function hasPrivacyMemberScope(): bool
    {
        $view = $this->getView();

        return !in_array($view, [ViewScope::VIEW_INVITED]);
    }
}
