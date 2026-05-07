<?php

namespace App\Constants;

class ResponseMessage
{
    const SUCCESS="Success";
    const LOGGED_IN_SUCCESS_MESSAGE = "SignIn Successfully.";
    const SIGNUP_SUCCESS_MESSAGE = "SignUp Successfully.";
    const PROFILE_UPDATED_MESSAGE = "Profile updated successfully.";
    const UNAUTHORISED_MESSAGE = "Incorrect credentials or Your a/c was Blocked by the server admin.";
    const DATA_FOUND_MESSAGE = "Data Found.";

    // applicant
    const APPLICANT_CREATED_SUCCESSFULLY = "Applicant created successfully";
    const APPLICANT_FETCHED_SUCCESSFULLY = "Applicants fetched successfully";
    const APPLICANT_UPDATED_SUCCESS_SUCCESSFULLY = "Applicant updated successfully";
    const APPLICANT_DELETED_SUCCESS_SUCCESSFULLY = "Applicant deleted successfully";

    //authentication
    const FORGOT_PASSWORD_USER_NOT_FOUND_MESSAGE = "User not found";
    const RESET_PASSWORD_LINK_SEND_MESSAGE = "Reset password link send to your email.";
    const FORGOT_PASSWORD_TOKEN_INVALID_MESSAGE = "Token is invalid.";
    const NOT_FOUND_USER_MESSAGE = "User not found";
    const PASSWORD_RESET_SUCCESS_MESSAGE = "Password reset successfully.";
    const CHANGE_ACCESSIBILITY_MESSAGE = "Accessibility changed successfully.";
    
}