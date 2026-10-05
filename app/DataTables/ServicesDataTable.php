<?php

namespace App\DataTables;

use App\Models\Service;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;

class ServicesDataTable extends BaseDataTable
{
    /**
     * Build the DataTable class.
     *
     * @param  QueryBuilder<Service>  $query
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('name', function (Service $service): string {
                return view('services.partials.datatable-name', compact('service'))->render();
            })
            ->editColumn('created_at', function (Service $service): string {
                return $service->created_at?->format('M j, Y') ?? '—';
            })
            ->addColumn('action', function (Service $service): string {
                return view('services.partials.datatable-actions', compact('service'))->render();
            })
            ->rawColumns(['name', 'action'])
            ->orderColumn('created_at', 'created_at $1')
            ->filterColumn('name', function (QueryBuilder $query, string $keyword): void {
                $query->where('name', 'like', "%{$keyword}%");
            })
            ->setRowId('id');
    }

    /**
     * @return QueryBuilder<Service>
     */
    public function query(Service $model): QueryBuilder
    {
        return $model->newQuery()->select([
            'id',
            'name',
            'created_at',
        ]);
    }

    public function html(): HtmlBuilder
    {
        return $this->applyDefaults(
            $this->builder()
                ->setTableId('services-table')
                ->columns($this->getColumns())
                ->minifiedAjax(route('admin.services.index'))
                ->orderBy(2, 'desc')
        );
    }

    /**
     * @return array<int, Column>
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')
                ->title('No')
                ->width(50)
                ->addClass('text-center')
                ->searchable(false)
                ->orderable(false)
                ->responsivePriority(4),
            Column::make('name')
                ->title('Service')
                ->name('name')
                ->responsivePriority(1),
            Column::make('created_at')
                ->title('Added')
                ->searchable(false)
                ->responsivePriority(3)
                ->addClass('text-nowrap'),
            Column::computed('action')
                ->title('Action')
                ->exportable(false)
                ->printable(false)
                ->width(120)
                ->addClass('text-end text-nowrap')
                ->searchable(false)
                ->orderable(false)
                ->responsivePriority(2),
        ];
    }

    protected function filename(): string
    {
        return 'Services_'.date('YmdHis');
    }
}
