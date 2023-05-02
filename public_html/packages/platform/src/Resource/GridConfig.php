<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Platform\Resource;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Html\BuiltinAdminSearchForm;

/**
 * Class GridConfig.
 *
 * Describe Data grid configuration
 */
class GridConfig extends JsonResource
{
    /**
     * @var GridColumns
     */
    protected GridColumns $columns;

    protected string $appName = '';

    protected string $resourceName = '';

    /***
     * @var BatchActionMenu|null
     */
    protected ?BatchActionMenu $batchActionMenu = null;

    /**
     * @var ItemActionMenu|null
     */
    protected ?ItemActionMenu $itemActionMenu = null;

    /**
     * @var Actions|null
     */
    protected ?Actions $actions = null;

    /***
     * @var array<string,mixed>|null
     */
    protected ?array $dataSource = null;

    /**
     * @var AbstractForm|null
     */
    protected ?AbstractForm $searchForm = null;

    /**
     * @var array<string,mixed>
     */
    protected array $attributes = [
        'rowHeight' => 48,
    ];

    /**
     * GridConfig constructor.
     *
     * @param string $resource
     */
    public function __construct($resource)
    {
        parent::__construct($resource);

        $this->columns = new GridColumns();

        if ($this->appName && $this->resourceName) {
            $this->setDataSource(
                apiUrl(sprintf('admin.%s.%s.index', $this->appName, $this->resourceName)),
                ['q' => ':q']
            );
        }

        $this->initialize();
    }

    protected function initialize(): void
    {
    }

    public function setDefaultDataSource()
    {
    }

    /**
     * Enable inline search fields.
     *
     * @param string[] $inlineSearch
     *
     * @return $this
     */
    public function inlineSearch(array $inlineSearch): self
    {
        return $this->setAttribute('inlineSearch', $inlineSearch);
    }

    /**
     * Add a checkbox section on the first cell of each row.
     *
     * @param bool $enabled
     *
     * @return $this
     */
    public function enableCheckboxSelection(bool $enabled = true): self
    {
        return $this->setAttribute('checkboxSelection', $enabled);
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function setAttribute(string $name, mixed $value): self
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * @param string $title
     *
     * @return GridConfig
     */
    public function title(string $title): self
    {
        return $this->setAttribute('title', $title);
    }

    /**
     * @param AbstractForm|null $searchForm
     */
    public function setSearchForm(?AbstractForm $searchForm): void
    {
        $this->searchForm = $searchForm;
    }

    /**
     * @param  int   $rowsPerPage
     * @param  array $options
     * @return $this
     */
    public function setRowsPerPage(int $rowsPerPage, array $options = []): static
    {
        $this->setAttribute('rowsPerPage', $rowsPerPage);
        $this->setAttribute('rowsPerPageOptions', $options);

        return $this;
    }

    /**
     * @return $this
     */
    public function useAdminSearchForm(): self
    {
        $this->searchForm = new BuiltinAdminSearchForm();

        return $this;
    }

    /**
     * Assign data source.
     *
     * @param string         $apiUrl
     * @param array|string[] $apiParams
     * @param array          $apiRules
     *
     * @return $this
     */
    public function setDataSource(
        string $apiUrl,
        array $apiParams = ['q' => ':q'],
        array $apiRules = ['q' => ['truthy', 'q']]
    ): self {
        return $this->setAttribute('dataSource', [
            'apiUrl'    => $apiUrl,
            'apiParams' => $apiParams,
            'apiRules'  => $apiRules,
        ]);
    }

    /**
     * Add new column by field.
     *
     * @param string $field
     *
     * @return GridColumn
     */
    protected function addColumn(string $field): GridColumn
    {
        return $this->columns->add($field);
    }

    /**
     * Handle columns process.
     *
     * @param Closure $closure
     */
    public function withColumns(Closure $closure): void
    {
        $closure($this->columns);
    }

    /**
     * @param Closure $closure
     */
    public function withBatchMenu(Closure $closure): void
    {
        if (!$this->batchActionMenu) {
            $this->batchActionMenu = new BatchActionMenu();
        }

        $closure($this->batchActionMenu);
    }

    /**
     * Assign actions
     * <code>
     * $grid->withItemMenu(function(ItemActionMenu $menu){
     *   $menu->add()
     * })
     * </code>.
     *
     * @param Closure $closure
     */
    public function withItemMenu(Closure $closure): void
    {
        if (!$this->itemActionMenu) {
            $this->itemActionMenu = new ItemActionMenu();
        }
        $closure($this->itemActionMenu);
    }

    /**
     * Assign actions
     * <code>
     * $grid->withAction(function(Actions $actions){
     *   $actions->add('editItem')->url();
     * })
     * </code>.
     *
     * @param Closure $closure
     */
    public function withActions(Closure $closure): void
    {
        if (!$this->actions) {
            $this->actions = new Actions($this->appName, $this->resourceName);
        }
        $closure($this->actions);
    }

    /**
     * @param Request $request
     *
     * @return array<string,mixed>
     */
    public function toArray($request): array
    {
        if ($this->actions) {
            $this->attributes['actions'] = $this->actions->toArray();
        }

        if ($this->itemActionMenu) {
            $this->attributes['itemActionMenu'] = $this->itemActionMenu->toArray();
        }

        if ($this->batchActionMenu) {
            $this->attributes['batchActionMenu'] = $this->batchActionMenu->toArray();
        }

        $this->attributes['columns'] = $this->columns->toArray();

        if ($this->searchForm) {
            $this->attributes['searchForm'] = $this->searchForm->toArray($request);
        }

        return $this->attributes;
    }

    public function rowHeight(int $rowHeight): static
    {
        $this->setAttribute('rowHeight', $rowHeight);

        return $this;
    }

    /**
     * @param  bool  $value
     * @return $this
     */
    public function sortable(bool $value = true): self
    {
        return $this->setAttribute('sortable', $value);
    }

    /**
     * @return $this
     *               set free height to skip react-window
     */
    public function dynamicRowHeight(): self
    {
        return $this->setAttribute('dynamicRowHeight', true);
    }

    /**
     * @param  bool  $value
     * @return $this
     */
    public function searchFormPlacement(string $location = 'top'): self
    {
        return $this->setAttribute('searchFormPlacement', $location);
    }
}
