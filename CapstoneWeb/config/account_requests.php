<?php
require_once __DIR__.'/auth.php';
function accountRateEnquiries($userId) {
    $q=authDb()->prepare('SELECT e.rates_enquiry_id,e.enquiry_type,e.assessment_number,e.property_address,e.full_name,e.email,e.phone,e.enquiry_details,e.response_method,e.submitted_at,e.resolved_at,e.rates_enquiry_resolution,s.rates_enquiry_status_name FROM RatesEnquiry e JOIN RatesEnquiryStatus s ON s.rates_enquiry_status_id=e.rates_enquiry_status_id WHERE e.user_id=? ORDER BY e.submitted_at DESC,e.rates_enquiry_id DESC');
    $q->execute(array($userId)); $rows=$q->fetchAll(PDO::FETCH_ASSOC); $q->closeCursor(); return $rows;
}
function accountFeedback($userId) {
    $q=authDb()->prepare('SELECT f.feedback_id,f.feedback_name,f.feedback_email,f.feedback_message,f.response_request,f.submitted_at,f.resolved_at,f.feedback_resolution,c.feedback_category_name,s.feedback_status_name FROM Feedback f JOIN FeedbackCategory c ON c.feedback_category_id=f.feedback_category_id JOIN FeedbackStatus s ON s.feedback_status_id=f.feedback_status_id WHERE f.user_id=? ORDER BY f.submitted_at DESC,f.feedback_id DESC');
    $q->execute(array($userId)); $rows=$q->fetchAll(PDO::FETCH_ASSOC); $q->closeCursor(); return $rows;
}
