<?php
require_once __DIR__ . '/auth.php';

function accountBookings($userId) {
    // Include inactive services and past/cancelled sessions in the member's history.
    $sql = "SELECT CONCAT('S-',b.booking_id) AS reference, 'Activity / service' AS booking_type,
        s.service_title AS title, x.session_title, x.session_start_at, x.session_end_at,
        COALESCE(NULLIF(x.session_location,''),s.service_location) AS location,
        b.booking_first_name,b.booking_surname,b.booking_email,b.booked_at,st.booking_status_name
        FROM Booking b JOIN ServiceSession x ON x.service_session_id=b.service_session_id
        JOIN Service s ON s.service_id=x.service_id
        JOIN BookingStatus st ON st.booking_status_id=b.booking_status_id WHERE b.user_id=?
        UNION ALL
        SELECT CONCAT('F-',b.facility_booking_id), 'Facility', f.facility_name, NULL,
        x.session_start_at,x.session_end_at,f.facility_location,
        b.booking_first_name,b.booking_surname,b.booking_email,b.booked_at,st.booking_status_name
        FROM FacilityBooking b JOIN FacilitySession x ON x.facility_session_id=b.facility_session_id
        JOIN Facility f ON f.facility_id=x.facility_id
        JOIN BookingStatus st ON st.booking_status_id=b.booking_status_id WHERE b.user_id=?
        ORDER BY booked_at DESC, reference DESC";
    $query = authDb()->prepare($sql);
    $query->execute(array($userId,$userId));
    $rows = $query->fetchAll(PDO::FETCH_ASSOC); $query->closeCursor();
    return $rows;
}

function accountBookingTime($value) {
    $date = new DateTime($value,new DateTimeZone('Australia/Perth'));
    return $date->format('d M Y, g:i a');
}
