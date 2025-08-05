<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';

// Upload folders
@mkdir('uploads/photos', 0777, true);
@mkdir('uploads/idcopies', 0777, true);

// Insert or Update logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $id = $_POST['cus_id'] ?? null;
  $name = $_POST['cus_name'];
  $cno = $_POST['cus_cno'];
  $email = $_POST['cus_email'];
  $address = $_POST['cus_address'];
  $ref_name = $_POST['cus_ref'];
  $ref_cno = $_POST['cus_ref_cno'];
  $branch_id = $_POST['branch_id'];
  $status = $_POST['cus_status'];

  $photo_path = $_FILES['cus_photo']['error'] == 0 ? 'uploads/photos/' . time() . '_' . basename($_FILES['cus_photo']['name']) : ($_POST['existing_photo'] ?? '');
  $id_copy_path = $_FILES['cus_id_copy']['error'] == 0 ? 'uploads/idcopies/' . time() . '_' . basename($_FILES['cus_id_copy']['name']) : ($_POST['existing_idcopy'] ?? '');

  if ($_FILES['cus_photo']['error'] == 0) move_uploaded_file($_FILES['cus_photo']['tmp_name'], $photo_path);
  if ($_FILES['cus_id_copy']['error'] == 0) move_uploaded_file($_FILES['cus_id_copy']['tmp_name'], $id_copy_path);

  if ($id) {
    $stmt = $conn->prepare("UPDATE Customer_Master SET Cus_Name=?, Cus_CNo=?, Cus_Email=?, Cus_Address=?, Cus_Ref_Name=?, Cus_Ref_CNo=?, Branch_Id=?, Is_Active=?, Cus_Photo_Path=?, Cus_Id_Copy_Path=? WHERE Cus_Id=?");
    $stmt->bind_param("sisssiiissi", $name, $cno, $email, $address, $ref_name, $ref_cno, $branch_id, $status, $photo_path, $id_copy_path, $id);
  } else {
    $stmt = $conn->prepare("INSERT INTO Customer_Master (Cus_Name, Cus_CNo, Cus_Email, Cus_Address, Cus_Ref_Name, Cus_Ref_CNo, Branch_Id, Is_Active, Cus_Photo_Path, Cus_Id_Copy_Path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sisssiiiss", $name, $cno, $email, $address, $ref_name, $ref_cno, $branch_id, $status, $photo_path, $id_copy_path);
  }

  $stmt->execute();
  $stmt->close();
  header("Location: Customer_Master.php");
  exit();
}

// Delete
if (isset($_GET['delete'])) {
  $delId = intval($_GET['delete']);
  $check = $conn->query("SELECT * FROM Insurance_Entry WHERE Cus_Id = $delId");
  if ($check->num_rows > 0) {
    echo "<script>alert('Cannot delete: Customer has insurance.'); window.location.href='Customer_Master.php';</script>";
    exit;
  }
  $conn->query("DELETE FROM Customer_Master WHERE Cus_Id = $delId");
  header("Location: Customer_Master.php?deleted=1");
  exit();
}

// Fetch for edit
$editData = null;
if (isset($_GET['edit'])) {
  $editId = intval($_GET['edit']);
  $res = $conn->query("SELECT * FROM Customer_Master WHERE Cus_Id = $editId");
  if ($res && $res->num_rows > 0) {
    $editData = $res->fetch_assoc();
  }
}

// Fetch customer list
$customers = $conn->query("SELECT c.*, b.Branch_Name FROM Customer_Master c LEFT JOIN Branch_Master b ON c.Branch_Id = b.Branch_Id ORDER BY Cus_Id DESC");

$branches = $conn->query("SELECT Branch_Id, Branch_Name FROM Branch_Master");
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Customer Master</title>
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
        <li><a href="index.php">Dashboard</a></li>
        <li><a href="branch.php">Branch Master</a></li>
        <li><a href="brand.php">Brand Master</a></li>
        <li><a href="add_staff.php">Staff Master</a></li>
        <li><a href="Customer_Master.php" class="active">Customer Master</a></li>
        <li><a href="add_insurance.php">Insurance Master</a></li>
        <li><a href="add_defect.php">Defect Master</a></li>
        <li><a href="insurance_entry.php">Insurance Entry</a></li>
        <li><a href="serch.php">Claim</a></li>
      </ul>
    </aside>

    <main class="main-content">
      <div class="content-area">
        <!-- Form Area -->
        <section class="add-branch">
          <h3><?= $editData ? "Edit Customer" : "Add Customer" ?></h3>
          <form method="POST" enctype="multipart/form-data">
            <?php if ($editData): ?>
              <input type="hidden" name="cus_id" value="<?= $editData['Cus_Id'] ?>">
              <input type="hidden" name="existing_photo" value="<?= $editData['Cus_Photo_Path'] ?>">
              <input type="hidden" name="existing_idcopy" value="<?= $editData['Cus_Id_Copy_Path'] ?>">
            <?php endif; ?>
            <input type="text" name="cus_name" placeholder="Customer Name" required value="<?= $editData['Cus_Name'] ?? '' ?>">
            <input type="number" name="cus_cno" placeholder="Contact Number" required value="<?= $editData['Cus_CNo'] ?? '' ?>">
            <input type="email" name="cus_email" placeholder="Email" required value="<?= $editData['Cus_Email'] ?? '' ?>">
            <textarea name="cus_address" placeholder="Address" required><?= $editData['Cus_Address'] ?? '' ?></textarea>
            <input type="text" name="cus_ref" placeholder="Reference Name" value="<?= $editData['Cus_Ref_Name'] ?? '' ?>">
            <input type="number" name="cus_ref_cno" placeholder="Reference Number" value="<?= $editData['Cus_Ref_CNo'] ?? '' ?>">

            <select name="branch_id" required>
              <option value="">-- Select Branch --</option>
              <?php while ($b = $branches->fetch_assoc()): ?>
                <option value="<?= $b['Branch_Id'] ?>" <?= (isset($editData['Branch_Id']) && $editData['Branch_Id'] == $b['Branch_Id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($b['Branch_Name']) ?>
                </option>
              <?php endwhile; ?>
            </select>

            <select name="cus_status">
              <option value="">-- Select Status --</option>
              <option value="1" <?= (isset($editData['Is_Active']) && $editData['Is_Active'] == 1) ? 'selected' : '' ?>>Active</option>
              <option value="0" <?= (isset($editData['Is_Active']) && $editData['Is_Active'] == 0) ? 'selected' : '' ?>>Inactive</option>
            </select>

            <label>Customer Photo</label>
            <input type="file" name="cus_photo">

            <label>ID Copy</label>
            <input type="file" name="cus_id_copy">

            <button type="submit"><?= $editData ? 'Update Customer' : 'Add Customer' ?></button>
          </form>
        </section>

        <!-- Customer List -->
        <section class="overview">
          <h3>Customer Overview</h3>
          <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="searchBox" placeholder="Search customers..." onkeyup="filterCustomer(this.value)">
          </div>

          <div class="table-responsive">
            <table>
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Contact</th>
                  <th>Branch</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($row = $customers->fetch_assoc()):
                  $jsonRow = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                  $statusText = $row['Is_Active'] == 1 ? 'Active' : 'Inactive';
                ?>
                  <tr>
                    <td><?= $row['Cus_Name'] ?></td>
                    <td><?= $row['Cus_CNo'] ?></td>
                    <td><?= $row['Branch_Name'] ?? 'N/A' ?></td>
                    <td><?= $statusText ?></td>
                    <td class="action-btns">
                      <i class='fas fa-eye' onclick='viewDetails(<?= $jsonRow ?>)'></i>
                      <a href='?edit=<?= $row['Cus_Id'] ?>'><i class='fas fa-pen'></i></a>
                      
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
      <h3>Customer Details</h3>
      <div id="popupDetails"></div>
    </div>
  </div>

  <script>
    function toggleSidebar() {
      document.getElementById('sidebarMenu').classList.toggle('mobile-hidden');
    }

    function filterCustomer(query) {
      const rows = document.querySelectorAll("tbody tr");
      rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(query.toLowerCase()) ? "" : "none";
      });
    }

    function viewDetails(cus) {
      const html = `
        <p><strong>Name:</strong> ${cus.Cus_Name}</p>
        <p><strong>Contact:</strong> ${cus.Cus_CNo}</p>
        <p><strong>Email:</strong> ${cus.Cus_Email}</p>
        <p><strong>Address:</strong> ${cus.Cus_Address}</p>
        <p><strong>Reference:</strong> ${cus.Cus_Ref_Name} (${cus.Cus_Ref_CNo})</p>
        <p><strong>Branch:</strong> ${cus.Branch_Name ?? 'Not Assigned'}</p>
        <p><strong>Status:</strong> ${cus.Is_Active == 1 ? 'Active' : 'Inactive'}</p>
        <p><strong>Photo:</strong><br><img src='${cus.Cus_Photo_Path}' width='100'></p>
        <p><strong>ID Copy:</strong><br><img src='${cus.Cus_Id_Copy_Path}' width='100'></p>
      `;
      document.getElementById("popupDetails").innerHTML = html;
      document.getElementById("popupOverlay").style.display = 'flex';
    }

    function closePopup() {
      document.getElementById("popupOverlay").style.display = 'none';
    }

    function deleteCustomer(id) {
      if (confirm("Are you sure you want to delete this customer?")) {
        window.location.href = "?delete=" + id;
      }
    }
  </script>
</body>
</html>