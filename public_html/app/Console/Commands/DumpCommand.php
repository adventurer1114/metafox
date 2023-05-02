<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use MetaFox\Core\Models\AdminSearch;
use MetaFox\Core\Repositories\AdminSearchRepositoryInterface;
use MetaFox\Menu\Repositories\MenuItemRepositoryInterface;
use MetaFox\User\Models\User;
use MetaFox\User\Repositories\Contracts\UserRepositoryInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\Console\Input\InputOption;

class DumpCommand extends Command
{
    protected ?Spreadsheet $spreadsheet = null;

    protected array $urlFields = ['id', 'app', 'url', 'title'];

    protected array $userFields = ['id', 'name', 'username', 'email', 'password', 'url', 'skip'];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'metafox:dump';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    private Worksheet $worksheet;
    private int $rowIndex = 0;
    private array $fields;
    private int $sheetIndex = 0;

    /**
     * Execute the console command.
     *
     * @return int
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function handle(): int
    {
        $this->spreadsheet = new Spreadsheet();

        $this->dumpUrls();
        $this->dumpAdminUrsl();
        $this->dumpAdminSearch();
        $this->dumpUsers();

        $writer = new Xlsx($this->spreadsheet);
        $writer->save('metafox.xlsx');

        if (is_dir('../wdio')) {
            copy('metafox.xlsx', '../wdio/src/fixtures/metafox.xlsx');
        }

        $this->comment('Updated ' . 'metafox.xlsx');

        return 0;
    }

    protected function addWorkSheet(string $title, array $fields)
    {
        $this->worksheet = new Worksheet($this->spreadsheet, $title);
        $this->worksheet->setTitle($title);
        $this->spreadsheet->addSheet($this->worksheet, $this->sheetIndex);
        $this->sheetIndex = $this->sheetIndex + 1;

        $this->fields = $fields;
        $this->rowIndex = 1;

        foreach ($this->fields as $index => $field) {
            $this->worksheet->setCellValueByColumnAndRow($index + 1, 1, $field);
        }
    }

    protected function addRowToCurrentWorksheet(array $data): void
    {
        $this->rowIndex = $this->rowIndex + 1;
        foreach ($this->fields as $index => $name) {
            $this->worksheet->setCellValueByColumnAndRow($index + 1, $this->rowIndex, $data[$name] ?? null);
        }
    }

    public function dumpAdminUrsl(): void
    {
        $this->addWorkSheet('admin_urls', $this->urlFields);

        $exists = [];

        /** @var \MetaFox\Menu\Models\MenuItem[] $query */
        $query = resolve(MenuItemRepositoryInterface::class)
            ->getModel()
            ->newQuery()
            ->whereNull('as')
            ->where([
                ['is_active', '=', 1],
                ['to', 'like', '/admincp/%'],
            ])
            ->orderBy('to')
            ->cursor();

        foreach ($query as $item) {
            if (isset($exists[$item->to])) {
                continue;
            }
            $exists[$item->to] = true;

            $this->addRowToCurrentWorksheet([
                'id'    => $item->id,
                'name'  => $item->testid || $item->name,
                'url'   => $item->to,
                'app'   => $item->module_id,
                'title' => __p($item->label),
            ]);
        }

        /** @var AdminSearch[] $query */
        $query = resolve(AdminSearchRepositoryInterface::class)
            ->getModel()
            ->newQuery()
            ->cursor();

        foreach ($query as $item) {
            if (isset($exists[$item->to])) {
                continue;
            }
            $exists[$item->to] = true;

            $this->addRowToCurrentWorksheet([
                'id'    => $item->id,
                'name'  => $item->title,
                'url'   => $item->url,
                'title' => $item->caption,
                'app'   => $item->module_id,
            ]);
        }

        $this->info(sprintf("dump %d web urls", $this->rowIndex - 2));
    }

    public function dumpUrls(): void
    {
        $this->addWorkSheet('urls', $this->urlFields);
        $exists = [];

        /** @var \MetaFox\Menu\Models\MenuItem[] $query */
        $query = resolve(MenuItemRepositoryInterface::class)
            ->getModel()
            ->newQuery()
            ->whereNull('as')
            ->whereNull('value')
            ->whereNotNull('to')
            ->whereNot([
                ['is_active', '=', 1],
                ['to', 'like', '/admincp/%'],
            ])
            ->orderBy('to')
            ->cursor();

        foreach ($query as $item) {
            if (isset($exists[$item->to])) {
                continue;
            }
            $exists[$item->to] = true;

            $this->addRowToCurrentWorksheet([
                'id'    => $item->id,
                'name'  => $item->testid || $item->name,
                'url'   => $item->to,
                'app'   => $item->module_id,
                'title' => __p($item->label),
            ]);
        }


        $this->info(sprintf("dump %d admin urls", $this->rowIndex - 2));
    }

    public function dumpUsers(): void
    {
        $this->addWorkSheet('users', $this->userFields);

        /** @var Collection<User> $query */
        $query = resolve(UserRepositoryInterface::class)
            ->getModel()
            ->newQuery()
            ->orderBy('id')
            ->limit(100)->cursor();

        foreach ($query as $user) {
            $this->addRowToCurrentWorksheet([
                'id'       => $user->id,
                'name'     => $user->full_name,
                'username' => $user->user_name,
                'email'    => $user->email,
                'password' => $user->user_name === 'admin' ? 'bubble666' : '123456',
                'url'      => '/' . $user->user_name,
            ]);
        }

        $this->info(sprintf("dump %d users", $this->rowIndex - 2));
    }


    public function dumpAdminSearch(): void
    {
        $exists = [];
        $this->addWorkSheet('admin_search_urls', $this->urlFields);

        /** @var AdminSearch[] $searchs */
        $searchs = resolve(AdminSearchRepositoryInterface::class)
            ->getModel()->newQuery()
            ->orderBy('url')
            ->cursor();

        foreach ($searchs as $item) {
            if (isset($exists[$item->url])) {
                continue;
            }
            $exists[$item->url] = true;

            $this->addRowToCurrentWorksheet([
                'id'    => $item->id,
                'name'  => $item->title,
                'url'   => $item->url,
                'app'   => $item->module_id,
                'title' => $item->title,
            ]);
        }

        $this->info(sprintf("dump %d admin_search_urls", $this->rowIndex - 2));
    }

    protected function getOptions()
    {
        return [
            ['urls', null, InputOption::VALUE_NONE],
            ['lang', null, InputOption::VALUE_NONE],
        ];
    }
}
