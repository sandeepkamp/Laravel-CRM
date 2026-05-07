<?php

namespace App\Constants;

class Enum
{
    // -----project_tasks table-----
    const PRIORITIES = [
        'low'    => ['label' => 'Low', 'font_color' => '#1E88E5', 'background_color' => '#E3F2FD'],
        'medium' => ['label' => 'Medium', 'font_color' => '#FF9800', 'background_color' => '#FFF3E0'],
        'high'   => ['label' => 'High', 'font_color' => '#D32F2F', 'background_color' => '#FFEBEE']
    ];

    public static function getPriorities()
    {
        return array_keys(self::PRIORITIES); // Enum::getPriorities(); ['low', 'medium', 'high']
    }
    
    // // -----hosting_features table-----
    // const YES_NO = ['Yes', 'No']; // ssl, backup
    // const SERVER_TYPES = ['Shared', 'VPS', 'Dedicated'];

    // // -----seo_services table-----
    // const SEO_PACKAGES = ['Basic', 'Advanced'];
    // const SEO_STATUSES = ['Active', 'Inactive', 'Pending'];

    // // -----customer_hostings table-----
    // const HOSTING_STATUSES = ['Active', 'Expired'];

    // // -----employee_feedbacks table-----
    // const QUARTER = ['quarterly', 'half_yearly', 'yearly'];

    // -----hosting_features table-----
    const YES_NO = [
        'yes' => ['label' => 'Yes'],
        'no'  => ['label' => 'No'],
    ]; // ssl, backup

    const SERVER_TYPES = [
        'shared'    => ['label' => 'Shared'],
        'vps'       => ['label' => 'VPS'],
        'dedicated' => ['label' => 'Dedicated'],
    ];

    // -----seo_services table-----
    const SEO_PACKAGES = [
        'basic'    => ['label' => 'Basic'],
        'advanced' => ['label' => 'Advanced'],
        'premium' => ['label' => 'Premium'],
    ];

    const SEO_STATUSES = [
        'active'   => ['label' => 'Active'],
        'inactive' => ['label' => 'Inactive'],
        'pending'  => ['label' => 'Pending'],
    ];

    // -----customer_hostings table-----
    const HOSTING_STATUSES = [
        'active'  => ['label' => 'Active'],
        'expired' => ['label' => 'Expired'],
    ];

    // -----employee_feedbacks table-----
    const QUARTER = [
        'q1' => ['label' => 'Quarterly'],
        'q2' => ['label' => 'Half Yearly'],
        'q3' => ['label' => 'Yearly'],
    ];

    // -----attendances table-----
    const ATTENDANCE_STATUS = [
        'present' => ['label' => 'Present'],
        'late'    => ['label' => 'Late'],
        'absent'  => ['label' => 'Absent']
    ];

    // -----attendance_regularizations table-----
    const ATTENDANCE_REGULARIZATIONS = [
        'pending'  => ['label' => 'Pending'],
        'approved' => ['label' => 'Approved'],
        'rejected' => ['label' => 'Rejected']
    ];

    // -----salary_payments table-----
    const PAYMENT_MODE = [
        'bank_transfer' => ['label' => 'Bank Transfer'],
        'cash'          => ['label' => 'Cash'],
        'cheque'        => ['label' => 'Cheque'],
        'upi'           => ['label' => 'UPI']
    ];

    // -----job_postings table-----
    const JOB_POSTING_STATUS = [
        'open'   => ['label' => 'Open'], 
        'closed' => ['label' => 'Closed'], 
        'filled' => ['label' => 'Filled']
    ];

    // -----interviews table-----
    const INTERVIEW_STATUS = [
        'scheduled' => ['label' => 'Scheduled'], 
        'completed' => ['label' => 'Completed']
    ];

    // -----applicants table-----
    const APPLICANT_STATUS = [
        'applied'     => ['label' => 'Applied'], 
        'shortlisted' => ['label' => 'Shortlisted'],
        'interviewed' => ['label' => 'Interviewed'], 
        'offered'     => ['label' => 'Offered'],
        'hired'       => ['label' => 'Hired']
    ];

    // -----offer_letters table-----
    const OFFER_LETTER_STATUS = [
        'accepted' => ['label' => 'Accepted'], 
        'declined' => ['label' => 'Declined'],
        'pending'  => ['label' => 'Pending']
    ];

    // -----appraisals table-----
    const PERIOD = [
        'q1' => ['label' => 'Quarterly'], 
        'q2' => ['label' => 'Half Yearly'],
        'q3' => ['label' => 'Yearly']
    ];

    // -----resignations table-----
    const RESIGNATION_STATUS = [
        'submitted' => ['label' => 'Submitted'],
        'accepted'  => ['label' => 'Accepted'],
        'rejected'  => ['label' => 'Rejected'],
    ];

    // -----asset_handovers table-----
    const ASSET_HANDOVER_STATUS = [
        'pending'   => ['label' => 'Pending'],
        'completed' => ['label' => 'Completed'],
    ];
    
    // -----asset_handovers table-----
    const EXIT_CLEARANCE_STATUS = [
        'pending' => ['label' => 'Pending'],
        'completed' => ['label' => 'Completed'],
    ];
    
    // -----leaves table-----
    const LEAVE_STATUS = [
        'pending' => ['label' => 'Pending', 'index' => 0],
        'approved' => ['label' => 'Approved', 'index' => 1],
        'disapproved' => ['label' => 'Disapproved', 'index' => 2],
        'on-hold' => ['label' => 'On Hold', 'index' => 3],
    ];

    // -----for all constants-----
    public static function keys(string $constant): array
    {
        return array_keys(constant("self::$constant"));
    }
}
