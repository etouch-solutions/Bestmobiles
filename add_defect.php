<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';

// Insert or Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $id = $_POST['defect_id'] ?? null;
  $name = $_POST['defect_name'];
  $desc = $_POST['defect_description'];
  $status = $_POST['defect_status'];

  if ($id) {
    $stmt = $conn->prepare("UPDATE Defect_Master SET Defect_Name=?, Defect_Description=?, Is_Active=?, Updated_At=NOW() WHERE Defect_Id=?");
    $stmt->bind_param("ssii", $name, $desc, $status, $id);
  } else {
    $stmt = $conn->prepare("INSERT INTO Defect_Master (Defect_Name, Defect_Description, Is_Active) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $name, $desc, $status);
  }

  $stmt->execute();
  $stmt->close();
  header("Location: add_defect.php");
  exit;
}

// Delete
if (isset($_GET['delete'])) {
  $id = intval($_GET['delete']);
  $conn->query("DELETE FROM Defect_Master WHERE Defect_Id = $id");
  header("Location: add_defect.php?deleted=1");
  exit;
}

// Fetch for edit
$editData = null;
if (isset($_GET['edit'])) {
  $editId = intval($_GET['edit']);
  $res = $conn->query("SELECT * FROM Defect_Master WHERE Defect_Id = $editId");
  if ($res && $res->num_rows > 0) {
    $editData = $res->fetch_assoc();
  }
}

$search = $_GET['search'] ?? '';
$searchSql = $search ? "WHERE Defect_Name LIKE '%$search%' OR Defect_Description LIKE '%$search%'" : "";
$defects = $conn->query("SELECT * FROM Defect_Master $searchSql ORDER BY Defect_Id DESC");
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Defect Master</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body>
  <div class="navtop">
    <div class="logo">LOGO</div>
    <h1>Best Mobile Insurance Software</h1>
    <div class="hamburger" onclick="toggleSidebar()">☰</div>
    <a href="xl.php?table=Defect_Master">
    <button>Export Defect Master</button>
</a>

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


    <!-- Main Content -->
    <main class="main-content">
      <div class="content-area">
        <!-- Form -->
        <section class="add-branch">
          <h3><?= $editData ? "Edit Defect" : "Add Defect" ?></h3>
          <form method="POST">
            <?php if ($editData): ?>
              <input type="hidden" name="defect_id" value="<?= $editData['Defect_Id'] ?>">
            <?php endif; ?>

            <input type="text" name="defect_name" placeholder="Defect Name" required value="<?= $editData['Defect_Name'] ?? '' ?>">
            <textarea name="defect_description" placeholder="Description" required><?= $editData['Defect_Description'] ?? '' ?></textarea>

            <select name="defect_status" required>
              
              <option value="1" <?= (isset($editData['Is_Active']) && $editData['Is_Active'] == 1) ? 'selected' : '' ?>>Active</option>
              <option value="0" <?= (isset($editData['Is_Active']) && $editData['Is_Active'] == 0) ? 'selected' : '' ?>>Inactive</option>
            </select>

            <button type="submit"><?= $editData ? 'Update Defect' : 'Add Defect' ?></button>
          </form>
        </section>

        <!-- List -->
        <section class="overview">
          <h3>Defect List</h3>
          <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="searchBox" placeholder="Search defects..." value="<?= htmlspecialchars($search) ?>" onkeyup="filterDefects(this.value)">
          </div>

          <div class="table-responsive">
            <table>
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Description</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($row = $defects->fetch_assoc()):
                  $jsonRow = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                  $statusText = $row['Is_Active'] == 1 ? 'Active' : 'Inactive';
                  $rowClass = $row['Is_Active'] == 1 ? 'active-row' : 'inactive-row';
                ?>
                  <tr class="<?= $rowClass ?>">
                    <td><?= $row['Defect_Name'] ?></td>
                    <td><?= $row['Defect_Description'] ?></td>
                    <td><?= $statusText ?></td>
                    <td class="action-btns">
                      <i class='fas fa-eye' onclick='viewDetails(<?= $jsonRow ?>)'></i>
                      <a href='?edit=<?= $row['Defect_Id'] ?>'><i class='fas fa-pen'></i></a>
                       
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
      <h3>Defect Details</h3>
      <div id="popupDetails"></div>
    </div>
  </div>

  <script>
    function toggleSidebar() {
      document.getElementById('sidebarMenu').classList.toggle('mobile-hidden');
    }

    function filterDefects(query) {
      const rows = document.querySelectorAll("tbody tr");
      rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(query.toLowerCase()) ? "" : "none";
      });
    }

    function viewDetails(data) {
      const html = `
        <p><strong>ID:</strong> ${data.Defect_Id}</p>
        <p><strong>Name:</strong> ${data.Defect_Name}</p>
        <p><strong>Description:</strong> ${data.Defect_Description}</p>
        <p><strong>Status:</strong> ${data.Is_Active == 1 ? 'Active' : 'Inactive'}</p>
      `;
      document.getElementById("popupDetails").innerHTML = html;
      document.getElementById("popupOverlay").style.display = 'flex';
    }

    function closePopup() {
      document.getElementById("popupOverlay").style.display = 'none';
    }

    function deleteDefect(id) {
      if (confirm("Are you sure you want to delete this defect?")) {
        window.location.href = "?delete=" + id;
      }
    }
  </script>
</body>
</html>