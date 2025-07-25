
    <?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'db.php'; // Make sure this file contains your DB credentials

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Collect data
$staff_name = trim($_POST['staff_name']);
$staff_cno = $_POST['staff_cno'];
$staff_email = trim($_POST['staff_email']);
$staff_address = trim($_POST['staff_address']);
$staff_designation = trim($_POST['staff_designation']);
$staff_status = $_POST['staff_status'] ?? 1;

// Validate inputs
if (
    empty($staff_name) || empty($staff_cno) || empty($staff_email) ||
    empty($staff_address) || empty($staff_designation)
) {
    die("❌ All fields are required.");
}

// Optional: Prevent duplicate email or phone
$check_sql = "SELECT Staff_Id FROM Staff_Master WHERE Staff_Email = ? OR Staff_CNo = ?";
$check_stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($check_stmt, "si", $staff_email, $staff_cno);
mysqli_stmt_execute($check_stmt);
mysqli_stmt_store_result($check_stmt);

if (mysqli_stmt_num_rows($check_stmt) > 0) {
    echo "❌ Staff with this email or contact number already exists.";
} else {
    // Insert
    $insert_sql = "INSERT INTO Staff_Master 
        (Staff_Name, Staff_CNo, Staff_Email, Staff_Address, Staff_Designation, Staff_Status, Created_At)
        VALUES (?, ?, ?, ?, ?, ?, NOW())";
        
    $insert_stmt = mysqli_prepare($conn, $insert_sql);
    mysqli_stmt_bind_param($insert_stmt, "sisssi", $staff_name, $staff_cno, $staff_email, $staff_address, $staff_designation, $staff_status);

    if (mysqli_stmt_execute($insert_stmt)) {
        echo "✅ Staff member '$staff_name' added successfully!";
    } else {
        echo "❌ Error: " . mysqli_error($conn);
    }

    mysqli_stmt_close($insert_stmt);
}

mysqli_stmt_close($check_stmt);
mysqli_close($conn);
?>
