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


class AdminProjectsDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', function($row){
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
            })
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
                if($row->assigns()->count() > 0){
                    return $row->assigns()->get()->map(function($assign){
                        return '<span class="badge rounded-pill bg-label-primary mb-2 role-'.$assign->role.'">'.$assign->first_name.' '.$assign->last_name.'</span>';
                    })->implode('<br>');
                }else{
                    return '';
                }
            })
            ->rawColumns(['action', 'name', 'assigns'])
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

    public function getColumns(): array
    {
        return [
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
            Column::computed('action')
                ->searchable(false)
                ->orderable(false)
                ->exportable(false)
                ->printable(false)
                ->addClass('text-center')
                ->title('Action'),
        ];
    }
}