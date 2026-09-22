<?php

namespace App\DataTables;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;

class CustomersDataTable extends BaseDataTable
{
    /**
     * Build the DataTable class.
     *
     * @param  QueryBuilder<Customer>  $query
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('name', function (Customer $customer): string {
                return view('customers.partials.datatable-name', compact('customer'))->render();
            })
            ->editColumn('address', function (Customer $customer): string {
                return e($customer->address ?: '—');
            })
            ->editColumn('created_at', function (Customer $customer): string {
                return $customer->created_at?->format('M j, Y') ?? '—';
            })
            ->addColumn('action', function (Customer $customer): string {
                return view('customers.partials.datatable-actions', compact('customer'))->render();
            })
            ->rawColumns(['name', 'action'])
            ->orderColumn('created_at', 'created_at $1')
            ->filterColumn('name', function (QueryBuilder $query, string $keyword): void {
                $query->where('name', 'like', "%{$keyword}%");
            })
            ->setRowId('id');
    }

    /**
     * @return QueryBuilder<Customer>
     */
    public function query(Customer $model): QueryBuilder
    {
        return $model->newQuery()->select([
            'id',
            'name',
            'whatsapp_number',
            'address',
            'created_at',
        ]);
    }

    public function html(): HtmlBuilder
    {
        return $this->applyDefaults(
            $this->builder()
                ->setTableId('customers-table')
                ->columns($this->getColumns())
                ->minifiedAjax(route('admin.customers.index'))
                ->orderBy(4, 'desc')
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
            Column::make('name')
                ->title('Customer')
                ->name('name')
                ->responsivePriority(1),
            Column::make('whatsapp_number')
                ->title('WhatsApp')
                ->responsivePriority(3)
                ->addClass('text-nowrap'),
            Column::make('address')
                ->title('Address')
                ->orderable(false)
                ->responsivePriority(4),
            Column::make('created_at')
                ->title('Added')
                ->searchable(false)
                ->responsivePriority(6)
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
        return 'Customers_'.date('YmdHis');
    }
}
