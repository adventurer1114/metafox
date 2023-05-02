<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Page\Observers;

use MetaFox\Page\Models\PageMember;

class PageMemberObserver
{
    public function created(PageMember $model)
    {
        $model->page->incrementAmount('total_member');
    }

    public function deleted(PageMember $model)
    {
        $model->page->decrementAmount('total_member');
    }
}
