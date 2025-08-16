<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';
include 'popup_handler.php';
// Insert or Update logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $id = $_POST['staff_id'] ?? null;
  $name = $_POST['staff_name'];
  $cno = $_POST['staff_cno'];
  $email = $_POST['staff_email'];
  $address = $_POST['staff_address'];
  $designation = $_POST['staff_designation'];
  $status = $_POST['staff_status'];
  $branch_id = $_POST['branch_id'];

  if ($id) {
    $stmt = $conn->prepare("UPDATE Staff_Master SET Staff_Name=?, Staff_CNo=?, Staff_Email=?, Staff_Address=?, Staff_Designation=?, Staff_Status=?, Branch_Id=? WHERE Staff_Id=?");
    $stmt->bind_param("sisssiii", $name, $cno, $email, $address, $designation, $status, $branch_id, $id);
  } else {
    $stmt = $conn->prepare("INSERT INTO Staff_Master (Staff_Name, Staff_CNo, Staff_Email, Staff_Address, Staff_Designation, Staff_Status, Branch_Id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sisssii", $name, $cno, $email, $address, $designation, $status, $branch_id);
  }

  $stmt->execute();
  $stmt->close();
  header("Location: add_staff.php");
  exit();
}

// Delete
if (isset($_GET['delete'])) {
  $delId = intval($_GET['delete']);
  $conn->query("DELETE FROM Staff_Master WHERE Staff_Id = $delId");
  header("Location: add_staff.php?deleted=1");
  exit();
}

// Fetch for edit
$editData = null;
if (isset($_GET['edit'])) {
  $editId = intval($_GET['edit']);
  $res = $conn->query("SELECT * FROM Staff_Master WHERE Staff_Id = $editId");
  if ($res && $res->num_rows > 0) {
    $editData = $res->fetch_assoc();
  }
}

// Fetch staff list
$staffs = $conn->query("
  SELECT s.*, b.Branch_Name 
  FROM Staff_Master s 
  LEFT JOIN Branch_Master b ON s.Branch_Id = b.Branch_Id 
  ORDER BY s.Staff_Id DESC
");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';

    // Example: Staff added successfully
    if (!empty($name)) {
        header("Location: add_staff.php?success=1&msg=Staff+Added+Successfully");
        exit();
    } else {
        header("Location: add_staff.php?error=1&msg=Failed+to+Add+Staff");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Staff Master</title>
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
        <a href="insuranceentry.php"><li>Insurance Entry</li></a>
        <a href="serch.php"><li>Claim</li></a>
      </ul>
    </aside>


    <main class="main-content">
      <div class="content-area">
        <!-- Form Area -->
        <section class="add-branch">
          <h3><?= $editData ? "Edit Staff" : "Add Staff" ?></h3>
          <form method="POST">
            <?php if ($editData): ?>
              <input type="hidden" name="staff_id" value="<?= $editData['Staff_Id'] ?>">
            <?php endif; ?>
            <input type="text" name="staff_name" placeholder="Staff Name" required value="<?= $editData['Staff_Name'] ?? '' ?>">
            <input type="number" name="staff_cno" placeholder="Contact Number" required value="<?= $editData['Staff_CNo'] ?? '' ?>">
            <input type="email" name="staff_email" placeholder="Email" required value="<?= $editData['Staff_Email'] ?? '' ?>">
            <textarea name="staff_address" placeholder="Address" required><?= $editData['Staff_Address'] ?? '' ?></textarea>
            <input type="text" name="staff_designation" placeholder="Designation" required value="<?= $editData['Staff_Designation'] ?? '' ?>">

            <select name="branch_id" required>
               
              <?php
              $branches = $conn->query("SELECT Branch_Id, Branch_Name FROM Branch_Master");
              while ($b = $branches->fetch_assoc()):
                $selected = (isset($editData['Branch_Id']) && $editData['Branch_Id'] == $b['Branch_Id']) ? 'selected' : '';
              ?>
                <option value="<?= $b['Branch_Id'] ?>" <?= $selected ?>><?= htmlspecialchars($b['Branch_Name']) ?></option>
              <?php endwhile; ?>
            </select>

            <select name="staff_status">
             
              <option value="1" <?= (isset($editData['Staff_Status']) && $editData['Staff_Status'] == 1) ? 'selected' : '' ?>>Active</option>
              <option value="0" <?= (isset($editData['Staff_Status']) && $editData['Staff_Status'] == 0) ? 'selected' : '' ?>>Inactive</option>
            </select>

            <button type="submit"><?= $editData ? 'Update Staff' : 'Add Staff' ?></button>
          </form>
        </section>

        <!-- Staff List -->
        <section class="overview">
          <h3>Staff Overview</h3>
          <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="staffSearch" placeholder="Search staff..." onkeyup="filterStaff(this.value)">
          </div>

          <div class="table-responsive">
            <table>
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Branch</th>
                  <th>Designation</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($row = $staffs->fetch_assoc()):
                  $jsonRow = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                  $statusText = $row['Staff_Status'] == 1 ? 'Active' : 'Inactive';
                  $rowClass = $row['Staff_Status'] == 1 ? 'active-row' : 'inactive-row';
                ?>
                  <tr class="<?= $rowClass ?>">
                    <td><?= $row['Staff_Name'] ?></td>
                    <td><?= $row['Branch_Name'] ?? 'N/A' ?></td>
                    <td><?= $row['Staff_Designation'] ?></td>
                    <td><?= $statusText ?></td>
                    <td class="action-btns">
                      <i class='fas fa-eye' onclick='viewDetails(<?= $jsonRow ?>)'></i>
                      <a href='?edit=<?= $row['Staff_Id'] ?>'><i class='fas fa-pen'></i></a>
                       
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
      <h3>Staff Details</h3>
      <div id="popupDetails"></div>
    </div>
  </div>

  <script>
    function toggleSidebar() {
      document.getElementById('sidebarMenu').classList.toggle('mobile-hidden');
    }

    function filterStaff(query) {
      const rows = document.querySelectorAll("tbody tr");
      rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(query.toLowerCase()) ? "" : "none";
      });
    }

    function viewDetails(staff) {
      const html = `
        <p><strong>ID:</strong> ${staff.Staff_Id}</p>
        <p><strong>Name:</strong> ${staff.Staff_Name}</p>
        <p><strong>Contact:</strong> ${staff.Staff_CNo}</p>
        <p><strong>Email:</strong> ${staff.Staff_Email}</p>
        <p><strong>Address:</strong> ${staff.Staff_Address}</p>
        <p><strong>Designation:</strong> ${staff.Staff_Designation}</p>
        <p><strong>Branch:</strong> ${staff.Branch_Name ?? 'Not Assigned'}</p>
        <p><strong>Status:</strong> ${staff.Staff_Status == 1 ? 'Active' : 'Inactive'}</p>
      `;
      document.getElementById("popupDetails").innerHTML = html;
      document.getElementById("popupOverlay").style.display = 'flex';
    }

    function closePopup() {
      document.getElementById("popupOverlay").style.display = 'none';
    }

    function deleteStaff(id) {
      if (confirm("Are you sure you want to delete this staff?")) {
        window.location.href = "?delete=" + id;
      }
    }
  </script>
</body>
</html>
