<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Menu\Http\Resources\v1\MenuItem\Admin;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;

class SearchMenuItemForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->action('/admincp/menu');
    }

    protected function initialize(): void
    {
        $this->addBasic(['variant' => 'horizontal'])
            ->asHorizontal()
            ->addFields(
                Builder::text('q')
                    ->forAdminSearchForm(),
                Builder::submit()
                    ->forAdminSearchForm()
            );
    }
}
