<?php

namespace App\DataTables;

use App\Models\JobCard;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;

class JobCardsDataTable extends BaseDataTable
{
    /**
     * Build the DataTable class.
     *
     * @param  QueryBuilder<JobCard>  $query
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('customer', function (JobCard $jobCard): string {
                return view('job-cards.partials.datatable-customer', compact('jobCard'))->render();
            })
            ->editColumn('date', function (JobCard $jobCard): string {
                return $jobCard->date?->format('M j, Y') ?? '—';
            })
            ->addColumn('vehicle', function (JobCard $jobCard): string {
                return e($jobCard->vehicle?->optionLabel() ?? '—');
            })
            ->editColumn('grand_total', function (JobCard $jobCard): string {
                return e(number_format((float) $jobCard->grand_total, 2));
            })
            ->editColumn('status', function (JobCard $jobCard): string {
                return view('job-cards.partials.status-select', compact('jobCard'))->render();
            })
            ->addColumn('action', function (JobCard $jobCard): string {
                return view('job-cards.partials.datatable-actions', compact('jobCard'))->render();
            })
            ->rawColumns(['customer', 'status', 'action'])
            ->orderColumn('date', 'job_cards.date $1')
            ->orderColumn('grand_total', 'job_cards.grand_total $1')
            ->filterColumn('customer', function (QueryBuilder $query, string $keyword): void {
                $query->whereHas('customer', function (QueryBuilder $query) use ($keyword): void {
                    $query->where('name', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('vehicle', function (QueryBuilder $query, string $keyword): void {
                $query->whereHas('vehicle', function (QueryBuilder $query) use ($keyword): void {
                    $query->where('vehicle_number', 'like', "%{$keyword}%")
                        ->orWhere('vehicle_model', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('status', function (QueryBuilder $query, string $keyword): void {
                $query->where('job_cards.status', 'like', "%{$keyword}%");
            })
            ->setRowId('id');
    }

    /**
     * @return QueryBuilder<JobCard>
     */
    public function query(JobCard $model): QueryBuilder
    {
        return $model->newQuery()
            ->with([
                'customer:id,name',
                'vehicle:id,vehicle_number,vehicle_model',
            ])
            ->listing(JobCard::listingFilters(request()))
            ->select('job_cards.*');
    }

    public function html(): HtmlBuilder
    {
        return $this->applyDefaults(
            $this->builder()
                ->setTableId('job-cards-table')
                ->columns($this->getColumns())
                ->minifiedAjax(
                    route('admin.job-cards.index'),
                    'var filterForm = document.getElementById("job-card-filter-form"); if (filterForm) { new FormData(filterForm).forEach(function (value, key) { data[key] = value; }); }',
                )
                ->orderBy(3, 'desc')
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
                ->responsivePriority(5),
            Column::computed('customer')
                ->title('Customer')
                ->name('customer')
                ->orderable(false)
                ->responsivePriority(1),
            Column::computed('vehicle')
                ->title('Vehicle')
                ->name('vehicle')
                ->orderable(false)
                ->responsivePriority(2),
            Column::make('date')
                ->title('Date')
                ->searchable(false)
                ->responsivePriority(3)
                ->addClass('text-nowrap'),
            Column::make('grand_total')
                ->title('Grand Total')
                ->searchable(false)
                ->responsivePriority(4)
                ->addClass('text-end text-nowrap'),
            Column::make('status')
                ->title('Status')
                ->responsivePriority(2)
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
        return 'JobCards_'.date('YmdHis');
    }
}
