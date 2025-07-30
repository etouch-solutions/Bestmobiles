<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $insurance_entry_id = intval($_POST['insurance_entry_id']);
    $defect_id = intval($_POST['defect_id']);
    $remarks = mysqli_real_escape_string($conn, $_POST['remarks'] ?? '');

    // Validate if insurance entry ID exists
    $check = mysqli_query($conn, "SELECT * FROM Insurance_Entry WHERE Insurance_Entry_Id = $insurance_entry_id");
    if (mysqli_num_rows($check) == 0) {
        die("Invalid Insurance Entry ID.");
    }

    // Upload image
    $image_path = '';
    if (isset($_FILES['claim_image']) && $_FILES['claim_image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/claims/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $filename = basename($_FILES["claim_image"]["name"]);
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $newname = uniqid("claim_", true) . "." . $ext;
        $target_file = $target_dir . $newname;

        if (move_uploaded_file($_FILES["claim_image"]["tmp_name"], $target_file)) {
            $image_path = $target_file;
        } else {
            die("Failed to upload image.");
        }
    }

    // Insert claim
    $sql = "INSERT INTO Claim_Entry (Insurance_Entry_Id, Defect_Id, Claim_Remarks, Claim_Image_Path)
            VALUES ($insurance_entry_id, $defect_id, '$remarks', '$image_path')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Claim submitted successfully!'); window.location.href='claim_entry.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
