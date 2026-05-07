<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use App\DataTables\LeavesDataTable;
use App\Models\LeaveManagementModel;
use App\Models\LeaveAttachmentsModel;

class LeaveManagementController extends Controller
{
    public function list(LeavesDataTable $dataTable)
    {
        return $dataTable->render('leaves.list');
    }

    public function leave_request()
    {
        if (auth()->user()->role == 1) {
            // Admin can see all employees, including HR
            $employees = User::whereNotIn('role', [1])->get();
        } elseif (auth()->user()->role == 3) {
            // HR can see all employees (including themselves)
           $employees = User::where('role', '!=', 1)->get();
        } else {
            // Other users can only see their own name
            $employees = User::where('id', auth()->id())->get();
        }

        $leaveTypes = LeaveType::all();
    
        return view('leaves.request', compact('employees', 'leaveTypes'));
    }

    public function submit_request(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'leave_from' => 'required|date|after_or_equal:' . now()->toDateString(), // Ensure it's not a past date
            'leave_to' => 'required|date|after_or_equal:leave_from', // Ensure the leave end date is after the start date
            'description' => 'required|string|max:255',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // Max 2MB
        ]);

        $leave = LeaveManagementModel::create([
            'user_id' => $request->user_id,
            'leave_type_id' => $request->leave_type_id,
            'leave_from' => $request->leave_from,
            'leave_to' => $request->leave_to,
            'description' => $request->description,
            'submitted_by' => auth()->id(),
            'status' => 0, // Default to Pending
        ]);
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            // Check if the file was uploaded correctly
            if ($file->isValid()) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->move(public_path('uploads/leave_attachments'), $filename);
                // Get the file size after the file has been successfully moved
                $fileSize = filesize($filePath);
                LeaveAttachmentsModel::create([
                    'leave_id' => $leave->id,
                    'file_name' => $filename,
                    'file_type' => $file->getClientOriginalExtension(),
                    'file_size' => $fileSize, // Use filesize() to get the size 
                    'uploaded_by' => auth()->id(),
                ]);
            } else {
                return redirect()->back()->with('error', 'File upload failed. Please try again.');
            }
        }
        return redirect()->route('leave.list')->with('success', 'Leave request submitted successfully.');
    }
    
    public function upload_attachment(Request $request)
    {
        $request->validate([
            'leave_id' => 'required|exists:leaves,id',
            'attachment' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);
        $file = $request->file('attachment');
        $filename = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->move(public_path('uploads/leave_attachments'), $filename);
        // Get the file size after the file has been successfully moved
        $fileSize = filesize($filePath);
        LeaveAttachmentsModel::create([
            'leave_id' => $request->leave_id,
            'file_name' => $filename,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $fileSize,
            'uploaded_by' => auth()->id(),
        ]);
    
        return response()->json(['status' => 'success', 'message' => 'File uploaded successfully']);
    }
    
    public function edit_request($id)
    {
        $leave = LeaveManagementModel::findOrFail($id);
        // Only Admin or HR can edit leaves
        if (auth()->user()->role == 1 || auth()->user()->role == 3) {
            // HR cannot edit their own leaves
            if (auth()->user()->role == 3 && $leave->user->role == 3) {
                return redirect()->back()->with('error', 'You are not authorized to edit this leave.');
            }
            $employees = User::whereNotIn('role', [1])->get();
            return view('leaves.edit', compact('leave', 'employees'));
        }
        return redirect()->back()->with('error', 'You are not authorized to edit this leave.');
    }
    
    public function update_request(Request $request, $id)
    {
        $leave = LeaveManagementModel::findOrFail($id);
        // Only Admin or HR can update leaves
        if (auth()->user()->role == 1 || auth()->user()->role == 3) {
            // HR cannot update their own leaves
            if (auth()->user()->role == 3 && $leave->user->role == 3) {
                return redirect()->back()->with('error', 'You are not authorized to update this leave.');
            }
            $request->validate([
                'leave_from' => 'required|date|after_or_equal:' . now()->toDateString(),
                'leave_to' => 'required|date|after_or_equal:leave_from',
                'description' => 'required|string|max:255',
            ]);
            $leave->update([
                'leave_from' => $request->leave_from,
                'leave_to' => $request->leave_to,
                'description' => $request->description,
            ]);
    
            return redirect()->route('leave.list')->with('success', 'Leave updated successfully.');
        }
    
        return redirect()->back()->with('error', 'You are not authorized to update this leave.');
    }
    
    public function approve_request(Request $request, $id)
    {
        if ($request->ajax()) {
            $leave = LeaveManagementModel::find($id);
    
            if (!$leave) {
                return response()->json(['status' => 'error', 'message' => 'Leave request not found.']);
            }
    
            $authUser = auth()->user();
            if (!($authUser->role == 1 || ($authUser->role == 3 && $leave->user->role != 3))) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized.']);
            }
    
            // Check if the leave is already approved or rejected
            if (in_array($leave->status, [1, 2])) {
                return response()->json(['status' => 'error', 'message' => 'Approved or Rejected leave cannot be changed.']);
            }
    
            // Allow status change from Pending (0) or On Hold (3) to Approved (1)
            if (in_array($leave->status, [0, 3])) {
                $leave->status = 1; // Approved
                $leave->save();
    
                // Send email notification
                \Mail::to($leave->user->email)
                    ->send(new \App\Mail\LeaveStatusChanged($leave, 'Approved'));
            }

            // Update leave balance
            $this->updateLeavebalance($leave);
    
            return response()->json(['status' => 'success', 'message' => 'Leave request approved successfully.']);
        }
    
        return response()->json(['status' => 'error', 'message' => 'Invalid request.']);
    }

    private function updateLeaveBalance($leave)
    {
        $employeeId = $leave->user_id;
        $leaveTypeId = $leave->leave_type_id;
        $leaveFrom = Carbon::parse($leave->leave_from);
        $leaveTo = Carbon::parse($leave->leave_to);

        // duration_type === 'half' or 'full' add in enum and request leave add/edit form
        // if ($leave->duration_type === 'half') {
        //     $countDays = 0.5;
        // } else {
        //     $countDays = $leaveFrom->diffInDays($leaveTo) + 1;
        // }
        $countDays = $leaveFrom->diffInDays($leaveTo) + 1;

        $leaveBalance = LeaveBalance::where(['employee_id' => $employeeId, 'leave_type_id' => $leaveTypeId])->first();

        if ($leaveBalance) {
            $leaveBalance->update([
                'used_days' => $leaveBalance->used_days + $countDays,
                'closing_balance' => $leaveBalance->opening_balance - ($leaveBalance->used_days + $countDays),
            ]);
        }
    }

    public function reject_request(Request $request, $id)
    {
        if ($request->ajax()) {
            $leave = LeaveManagementModel::find($id);
    
            if (!$leave) {
                return response()->json(['status' => 'error', 'message' => 'Leave request not found.']);
            }
    
            $authUser = auth()->user();
            if (!($authUser->role == 1 || ($authUser->role == 3 && $leave->user->role != 3))) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized.']);
            }
    
            // Check if the leave is already approved or rejected
            if (in_array($leave->status, [1, 2])) {
                return response()->json(['status' => 'error', 'message' => 'Approved or Rejected leave cannot be changed.']);
            }
    
            // Allow status change from Pending (0) or On Hold (3) to Rejected (2)
            if (in_array($leave->status, [0, 3])) {
                $leave->status = 2; // Rejected
                $leave->save();
    
                // Send email notification
                \Mail::to($leave->user->email)
                    ->send(new \App\Mail\LeaveStatusChanged($leave, 'Rejected'));
            }
    
            return response()->json(['status' => 'success', 'message' => 'Leave request rejected successfully.']);
        }
    
        return response()->json(['status' => 'error', 'message' => 'Invalid request.']);
    }
    
    public function hold_request(Request $request, $id)
    {
        if ($request->ajax()) {
            $leave = LeaveManagementModel::find($id);
    
            if (!$leave) {
                return response()->json(['status' => 'error', 'message' => 'Leave request not found.']);
            }
    
            $authUser = auth()->user();
            if (!($authUser->role == 1 || ($authUser->role == 3 && $leave->user->role != 3))) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized.']);
            }
    
            // Check if the leave is already approved or rejected
            if (in_array($leave->status, [1, 2])) {
                return response()->json(['status' => 'error', 'message' => 'Approved or Rejected leave cannot be changed.']);
            }
    
            // Send email only when status changes from Pending (0,1,2) to On Hold (3)
            if (in_array($leave->status, [0])) {
                $leave->status = 3; // On Hold
                $leave->save();
    
                // Send email notification
                \Mail::to($leave->user->email)
                    ->send(new \App\Mail\LeaveStatusChanged($leave, 'On Hold'));
            }
    
            return response()->json(['status' => 'success', 'message' => 'Leave request put on hold successfully.']);
        }
    
        return response()->json(['status' => 'error', 'message' => 'Invalid request.']);
    }
    
    public function destroy(Request $request, $leave_id)
    {
        // Only Admin can delete leave requests
        if (auth()->user()->role != 1) {
            return response()->json(['status' => 'error', 'message' => 'You do not have permission to delete this leave request.']);
        }
        $leave = LeaveManagementModel::find($leave_id);
        if ($leave) {
            // Delete related attachments
            $attachments = $leave->attachments;
            foreach ($attachments as $attachment) {
                // Delete the physical file if exists
                $filePath = public_path('uploads/leave_attachments/' . $attachment->file_name);
                if (file_exists($filePath)) {
                    unlink($filePath);  // Delete the file from the server
                }
                $attachment->delete();  // Delete the attachment record
            }
            // Delete the leave record
            $leave->delete();
            return response()->json(['status' => 'success', 'message' => 'Leave request deleted successfully.']);
        }
        return response()->json(['status' => 'error', 'message' => 'Leave request not found.']);
    }
}
