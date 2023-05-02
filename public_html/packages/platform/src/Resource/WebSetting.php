<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Platform\Resource;

/**
 * Class WebSetting.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.NumberOfChildren )
 */
class WebSetting extends Actions
{
    protected function initialize(): void
    {
    }

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        $this->initialize();

        return parent::toArray();
    }
}
