<?php
include 'db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);
// Validate Insurance_Entry_Id
if (!isset($_POST['insurance_entry_id']) || !is_numeric($_POST['insurance_entry_id'])) {
    die("Invalid Insurance Entry ID.");
}

$insurance_entry_id = intval($_POST['insurance_entry_id']);
$defect_id = intval($_POST['defect_id']);
$remarks = mysqli_real_escape_string($conn, $_POST['remarks'] ?? '');
$created_at = date("Y-m-d H:i:s");

// Validate Insurance_Entry existence
$check = mysqli_query($conn, "SELECT * FROM Insurance_Entry WHERE Insurance_Entry_Id = $insurance_entry_id");
if (mysqli_num_rows($check) === 0) {
    die("Insurance Entry ID does not exist.");
}

// File upload
if (isset($_FILES['claim_image']) && $_FILES['claim_image']['error'] === 0) {
    $uploadDir = 'uploads/claims/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $fileName = time() . "_" . basename($_FILES['claim_image']['name']);
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['claim_image']['tmp_name'], $targetPath)) {
        $imagePath = $targetPath;

        // Insert claim record
        $insert = "INSERT INTO Claim_Entry (Insurance_Entry_Id, Defect_Id, Claim_Remarks, Claim_Image_Path, Created_At)
                   VALUES ($insurance_entry_id, $defect_id, '$remarks', '$imagePath', '$created_at')";

        if (mysqli_query($conn, $insert)) {
            echo "<script>alert('Claim submitted successfully.'); window.location.href='claim_entry.php';</script>";
        } else {
            echo "Error inserting claim: " . mysqli_error($conn);
        }
    } else {
        echo "Failed to upload image.";
    }
} else {
    echo "Please upload a valid image.";
}
?>
