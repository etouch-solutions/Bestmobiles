<?php
include 'db.php';
$conn = mysqli_connect($host, $user, $pass, $db);

// Insert or Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['brand_name'])) {
    $brandName = mysqli_real_escape_string($conn, $_POST['brand_name']);
    $isActive = intval($_POST['is_active']);
    $brandId = $_POST['brand_id'] ?? null;

    if ($brandId) {
        $stmt = $conn->prepare("UPDATE Brands_Master SET Brand_Name = ?, Is_Active = ? WHERE Brand_Id = ?");
        $stmt->bind_param("sii", $brandName, $isActive, $brandId);
    } else {
        $stmt = $conn->prepare("INSERT INTO Brands_Master (Brand_Name, Is_Active) VALUES (?, ?)");
        $stmt->bind_param("si", $brandName, $isActive);
    }

    $stmt->execute();
    $stmt->close();
    header("Location: brand-master.php");
    exit();
}

// Delete
if (isset($_GET['delete'])) {
    $deleteId = intval($_GET['delete']);
    $conn->query("DELETE FROM Brands_Master WHERE Brand_Id = $deleteId");
    header("Location: brand-master.php?deleted=1");
    exit();
}

// Edit fetch
$editBrand = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $res = $conn->query("SELECT * FROM Brands_Master WHERE Brand_Id = $editId");
    if ($res && $res->num_rows > 0) {
        $editBrand = $res->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Brand Master</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body>
  <div class="navtop">
    <div class="logo">LOGO</div>
    <h1>Best Mobile Insurance Software</h1>
    <div class="hamburger" onclick="toggleSidebar()">☰</div>
  </div>

  <div class="container">
    <aside class="sidebar mobile-hidden" id="sidebarMenu">
      <ul>
        <<a style="text-decoration: none; color: #144d30ff; font-weight: 500; font-size: 14px;"  href="index.php"><li>Dashboard</li></a>
     <a style="text-decoration: none; color: #144d30ff; font-weight: 500; font-size: 14px;" href="branch.php" class="active"> <li>Branch Master</li></a>
     <a style="text-decoration: none; color: #144d30ff; font-weight: 500; font-size: 14px;"  href="brand.php" > <li>Brand Master</li></a>
      <a style="text-decoration: none; color: #144d30ff; font-weight: 500; font-size: 14px;"  href="add_staff.php"><li>Staff Master</li></a>
      <a style="text-decoration: none; color: #144d30ff; font-weight: 500; font-size: 14px;"  href="Customer_Master.php"><li>Customer Master</li></a>
      <a style="text-decoration: none; color: #144d30ff; font-weight: 500; font-size: 14px;"  href="add_insurance.php"><li>Insurance Master</li></a>
      <a style="text-decoration: none; color: #144d30ff; font-weight: 500; font-size: 14px;"  href="add_defect.php"><li>Defect Master</li></a>
      <a style="text-decoration: none; color: #144d30ff; font-weight: 500; font-size: 14px;"  href="insurance_entry.php"><li>Insurance Entry</li></a>
      <a style="text-decoration: none; color: #144d30ff; font-weight: 500; font-size: 14px;"  href="serch.php"><li>Claim</li></a>
      </ul>
    </aside>

    <main class="main-content">
      <div class="content-area">
        <!-- Brand Form -->
        <section class="add-branch">
          <h3><?= $editBrand ? 'Edit Brand' : 'Add Brand' ?></h3>
          <form method="POST">
            <?php if ($editBrand): ?>
              <input type="hidden" name="brand_id" value="<?= $editBrand['Brand_Id'] ?>">
            <?php endif; ?>
            <input type="text" name="brand_name" id="brandName" placeholder="Brand Name" required value="<?= $editBrand['Brand_Name'] ?? '' ?>">
            <select name="is_active" id="status">
              <option value="">Select Status</option>
              <option value="1" <?= (isset($editBrand['Is_Active']) && $editBrand['Is_Active'] == 1) ? 'selected' : '' ?>>Active</option>
              <option value="0" <?= (isset($editBrand['Is_Active']) && $editBrand['Is_Active'] == 0) ? 'selected' : '' ?>>Inactive</option>
            </select>
            <button type="submit"><?= $editBrand ? 'Update Brand' : 'Add Brand' ?></button>
          </form>
        </section>

        <!-- Brand Overview -->
        <section class="overview">
          <h3>Brand Overview</h3>
          <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="brandSearch" placeholder="Search by name..." onkeyup="filterBrands()" />
          </div>

          <div class="table-responsive">
            <table id="brandTable">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  $res = mysqli_query($conn, "SELECT * FROM Brands_Master ORDER BY Brand_Id DESC");
                  while ($row = mysqli_fetch_assoc($res)) {
                    $statusText = $row['Is_Active'] == 1 ? 'Active' : 'Inactive';
                    $statusClass = $row['Is_Active'] == 1 ? 'active-row' : 'inactive-row';
                    echo "<tr class='$statusClass'>
                            <td>{$row['Brand_Name']}</td>
                            <td>$statusText</td>
                            <td class='action-btns'>
                              <a href='?edit={$row['Brand_Id']}'><i class='fa fa-pen'></i></a>
                              <i class='fa fa-eye' onclick='viewDetails(" . json_encode($row) . ")'></i>
                              <i class='fa fa-trash' onclick='deleteBrand({$row['Brand_Id']})'></i>
                            </td>
                          </tr>";
                  }
                ?>
              </tbody>
            </table>
          </div>
        </section>
      </div>
    </main>
  </div>

  <!-- Brand Details Popup -->
  <div class="popup-overlay" id="popupOverlay">
    <div class="popup-content" id="popupContent">
      <span class="close-btn" onclick="closePopup()">&times;</span>
      <h3>Brand Details</h3>
      <div id="popupDetails"></div>
    </div>
  </div>

  <script>
    function filterBrands() {
      const input = document.getElementById('brandSearch').value.toLowerCase();
      const rows = document.querySelectorAll('#brandTable tbody tr');
      rows.forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(input) ? '' : 'none';
      });
    }

    function viewDetails(brand) {
      const html = `
        <p><strong>ID:</strong> ${brand.Brand_Id}</p>
        <p><strong>Name:</strong> ${brand.Brand_Name}</p>
        <p><strong>Status:</strong> ${brand.Is_Active == 1 ? 'Active' : 'Inactive'}</p>
      `;
      document.getElementById('popupDetails').innerHTML = html;
      document.getElementById('popupOverlay').style.display = 'flex';
    }

    function closePopup() {
      document.getElementById('popupOverlay').style.display = 'none';
    }

    function deleteBrand(id) {
      if (confirm("Are you sure you want to delete this brand?")) {
        window.location.href = "?delete=" + id;
      }
    }

    function toggleSidebar() {
      const sidebar = document.getElementById("sidebarMenu");
      sidebar.classList.toggle("mobile-hidden");
      sidebar.classList.toggle("mobile-visible");
    }
  </script>
</body>
</html>
