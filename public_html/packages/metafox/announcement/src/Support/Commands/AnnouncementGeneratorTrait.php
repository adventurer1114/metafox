<?php

namespace MetaFox\Announcement\Support\Commands;

use Illuminate\Console\Command;
use MetaFox\Announcement\Database\Factories\AnnouncementFactory;
use MetaFox\Announcement\Models\Announcement;
use MetaFox\Announcement\Models\Style;
use MetaFox\Platform\Contracts\User;

/**
 * Class AnnouncementGenerator.
 * @mixin Command
 * @codeCoverageIgnore
 * @ignore
 */
trait AnnouncementGeneratorTrait
{
    /**
     * @param  User                 $user
     * @param  array<string, mixed> $parameters
     * @return Announcement
     */
    public function createAnnouncement(User $user, array $parameters = []): Announcement
    {
        /** @var Style $style */
        $style = Style::all()->shuffle()->first();

        return AnnouncementFactory::new($parameters)
            ->setUser($user)
            ->setStyle($style)
            ->create()
            ->refresh();
    }
}
