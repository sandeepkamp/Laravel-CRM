<?php

namespace App\DataTables\Projects;

use App\Models\Projects\ProjectTasksModel;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Services\DataTable;
use App\Models\Projects\ProjectMilestoneModel;

class ClientsMilestoneDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
                ->addColumn('milestone_name', function($row){
                    return '<a href="'.route('clients.project-milestone-detail', [$row->project_id, $row->id]).'">'.ucwords($row->milestone_name).'</a>';
                })
                ->addColumn('project_name', function($row){
                    return '<a href="'.route('clients.projects-details', [$row->project_id]).'">'.ucwords($row->projectModel->project_name).'</a>';
                })
                ->addColumn('description', function($row){
                    $description = $row->description;
                    if (strlen($description) > 50) {
                        return '<span>' . substr($description, 0, 15) . '...</span>
                                <a href="javascript:void(0)" class="text-primary read-more" data-description="' . htmlspecialchars($description) . '" title="Read More">Read More</a>';
                    } else {
                        return $description;
                    }
                })
                ->addColumn('created_at', fn($row) => $row->created_at->format('jS M, Y'))
                ->rawColumns(['milestone_name', 'project_name', 'description'])
                ->setRowId('id')
                ->filterColumn('milestone_name', function($query, $keyword) {
                    $query->where('milestones.milestone_name', 'like', '%'.$keyword.'%');
                });
            }

    public function query(ProjectMilestoneModel $model): QueryBuilder
    {
        return $model->newQuery()
                ->whereHas('projectModel', function($q) {
                    $q->where('client_id', auth()->guard('clients')->id());
                })
                ->with(['projectModel'])
                ->select('milestones.*');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('tasks-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->orderBy(2, 'desc');
    }

    public function getColumns(): array
    {
        return [
            ['data' => 'milestone_name', 'title' => 'Milestone'],
            ['data' => 'project_name', 'title' => 'Project'],
            ['data' => 'description', 'title' => 'Description'],
            ['data' => 'created_at', 'title' => 'Created']
        ];
    }

    protected function filename(): string
    {
        return 'Milestones_' . date('YmdHis');
    }
}
