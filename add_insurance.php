<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';

// Insert or Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $id = $_POST['insurance_id'] ?? null;
  $name = $_POST['insurance_name'];
  $desc = $_POST['insurance_description'];
  $percent = $_POST['premium_percentage'];
  $duration = $_POST['duration'];
  $status = $_POST['insurance_status'];

  if ($id) {
    $stmt = $conn->prepare("UPDATE Insurance_Master SET Insurance_Name=?, Insurance_Description=?, Premium_Percentage=?, Duration_Months=?, Insurance_Status=? WHERE Insurance_Id=?");
    $stmt->bind_param("ssiiii", $name, $desc, $percent, $duration, $status, $id);
  } else {
    $stmt = $conn->prepare("INSERT INTO Insurance_Master (Insurance_Name, Insurance_Description, Premium_Percentage, Duration_Months, Insurance_Status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiii", $name, $desc, $percent, $duration, $status);
  }

  $stmt->execute();
  $stmt->close();
  header("Location: add_insurance.php");
  exit();
}

// Delete
if (isset($_GET['delete'])) {
  $delId = intval($_GET['delete']);
  $conn->query("DELETE FROM Insurance_Master WHERE Insurance_Id = $delId");
  header("Location: add_insurance.php?deleted=1");
  exit();
}

// Fetch for edit
$editData = null;
if (isset($_GET['edit'])) {
  $editId = intval($_GET['edit']);
  $res = $conn->query("SELECT * FROM Insurance_Master WHERE Insurance_Id = $editId");
  if ($res && $res->num_rows > 0) {
    $editData = $res->fetch_assoc();
  }
}

// Fetch all plans
$plans = $conn->query("SELECT * FROM Insurance_Master ORDER BY Insurance_Id DESC");
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Insurance Master</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body>
  <div class="navtop">
    <div class="logo">LOGO</div>
    <h1>Best Mobile Insurance Software</h1>
    <div class="hamburger" onclick="toggleSidebar()">☰</div>
  </div>

  <div class="container">
    <!-- Sidebar -->
   <aside class="sidebar mobile-hidden" id="sidebarMenu">
      <ul>
        <a href="index.php"><li>Dashboard</li></a>
        <a href="branch.php" class="active"><li>Branch Master</li></a>
        <a href="brand.php"><li>Brand Master</li></a>
        <a href="add_staff.php"><li>Staff Master</li></a>
        <a href="Customer_Master.php"><li>Customer Master</li></a>
        <a href="add_insurance.php"><li>Insurance Master</li></a>
        <a href="add_defect.php"><li>Defect Master</li></a>
        <a href="insurance_entry.php"><li>Insurance Entry</li></a>
        <a href="serch.php"><li>Claim</li></a>
      </ul>
    </aside>


    <main class="main-content">
      <div class="content-area">
        <!-- Form -->
        <section class="add-branch">
          <h3><?= $editData ? "Edit Insurance Plan" : "Add Insurance Plan" ?></h3>
          <form method="POST">
            <?php if ($editData): ?>
              <input type="hidden" name="insurance_id" value="<?= $editData['Insurance_Id'] ?>">
            <?php endif; ?>
            <input type="text" name="insurance_name" placeholder="Insurance Name" required value="<?= $editData['Insurance_Name'] ?? '' ?>">
            <textarea name="insurance_description" placeholder="Description" required><?= $editData['Insurance_Description'] ?? '' ?></textarea>
            <input type="number" name="premium_percentage" placeholder="Premium %" min="1" required value="<?= $editData['Premium_Percentage'] ?? '' ?>">

            <select name="duration" required>
              <option value="">-- Select Duration --</option>
              <?php for ($i = 1; $i <= 24; $i++): ?>
                <option value="<?= $i ?>" <?= (isset($editData['Duration_Months']) && $editData['Duration_Months'] == $i) ? 'selected' : '' ?>>
                  <?= $i ?> Month<?= $i > 1 ? 's' : '' ?>
                </option>
              <?php endfor; ?>
            </select>

            <select name="insurance_status">
              <option value="">-- Select Status --</option>
              <option value="1" <?= (isset($editData['Insurance_Status']) && $editData['Insurance_Status'] == 1) ? 'selected' : '' ?>>Active</option>
              <option value="0" <?= (isset($editData['Insurance_Status']) && $editData['Insurance_Status'] == 0) ? 'selected' : '' ?>>Inactive</option>
            </select>

            <button type="submit"><?= $editData ? 'Update Plan' : 'Add Plan' ?></button>
          </form>
        </section>

        <!-- List -->
        <section class="overview">
          <h3>Insurance Plans</h3>
          <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="planSearch" placeholder="Search insurance plans..." onkeyup="filterPlans(this.value)">
          </div>

          <div class="table-responsive">
            <table>
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Premium %</th>
                  <th>Duration</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($row = $plans->fetch_assoc()):
                  $jsonRow = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                  $statusText = $row['Insurance_Status'] == 1 ? 'Active' : 'Inactive';
                  $rowClass = $row['Insurance_Status'] == 1 ? 'active-row' : 'inactive-row';
                ?>
                  <tr class="<?= $rowClass ?>">
                    <td><?= $row['Insurance_Name'] ?></td>
                    <td><?= $row['Premium_Percentage'] ?>%</td>
                    <td><?= $row['Duration_Months'] ?> month(s)</td>
                    <td><?= $statusText ?></td>
                    <td class="action-btns">
                      <i class='fas fa-eye' onclick='viewDetails(<?= $jsonRow ?>)'></i>
                      <a href='?edit=<?= $row['Insurance_Id'] ?>'><i class='fas fa-pen'></i></a>
                       
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </section>
      </div>
    </main>
  </div>

  <!-- Popup -->
  <div class="popup-overlay" id="popupOverlay">
    <div class="popup-content" id="popupContent">
      <span class="close-btn" onclick="closePopup()">&times;</span>
      <h3>Insurance Plan Details</h3>
      <div id="popupDetails"></div>
    </div>
  </div>

  <script>
    function toggleSidebar() {
      document.getElementById('sidebarMenu').classList.toggle('mobile-hidden');
    }

    function filterPlans(query) {
      const rows = document.querySelectorAll("tbody tr");
      rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(query.toLowerCase()) ? "" : "none";
      });
    }

    function viewDetails(data) {
      const html = `
        <p><strong>ID:</strong> ${data.Insurance_Id}</p>
        <p><strong>Name:</strong> ${data.Insurance_Name}</p>
        <p><strong>Description:</strong> ${data.Insurance_Description}</p>
        <p><strong>Premium %:</strong> ${data.Premium_Percentage}%</p>
        <p><strong>Duration:</strong> ${data.Duration_Months} month(s)</p>
        <p><strong>Status:</strong> ${data.Insurance_Status == 1 ? 'Active' : 'Inactive'}</p>
      `;
      document.getElementById("popupDetails").innerHTML = html;
      document.getElementById("popupOverlay").style.display = 'flex';
    }

    function closePopup() {
      document.getElementById("popupOverlay").style.display = 'none';
    }

    function deleteInsurance(id) {
      if (confirm("Are you sure you want to delete this insurance plan?")) {
        window.location.href = "?delete=" + id;
      }
    }
  </script>
</body>
</html>
