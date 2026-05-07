<?php

namespace App\DataTables\Projects;


use App\Models\Projects\ProjectModel;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;


class ProjectsDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $dataTable = new EloquentDataTable($query);

        // return (new EloquentDataTable($query))
        //     ->addColumn('action', function($row){
        //         return '<form method="POST" action="'.route('project.delete', $row->id).'">
        //                     '.csrf_field().'
        //                     <input type="hidden" name="_method" value="DELETE">
        //                     <div class="dropdown">
        //                         <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
        //                             <i class="mdi mdi-dots-vertical"></i>
        //                         </button>
        //                         <div class="dropdown-menu">
        //                             <a class="dropdown-item" href="'.route('project.edit', $row->id).'">
        //                                 <i class="mdi mdi-pencil-outline me-1"></i>
        //                                 Edit
        //                             </a>
        //                             <button class="dropdown-item" type="submit" onclick="javascript:if( !confirm(\'Are you sure delete this project?\') ) return false;">
        //                                 <i class="mdi mdi-trash-can-outline me-1"></i>
        //                                 Delete
        //                             </button>
        //                         </div>
        //                     </div>
        //                 </form>';
        //     })
        if (!in_array(auth()->user()->role, [4, 8])) {
            $dataTable->addColumn('action', function($row){
                return '<form method="POST" action="'.route('project.delete', $row->id).'">
                            '.csrf_field().'
                            <input type="hidden" name="_method" value="DELETE">
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="mdi mdi-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="'.route('project.edit', $row->id).'">
                                        <i class="mdi mdi-pencil-outline me-1"></i>
                                        Edit
                                    </a>
                                    <button class="dropdown-item" type="submit" onclick="javascript:if( !confirm(\'Are you sure delete this project?\') ) return false;">
                                        <i class="mdi mdi-trash-can-outline me-1"></i>
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </form>';
            });
        }
    
        return $dataTable
            ->addColumn('name', function($row){
                return '<a href="'.route('project.details', $row->id).'">'.$row->project_name.'</a>';
            })
            ->addColumn('created_at', function($row){
                return $row->created_at->format('jS M, Y');
            })
            ->addColumn('client', function($row){
                return $row->client()->count() > 0 ? $row->client()->first()->full_name : '';
            })
            ->addColumn('assigns', function($row){
                if(in_array(auth()->user()->role, [8, 4])) {
                    $assigns = $row->assigns()->where('user_id', auth()->id())->get();
                } else {
                    $assigns = $row->assigns()->get();
                }                    

                if($assigns->count() > 0){
                    $firstAssign = $assigns->first();
                    $remainingAssigns = $assigns->slice(1);

                    $tooltip = '';
                    if ($remainingAssigns->count() > 0) {
                        $tooltipContent = $remainingAssigns->map(fn($assign) => e($assign->first_name . ' ' . $assign->last_name))->implode('<br>'); 
                        $tooltip = $tooltipContent 
                            ? ' <i class="mdi mdi-dots-horizontal-circle text-primary ms-1" style="cursor:pointer;" 
                                    data-bs-toggle="tooltip" 
                                    data-bs-placement="right" 
                                    data-bs-html="true" 
                                    title="'.$tooltipContent.'"></i>'
                            : '';
                    }
                    $padding = $tooltip ? 'style="padding: 0px 6px;"' : '';
                    return '<span class="badge rounded-pill bg-label-primary role-'.$firstAssign->role.'" ' . $padding . '>'
                        . e($firstAssign->first_name . ' ' . $firstAssign->last_name) .
                        $tooltip .
                       '</span>';
                }else{
                    return '';
                }
            })
            ->rawColumns(array_merge(
                ['name', 'assigns'], 
                !in_array(auth()->user()->role, [4, 8]) ? ['action'] : []
            ))
            ->setRowId('id')

            // filter columns
            ->filterColumn('name', function($query, $keyword) {
                $query->where('projects.project_name', 'like', '%'.$keyword.'%');
            })
            ->filterColumn('assigns', function($query, $keyword){
                $query->where('users.first_name', 'like', '%'.$keyword.'%')->orWhere('users.last_name', 'like', '%'.$keyword.'%');
            })
            ->filterColumn('client', function($query, $keyword){
                $query->where('clients.first_name', 'like', '%'.$keyword.'%')->orWhere('clients.last_name', 'like', '%'.$keyword.'%');
            })

            // order columns
            ->orderColumn('created_at', function ($query, $order) {
                $query->orderBy('projects.created_at', $order);
            })
            ->orderColumn('name', function ($query, $order) {
                $query->orderBy('projects.project_name', $order);
            })
            ->orderColumn('client', function ($query, $order) {
                $query->orderBy('clients.first_name', $order)->orderBy('clients.last_name', $order);
            });
    }

    public function query(ProjectModel $model): QueryBuilder
    {
        return $model->newQuery()->where(function($query){
            if(auth()->user()->role != 1){
                $query->where('project_assigns.user_id', auth()->user()->id);
            }
        })
        ->leftJoin('project_assigns', 'project_assigns.project_id', '=', 'projects.id')
        ->leftJoin('clients', 'clients.id', '=', 'projects.client_id')
        ->leftJoin('users', 'users.id', '=', 'project_assigns.user_id')
        ->select('projects.*')
        ->groupBy('projects.id');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('projects-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->deferRender()
                    ->dom('<"row"<"col-md-12 mb-4"Br><"col-md-6 mb-4"l><"col-md-6 mb-4"f>><"row"<"col-md-12 mb-4"<"table-responsive"t>>><"row"<"col-md-6 mb-4"i><"col-md-6 mb-4"p>>')
                    ->selectStyleSingle()
                    ->parameters([
                        'drawCallback' => 'function() { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }',
                        'searchDelay' => 500,
                        'buttons' =>  [
                            [
                                'extend' => 'collection',
                                'text' => __('Export'),
                                'buttons'   => [
                                    [
                                        'extend' => 'csvHtml5',
                                        'text' => __('CSV'),
                                        'filename' => $this->filename(),
                                        'exportOptions' => [
                                            'modifier' => ['selected' => true],
                                            'columns' => [0,1,2,3]
                                        ]
                                    ],
                                    [
                                        'extend' => 'excelHtml5',
                                        'text' => __('Excel'),
                                        'filename' => $this->filename(),
                                        'title' => '',
                                        'exportOptions' => [
                                            'modifier' => ['selected' => true],
                                            'columns' => [0,1,2,3]
                                        ]
                                    ],
                                    [
                                        'extend' => 'pdfHtml5',
                                        'text' => __('PDF'),
                                        'filename' => $this->filename(),
                                        'title' => '',
                                        'exportOptions' => [
                                            'modifier' => ['selected' => true],
                                            'columns' => [0,1,2,3]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'order' => [3, 'desc']
                    ]);
    }

    public function getColumns(): array
    {
        $columns = [
            Column::make('name')
                ->title('Project Name'),
            Column::make('assigns')
                ->addClass('text-center')
                ->orderable(false)
                ->title('Assigns'),
            Column::make('client')
                ->title('Client'),
            Column::make('created_at')
                ->title('Project Created'),
        ];
    
        // Only add the Action column if the user is NOT role 4 or 8
        if (! in_array(auth()->user()->role, [4, 8])) {
            $columns[] = Column::computed('action')
                ->searchable(false)
                ->orderable(false)
                ->exportable(false)
                ->printable(false)
                ->addClass('text-center')
                ->title('Action');
        }
    
        return $columns;
    }

    protected function filename(): string
    {
        return 'Projects on -'.date('YmdHis').'-'.config('app.name');
    }
}