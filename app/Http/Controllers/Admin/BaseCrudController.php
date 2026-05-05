<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

/**
 * Base controller every admin CRUD module extends.
 *
 * Subclasses configure:
 *   - $modelClass   FQN of the Eloquent model
 *   - $routeSlug    plural snake-case slug used in route names ("branches")
 *   - $singularKey  i18n key for singular ("admin.resources.branch")
 *   - $pluralKey    i18n key for plural ("admin.resources.branches")
 *   - $columns      ['column' => 'Header'] (header text is i18n-rendered if it starts with 'admin.')
 *   - $with         relations to eager-load
 *   - $searchable   columns to LIKE against on Yajra search
 *   - fields()      form fields (CrudField[])
 *   - rules()       validation rules
 *
 * Provides:
 *   - index()         server-rendered shell that mounts the React DataTable
 *   - data()          Yajra JSON endpoint
 *   - create/store/edit/update/destroy/show
 *
 * Hooks subclasses may override:
 *   - beforeSave($data, $request, $record = null)
 *   - afterSave($record, $request)
 *   - applyExtraFilters(Builder $q, Request $r)
 *   - rowActions(Model $row): string
 */
abstract class BaseCrudController extends Controller
{
    protected string $modelClass;

    protected string $routeSlug;

    protected string $singularKey;

    protected string $pluralKey;

    /** @var array<string,string> column key => header (i18n key or literal). */
    protected array $columns = [];

    protected array $with = [];

    protected array $searchable = [];

    protected string $defaultSort = 'id';

    protected string $defaultDir = 'desc';

    protected string $viewPath = 'admin._crud';

    abstract protected function fields(): array;

    abstract protected function rules(?Model $record = null): array;

    public function index()
    {
        return view($this->indexView(), [
            'columns' => $this->columns,
            'meta' => $this->meta(),
            'dataUrl' => route("admin.{$this->routeSlug}.data"),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $query = $this->modelClass::query()->with($this->with);
        $this->applyExtraFilters($query, $request);

        return DataTables::eloquent($query)
            ->addColumn('action_buttons', fn (Model $row) => $this->rowActions($row))
            ->addColumn('row_index', function ($row) {
                static $i = 0;

                return ++$i;
            })
            ->editColumn('created_at', fn (Model $row) => optional($row->created_at)->format('Y-m-d H:i'))
            ->editColumn('updated_at', fn (Model $row) => optional($row->updated_at)->format('Y-m-d H:i'))
            ->filter(function (Builder $q) use ($request) {
                if (! $request->filled('search.value') && ! $request->filled('q')) {
                    return;
                }
                $term = '%'.($request->input('search.value') ?: $request->input('q')).'%';
                if (! $this->searchable) {
                    return;
                }
                $q->where(function (Builder $sub) use ($term) {
                    foreach ($this->searchable as $col) {
                        $sub->orWhere($col, 'like', $term);
                    }
                });
            }, true)
            ->rawColumns($this->rawColumns())
            ->toJson();
    }

    public function create()
    {
        $record = new $this->modelClass;

        return view($this->formView(), [
            'record' => $record,
            'fields' => $this->fields(),
            'mode' => 'create',
            'meta' => $this->meta(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateInput($request);
        $record = $this->modelClass::create($this->beforeSave($data, $request));
        $this->afterSave($record, $request);
        flash()->success(__('admin.flash.created', ['name' => __($this->singularKey)]));

        return redirect()->route("admin.{$this->routeSlug}.index");
    }

    public function show($key)
    {
        $record = $this->resolveModel($key, true);

        return view($this->showView(), [
            'record' => $record,
            'fields' => $this->fields(),
            'meta' => $this->meta(),
        ]);
    }

    public function edit($key)
    {
        $record = $this->resolveModel($key);

        return view($this->formView(), [
            'record' => $record,
            'fields' => $this->fields(),
            'mode' => 'edit',
            'meta' => $this->meta(),
        ]);
    }

    public function update(Request $request, $key)
    {
        $record = $this->resolveModel($key);
        $data = $this->validateInput($request, $record);
        $record->update($this->beforeSave($data, $request, $record));
        $this->afterSave($record, $request);
        flash()->success(__('admin.flash.updated', ['name' => __($this->singularKey)]));

        return redirect()->route("admin.{$this->routeSlug}.index");
    }

    public function destroy($key)
    {
        $record = $this->resolveModel($key);
        $record->delete();
        flash()->success(__('admin.flash.deleted', ['name' => __($this->singularKey)]));

        return redirect()->route("admin.{$this->routeSlug}.index");
    }

    /**
     * Resolve the model from a URL segment, supporting both numeric IDs
     * and string keys (slug, code, etc) without depending on route
     * model binding so subclasses can keep their public route key as `slug`.
     */
    protected function resolveModel($key, bool $eager = false): Model
    {
        $query = $this->modelClass::query();
        if ($eager && $this->with) {
            $query->with($this->with);
        }
        $instance = new $this->modelClass;
        $routeKey = $instance->getRouteKeyName();

        if (is_numeric($key)) {
            return $query->whereKey($key)->firstOrFail();
        }

        return $query->where($routeKey, $key)->firstOrFail();
    }

    /* -----------------------------------------------------------------
     | Hooks
     *----------------------------------------------------------------*/

    protected function validateInput(Request $request, ?Model $record = null): array
    {
        $data = $request->validate($this->rules($record));

        // Auto-cast checkbox / boolean fields based on form schema.
        foreach ($this->fields() as $field) {
            if ($field->type === 'checkbox') {
                $data[$field->name] = (bool) ($data[$field->name] ?? false);
            }
        }

        return $data;
    }

    protected function beforeSave(array $data, Request $request, ?Model $record = null): array
    {
        return $data;
    }

    protected function afterSave(Model $record, Request $request): void
    {
        // override
    }

    protected function applyExtraFilters(Builder $query, Request $request): void
    {
        // override to add module-specific filters
    }

    protected function rowActions(Model $row): string
    {
        $base = "admin.{$this->routeSlug}";
        $buttons = '<div class="btn-group btn-group-sm">';
        if (\Route::has("$base.show")) {
            $buttons .= '<a href="'.route("$base.show", $row).'" class="btn btn-outline-info" data-i18n-attr-title="admin.actions.view"><i class="bi bi-eye"></i></a>';
        }
        if (\Route::has("$base.edit")) {
            $buttons .= '<a href="'.route("$base.edit", $row).'" class="btn btn-outline-warning" data-i18n-attr-title="admin.actions.edit"><i class="bi bi-pencil"></i></a>';
        }
        if (\Route::has("$base.destroy")) {
            $url = route("$base.destroy", $row);
            $csrf = csrf_token();
            $token = method_field('DELETE');
            $buttons .= <<<HTML
<form method="POST" action="{$url}" data-confirm-delete class="d-inline">
    <input type="hidden" name="_token" value="{$csrf}">
    {$token}
    <button type="submit" class="btn btn-outline-danger" data-i18n-attr-title="admin.actions.delete"><i class="bi bi-trash"></i></button>
</form>
HTML;
        }
        $buttons .= '</div>';

        return $buttons;
    }

    protected function rawColumns(): array
    {
        return ['action_buttons'];
    }

    protected function meta(): array
    {
        return [
            'singular' => __($this->singularKey),
            'plural' => __($this->pluralKey),
            'singular_key' => $this->singularKey,
            'plural_key' => $this->pluralKey,
            'route_slug' => $this->routeSlug,
            'searchable' => ! empty($this->searchable),
        ];
    }

    protected function indexView(): string
    {
        return view()->exists("admin.{$this->routeSlug}.index")
            ? "admin.{$this->routeSlug}.index"
            : "{$this->viewPath}.index";
    }

    protected function formView(): string
    {
        return view()->exists("admin.{$this->routeSlug}.form")
            ? "admin.{$this->routeSlug}.form"
            : "{$this->viewPath}.form";
    }

    protected function showView(): string
    {
        return view()->exists("admin.{$this->routeSlug}.show")
            ? "admin.{$this->routeSlug}.show"
            : "{$this->viewPath}.show";
    }
}
