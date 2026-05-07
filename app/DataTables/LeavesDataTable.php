<?php



namespace App\DataTables;



use Carbon\Carbon;
use App\Models\LeaveManagementModel;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;

use Yajra\DataTables\EloquentDataTable;

use Yajra\DataTables\Html\Builder as HtmlBuilder;

use Yajra\DataTables\Html\Column;

use Yajra\DataTables\Services\DataTable;



class LeavesDataTable extends DataTable

{

    /**

     * Prepare the data for DataTable.

     */

    public function dataTable(QueryBuilder $query): EloquentDataTable

    {

        return datatables()->eloquent($query)

            ->addColumn('employee_name', function ($leave) {

                return $leave->user->first_name . ' ' . $leave->user->last_name;

            }) 
            ->orderColumn('employee_name', function ($query, $order) {
                $query->join('users', 'leaves.user_id', '=', 'users.id')
                      ->orderByRaw("CONCAT(users.first_name, ' ', users.last_name) $order")
                      ->select('leaves.*'); // Prevents duplicate rows
            })
            ->addColumn('leave_type', function ($leave) {

                return $leave->leaveTypes ? $leave->leaveTypes->name : 'NA';

            })
            ->orderColumn('leave_type', function ($query, $order) {
                $query->leftJoin('leave_types', 'leaves.leave_type_id', '=', 'leave_types.id')
                  ->orderBy('leave_types.name', $order)
                  ->select('leaves.*');
            })

            ->addColumn('status', function ($leave) {

                $status = [

                    0 => '<span class="badge bg-warning">Pending</span>',

                    1 => '<span class="badge bg-success">Approved</span>',

                    2 => '<span class="badge bg-danger">Rejected</span>',

                    3 => '<span class="badge bg-info">On Hold</span>',

                ];

                return $status[$leave->status] ?? '<span class="badge bg-secondary">Unknown</span>';

            })

            ->addColumn('leave_from', function ($leave) {
                return \Carbon\Carbon::parse($leave->leave_from)->format('jS M, Y');
            })->orderColumn('leave_from', function ($query, $order) {
                $query->orderBy('leave_from', $order);
            })

            ->addColumn('leave_to', function ($leave) {
                return \Carbon\Carbon::parse($leave->leave_to)->format('jS M, Y');
            })->orderColumn('leave_to', function ($query, $order) {
                $query->orderBy('leave_to', $order);
            })

            ->addColumn('document', function ($leave) {

                $authUser = auth()->user();

                // Disable upload if the leave is Approved (1) or Rejected (2)

                if (in_array($leave->status, [1, 2])) {

                    $attachment = $leave->attachments()->first();

                    if ($attachment) {

                        // Allow only the leave owner, Admin, or HR to view the document

                        if ($authUser->role == 1 || $authUser->role == 3 || auth()->id() === $leave->user_id) {

                            return '<button class="btn btn-sm btn-primary view-doc-btn" data-url="' . asset('uploads/leave_attachments/' . $attachment->file_name) . '">View</button>';

                        }

                    }

                    return '<span class="text-secondary">No Document</span>';

                }

                // Admin or HR can upload or view documents for any leave request

                if ($authUser->role == 1 || $authUser->role == 3) {

                    $attachment = $leave->attachments()->first();

                    if ($attachment) {

                        // Open in modal instead of a new tab

                        return '<button class="btn btn-sm btn-primary view-doc-btn" data-url="' . asset('uploads/leave_attachments/' . $attachment->file_name) . '">View</button>';

                    } else {

                        return '<button class="btn btn-sm btn-warning upload-doc-btn" data-id="' . $leave->id . '">Upload</button>';

                    }

                } 

                // Regular user can only upload or view their own documents

                if (auth()->id() === $leave->user_id) {

                    $attachment = $leave->attachments()->first();

                    if ($attachment) {

                       return '<button class="btn btn-sm btn-primary view-doc-btn" data-url="' . asset('uploads/leave_attachments/' . $attachment->file_name) . '">View</button>';

                    } else {

                        return '<button class="btn btn-sm btn-warning upload-doc-btn" data-id="' . $leave->id . '">Upload</button>';

                    }

                }

                // No button for unauthorized users

                return '';

            })

            ->addColumn('action', function ($leave) {

                $authUser = auth()->user();

                $dropdown = '<div class="dropdown">

                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">

                                    <i class="mdi mdi-dots-vertical"></i>

                                </button>

                            <div class="dropdown-menu">';

                // Check if the user is Admin or (HR and not editing another HR's leave)

                if ($authUser->role == 1 || ($authUser->role == 3 && $leave->user->role != 3)) {

                    // Display approve, reject, and hold options

                    $dropdown .= '<a class="dropdown-item text-success approve-btn" href="javascript:void(0)" data-id="' . $leave->id . '" title="Approve">

                                    <i class="mdi mdi-check-circle me-1"></i>Approve

                                  </a>';

                    $dropdown .= '<a class="dropdown-item text-danger reject-btn" href="javascript:void(0)" data-id="' . $leave->id . '" title="Reject">

                                    <i class="mdi mdi-close-circle me-1"></i>Reject

                                  </a>';

                    $dropdown .= '<a class="dropdown-item text-warning hold-btn" href="javascript:void(0)" data-id="' . $leave->id . '" title="Put On Hold">

                                    <i class="mdi mdi-pause-circle me-1"></i>Put On Hold

                                  </a>';

                    // Allow Edit only if status is not approved or rejected, or if user is Admin

                    if ($authUser->role == 1 || !in_array($leave->status, [1, 2])) {

                        $dropdown .= '<a class="dropdown-item text-primary" href="'.route('leave.edit', $leave->id).'" title="Edit">

                                        <i class="mdi mdi-pencil me-1"></i>Edit

                                      </a>';

                    }

                    // Admin can delete leave requests

                    if ($authUser->role == 1) {

                        $dropdown .= '<a class="dropdown-item text-danger delete-btn" href="javascript:void(0)" data-id="' . $leave->id . '" title="Delete">

                                        <i class="mdi mdi-delete me-1"></i>Delete

                                      </a>';

                    }



                } else {

                    $dropdown .= '<span class="dropdown-item text-secondary">No Action</span>';

                }

            

                $dropdown .= '</div></div>';

                return $dropdown;

            })

            ->addColumn('description', function ($leave) {

                $authUser = auth()->user();

                // Check if the user is Admin, HR, or the owner of the leave request

                if ($authUser->role == 1 || $authUser->role == 3 || auth()->id() === $leave->user_id) {

                    $description = $leave->description;

                    // Truncate description if it's too long

                    if (strlen($description) > 50) {

                        return '<span>' . strip_tags(substr($description, 0, 15)) . '...</span>

                                <a href="javascript:void(0)" class="text-primary read-more" data-description="' . htmlspecialchars($description) . '" title="Read More">Read More</a>';

                    } else {

                        return $description;

                    }

                }

                // Return empty string if the user is not authorized to see the description

                return 'No Authorization';

            })

            // ->addColumn('description', function ($leave) {

            //     $description = $leave->description;

            //     if (strlen($description) > 50) {  // Truncate description if it's too long

            //         return '<span>' . substr($description, 0, 15) . '...</span>

            //                 <a href="javascript:void(0)" class="text-primary read-more" data-description="' . $description . '" title="Read More">Read More</a>';

            //     } else {

            //         return $description;

            //     }

            // })

        ->rawColumns(['description', 'status', 'document', 'action']);

    }

    /**

     * Query the data for the DataTable.

     */

    public function query(LeaveManagementModel $model): QueryBuilder

    {

        $currentYearStart = now()->startOfYear(); 

        $currentYearEnd = now()->endOfYear();

        $authUser = auth()->user();
        $today = Carbon::today();

        $query = $model->newQuery()

            ->with('user', 'attachments')

            ->whereDate('leave_to', '>=', $today)
            ->whereBetween('leave_from', [$currentYearStart, $currentYearEnd]); // Filter leaves within the current year
        
        if ($authUser->role != 1 && $authUser->role != 3) {
            $query->where('user_id', auth()->id());
        }

        // $query->orderByRaw('CASE WHEN leave_from >= ? THEN 0 ELSE 1 END', [now()])

        //     ->orderBy('leave_from', 'asc'); // Sort by leave_from date, ascending

        // Add search filter if present

        if (request()->has('search') && request()->get('search')['value']) {

            $searchValue = request()->get('search')['value'];

            $query->whereHas('user', function ($q) use ($searchValue) {

                $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $searchValue . '%']);

            });

        }

        return $query;

    }

    

    // public function query(LeaveManagementModel $model): QueryBuilder

    // {

    //     $currentYearStart = now()->startOfYear(); 

    //     $currentYearEnd = now()->endOfYear();

    //     $query = $model->newQuery()

    //         ->with('user', 'attachments')

    //         ->whereBetween('leave_from', [$currentYearStart, $currentYearEnd]) // Filter leaves within the current year

    //         ->orderByRaw('CASE WHEN leave_from >= ? THEN 0 ELSE 1 END', [now()])

    //         ->orderBy('leave_from', 'asc'); // Sort by leave_from date, ascending

    

    //     // Add date filter if present

    //     if (request()->has('search') && request()->get('search')['value']) {

    //         $searchDate = request()->get('search')['value'];

    //         $query->whereDate('leave_from', $searchDate)

    //               ->orWhereDate('leave_to', $searchDate);

    //     }

    

    //     return $query;

    // }



    /**

     * Build the DataTable HTML.

     */

    public function html(): HtmlBuilder

    {

        return $this->builder()

            ->setTableId('leaves-table')

            ->columns($this->getColumns())

            ->ajax(route('leave.list')) 

            ->minifiedAjax()

            ->dom('<"row"<"col-md-12 mb-4"Br><"col-md-6 mb-4"l><"col-md-6 mb-4"f>><"row"<"col-md-12 mb-4"<"table-responsive"t>>><"row"<"col-md-6 mb-4"i><"col-md-6 mb-4"p>>')

            ->selectStyleSingle()

            ->parameters([

                'drawCallback' => 'function() { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }',

                'searchDelay' => 500,

                'buttons' => [

                    [

                        'extend' => 'collection',

                        'text' => __('Export'),

                        'buttons' => [

                            [

                                'extend' => 'csvHtml5',

                                'text' => __('CSV'),

                                'filename' => $this->filename(),

                                'exportOptions' => [

                                    'modifier' => ['selected' => true],

                                    'columns' => [0, 1]

                                ]

                            ],

                            [

                                'extend' => 'excelHtml5',

                                'text' => __('Excel'),

                                'filename' => $this->filename(),

                                'title' => '',

                                'exportOptions' => [

                                    'modifier' => ['selected' => true],

                                    'columns' => [0, 1]

                                ]

                            ],

                            [

                                'extend' => 'pdfHtml5',

                                'text' => __('PDF'),

                                'filename' => $this->filename(),

                                'title' => '',

                                'exportOptions' => [

                                    'modifier' => ['selected' => true],

                                    'columns' => [0, 1]

                                ]

                            ]

                        ]

                    ]

                ],

                'order' => []

            ]);

    }



    /**

     * Define the columns for the DataTable.

     */

    public function getColumns(): array

    {

        return [

            Column::make('employee_name')->title('Employee Name'),
            Column::make('leave_type')->title('Leave Type'),

            Column::make('leave_from'),

            Column::make('leave_to'),

            Column::make('description'),

            Column::make('status'),

            Column::make('document')->title('Document')->orderable(false)->searchable(false),

            Column::computed('action')->exportable(false)->printable(false)->width(100)

        ];

    }

    /**

     * Generate the filename for exports.

     */

    protected function filename(): string

    {

        return 'Leaves -'.date('YmdHis').'-'.config('app.name');

    }

}

