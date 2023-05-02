<?php

namespace MetaFox\Comment\Database\Importers;

use Illuminate\Support\Arr;
use MetaFox\Platform\Support\JsonImporter;
use MetaFox\Comment\Models\Comment as Model;

/*
 * stub: packages/database/json-importer.stub
 */

class CommentImporter extends JsonImporter
{
    protected array $requiredColumns = [
        'user_id', 'owner_id', 'item_id',
        'user_type', 'owner_type', 'item_type',
    ];

    public function processImport()
    {
        $this->remapRefs([
            '$user', '$owner', '$item', '$parent',
        ]);

        $this->remapEmoji();

        $this->processImportEntries();

        $this->upsertBatchEntriesInChunked(Model::class, ['id']);
    }

    protected function processImportEntry(array &$entry): void
    {
        $this->addEntryToBatch(Model::class, [
            'id'              => $entry['$oid'] ?? null,
            'user_id'         => $entry['user_id'] ?? null,
            'user_type'       => $entry['user_type'] ?? null,
            'owner_id'        => $entry['owner_id'] ?? $entry['user_id'],
            'owner_type'      => $entry['owner_type'] ?? $entry['user_type'],
            'item_id'         => $entry['item_id'] ?? null,
            'item_type'       => $entry['item_type'] ?? null,
            'module_id'       => $entry['module_id'] ?? null,
            'package_id'      => $entry['package_id'] ?? null,
            'parent_id'       => $entry['parent_id'] ?? 0,
            'is_approved'     => $entry['is_approved'] ?? 1,
            'is_spam'         => $entry['is_spam'] ?? 0,
            'total_like'      => $entry['total_like'] ?? 0,
            'total_comment'   => $entry['total_comment'] ?? 0,
            'text'            => $entry['text'] ?? '',
            'text_parsed'     => html_entity_decode($entry['text_parsed'] ?? ''),
            'updated_at'      => $entry['updated_at'] ?? null,
            'created_at'      => $entry['created_at'] ?? null,
            'tagged_user_ids' => $this->handleTaggedUser($entry),
        ]);
    }

    private function handleTaggedUser(array $data): ?string
    {
        $usersRef = Arr::get($data, 'tagged_user_ids', []);
        if (empty($usersRef)) {
            return null;
        }

        $userIds = $this->getEntryRepository()
            ->getModel()
            ->newQuery()
            ->whereIn('ref_id', $usersRef)
            ->get('resource_id')
            ->whereNotNull('resource_id')
            ->toArray();

        $userIds = array_map(function ($item) {
            return $item['resource_id'];
        }, $userIds);

        return json_encode(array_values($userIds));
    }

    public function remapEmoji(): void
    {
        $list    = $this->getListEmoji();
        $pattern = $this->getRegexPattern($list);

        foreach ($this->entries as &$entry) {
            $text       = $this->handleEmoji($list, $pattern, $entry['text'] ?? '');
            $textParsed = $this->handleEmoji($list, $pattern, $entry['text_parsed'] ?? '');

            $entry['text']        = $text;
            $entry['text_parsed'] = $textParsed;
        }
    }

    public function handleEmoji(array $list, string $pattern, string $text): string
    {
        return preg_replace_callback('/' . $pattern . '/', function ($match) use ($list) {
            if (isset($list[$match[0]])) {
                return json_decode('"' . $list[$match[0]] . '"');
            }

            return $match[0];
        }, $text);
    }

    private function getListEmoji(): array
    {
        return [
            '(waving)'         => '\uD83D\uDC4B',
            '(OK)'             => '\uD83D\uDC4C',
            '(y)'              => '\uD83D\uDC4D',
            '(n)'              => '\uD83D\uDC4E',
            '(clap)'           => '\uD83D\uDC4F',
            '(smiling)'        => '\uD83D\uDE0A',
            '(savoring)'       => '\uD83D\uDE0B',
            '(relieved)'       => '\uD83D\uDE0C',
            '(hearteyes)'      => '\uD83D\uDE0D',
            '(cool)'           => '\uD83D\uDE0E',
            '(smirking)'       => '\uD83D\uDE0F',
            '(kiss)'           => '\uD83D\uDE1A',
            ':P'               => '\uD83D\uDE0B',
            '(disappointed)'   => '\uD83D\uDE1E',
            ':S'               => '\uD83D\uDE1F',
            '(sleepy)'         => '\uD83D\uDE2A',
            '(cryingface)'     => '\uD83D\uDE22',
            ';('               => '\uD83D\uDE2D',
            ':O'               => '\uD83D\uDE32',
            '(handshake)'      => '\uD83E\uDD1D',
            '(rockon)'         => '\uD83E\uDD1F',
            '(zany)'           => '\uD83D\uDE1C',
            '(shush)'          => '\uD83E\uDD2B',
            '(chuckle)'        => '\uD83E\uDD2D',
            '(puke)'           => '\uD83E\uDD2E',
            '(brokenheart)'    => '\uD83D\uDC94',
            ':D'               => '\uD83D\uDE00',
            '(beaming)'        => '\uD83D\uDE01',
            '(tearofjoys)'     => '\uD83D\uDE02',
            '(smilingeyes)'    => '\uD83D\uDE04',
            '(sweat)'          => '\uD83D\uDE05',
            '(squint)'         => '\uD83D\uDE06',
            '(angel)'          => '\uD83D\uDE07',
            '(devil)'          => '\uD83D\uDE08',
            ';)'               => '\uD83D\uDE09',
            ':|'               => '\uD83D\uDE10',
            '(expressionless)' => '\uD83D\uDE11',
            '(unamused)'       => '\uD83D\uDE12',
            '(downcast)'       => '\uD83D\uDE13',
            ':('               => '\uD83D\uDE41',
            ':-/'              => '\uD83D\uDE15',
            '(confounded)'     => '\uD83D\uDE16',
            '(kissingface)'    => '\uD83D\uDE17',
            ':-*'              => '\uD83D\uDE18',
            '(angry)'          => '\uD83D\uDE20',
            '(pounting)'       => '\uD83D\uDE21',
            '(persevering)'    => '\uD83D\uDE23',
            '(steamnose)'      => '\uD83D\uDE24',
            '(anguished)'      => '\uD83D\uDE27',
            '(fearful)'        => '\uD83D\uDE28',
            '(weary)'          => '\uD83D\uDE29',
            '(anxious)'        => '\uD83D\uDE30',
            '(scream)'         => '\uD83D\uDE31',
            '(sleep)'          => '\uD83D\uDE34',
            '(dizzy)'          => '\uD83D\uDE35',
            '(emptymouth)'     => '\uD83D\uDE36',
            '(medicalmask)'    => '\uD83D\uDE37',
            '(frown)'          => '\uD83D\uDE26',
            ':)'               => '\uD83D\uDE42',
            '(upsidedown)'     => '\uD83D\uDE43',
            '(rollingeyes)'    => '\uD83D\uDE44',
            '(zipper)'         => '\uD83E\uDD10',
            '(moneymouth)'     => '\uD83E\uDD11',
            '(sick)'           => '\uD83E\uDD12',
            '(nerd)'           => '\uD83E\uDD13',
            '(think)'          => '\uD83E\uDD14',
            '(injure)'         => '\uD83E\uDD15',
            '(hug)'            => '\uD83E\uDD17',
            '(nauseated)'      => '\uD83E\uDD22',
            '(drooling)'       => '\uD83E\uDD24',
            '(lie)'            => '\uD83E\uDD25',
            '(sneeze)'         => '\uD83E\uDD27',
            '(starstruck)'     => '\uD83E\uDD29',
            '(star)'           => '\uD83C\uDF1F',
            '(victory)'        => '\u270C',
            '(heart)'          => '\u2764',
        ];
    }

    private function getRegexPattern(array $list): string
    {
        $str = implode('[REGEX_KEY]', array_keys($list));

        $str = str_replace(['(', ')', '*', '-', '/', '|'], ['\(', '\)', '\*', '\-', '\/', '\|'], $str);

        return str_replace('[REGEX_KEY]', '|', $str);
    }
}
