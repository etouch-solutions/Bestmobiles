<?php
include 'db.php';
$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $insurance_name = $_POST['insurance_name'];
  $insurance_description = $_POST['insurance_description'];
  $premium_percentage = $_POST['premium_percentage'];
  $duration_string = $_POST['duration'];  // e.g. "12 Months"
  $insurance_status = $_POST['insurance_status'];

  // Extract numeric part: "12 Months" => 12
  preg_match('/\d+/', $duration_string, $matches);
  $duration_months = isset($matches[0]) ? intval($matches[0]) : 0;

  $stmt = $conn->prepare("INSERT INTO Insurance_Master (Insurance_Name, Insurance_Description, Premium_Percentage, Duration_Months, Insurance_Status) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("ssdii", $insurance_name, $insurance_description, $premium_percentage, $duration_months, $insurance_status);

  if ($stmt->execute()) {
    echo "✅ Insurance plan added successfully!";
  } else {
    echo "❌ Error: " . $stmt->error;
  }

  $stmt->close();
  $conn->close();
}
?>
