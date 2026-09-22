<?php

namespace App\DataTables;

use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Services\DataTable;

/**
 * Shared defaults for all admin DataTables.
 * Extend this class in each module (Customers, Vehicles, etc.).
 */
abstract class BaseDataTable extends DataTable
{
    /**
     * Default Bootstrap 5 layout: length (left) | reload + search (right) / table / info | pagination.
     */
    protected function defaultDom(): string
    {
        return "<'row align-items-center g-2 mb-3 dt-toolbar'"
            ."<'col-sm-12 col-md-6'l>"
            ."<'col-sm-12 col-md-6 dt-toolbar-right'Bf>"
            .'>'
            ."<'row'<'col-12'tr>>"
            ."<'row align-items-center g-2 mt-2 dt-footer'"
            ."<'col-sm-12 col-md-5'i>"
            ."<'col-sm-12 col-md-7'p>"
            .'>';
    }

    /**
     * Shared DataTables options used across modules.
     *
     * @return array<string, mixed>
     */
    protected function defaultParameters(): array
    {
        return [
            'pageLength' => 10,
            'lengthMenu' => [[10, 25, 50, 100], [10, 25, 50, 100]],
            'responsive' => true,
            'autoWidth' => false,
            'processing' => true,
            'serverSide' => true,
            'language' => [
                'lengthMenu' => '_MENU_ Entries Per Page',
                'search' => '',
                'searchPlaceholder' => 'Search...',
                'info' => 'Showing _START_ to _END_ of _TOTAL_ entries',
                'infoEmpty' => 'Showing 0 to 0 of 0 entries',
                'infoFiltered' => '(filtered from _MAX_ total entries)',
                'zeroRecords' => 'No matching records found',
                'emptyTable' => 'No data available in table',
                'processing' => 'Loading...',
                'paginate' => [
                    'first' => '&laquo;',
                    'last' => '&raquo;',
                    'next' => '&rsaquo;',
                    'previous' => '&lsaquo;',
                ],
            ],
        ];
    }

    /**
     * Apply shared HTML builder defaults.
     */
    protected function applyDefaults(HtmlBuilder $builder): HtmlBuilder
    {
        return $builder
            ->dom($this->defaultDom())
            ->parameters($this->defaultParameters())
            ->buttons($this->defaultButtons());
    }

    /**
     * Default toolbar buttons (reload). Override per module if needed.
     *
     * @return array<int, Button>
     */
    protected function defaultButtons(): array
    {
        return [
            Button::make('reload')
                ->className('btn btn-sm btn-soft-warning dt-btn-reload')
                ->text('<i class="bi bi-arrow-clockwise"></i>'),
        ];
    }
}
