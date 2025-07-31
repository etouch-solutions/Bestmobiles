<?php
// brand.php
include 'db.php';
$conn = mysqli_connect($host, $user, $pass, $db);

// Insert or Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['brand_name'])) {
    $brandName = mysqli_real_escape_string($conn, $_POST['brand_name']);
    $isActive = intval($_POST['is_active']);
    $brandId = $_POST['brand_id'] ?? null;

    if ($brandId) {
        // Update
        $stmt = $conn->prepare("UPDATE Brands_Master SET Brand_Name = ?, Is_Active = ? WHERE Brand_Id = ?");
        $stmt->bind_param("sii", $brandName, $isActive, $brandId);
    } else {
        // Insert
        $stmt = $conn->prepare("INSERT INTO Brands_Master (Brand_Name, Is_Active) VALUES (?, ?)");
        $stmt->bind_param("si", $brandName, $isActive);
    }

    $stmt->execute();
    $stmt->close();
    header("Location: brand.php");
    exit();
}

// Delete
if (isset($_GET['delete'])) {
    $deleteId = intval($_GET['delete']);
    $conn->query("DELETE FROM Brands_Master WHERE Brand_Id = $deleteId");
    header("Location: brand.php?deleted=1");
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
<html>
<head>
  <title>Manage Brands</title>
  <style>
    body { font-family: Arial; background: #f2f2f2; padding: 20px; }
    .container { display: flex; gap: 20px; }
    .form-box, .list-box { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 5px #aaa; width: 50%; }
    input, select, button { padding: 8px; width: 100%; margin: 10px 0; }
    .brand-item { cursor: pointer; padding: 5px; border-bottom: 1px solid #ddd; }
    .brand-item:hover { background: #f9f9f9; }
    #brandDetails { margin-top: 20px; }
    .search-box { margin-bottom: 10px; }
    .actions { font-size: 12px; margin-top: 5px; }
    .actions a { margin-right: 10px; text-decoration: none; color: blue; }
    .actions a.delete { color: red; }
  </style>
</head>
<body>
<h2>Brand Master</h2>
<div class="container">
  <!-- Form Box -->
  <div class="form-box">
    <h3><?= $editBrand ? 'Edit Brand' : 'Add Brand' ?></h3>
    <form method="POST">
      <?php if ($editBrand): ?>
        <input type="hidden" name="brand_id" value="<?= $editBrand['Brand_Id'] ?>">
      <?php endif; ?>

      <label>Brand Name:</label>
      <input type="text" name="brand_name" required value="<?= $editBrand['Brand_Name'] ?? '' ?>">

      <label>Status:</label>
      <select name="is_active">
        <option value="1" <?= (isset($editBrand['Is_Active']) && $editBrand['Is_Active'] == 1) ? 'selected' : '' ?>>Active</option>
        <option value="0" <?= (isset($editBrand['Is_Active']) && $editBrand['Is_Active'] == 0) ? 'selected' : '' ?>>Inactive</option>
      </select>

      <button type="submit"><?= $editBrand ? 'Update Brand' : 'Add Brand' ?></button>
    </form>
  </div>

  <!-- Brand List -->
  <div class="list-box">
    <h3>All Brands</h3>
    <input type="text" placeholder="Search brand..." class="search-box" onkeyup="filterBrands(this.value)">
    <div id="brandList">
      <?php
        $res = mysqli_query($conn, "SELECT * FROM Brands_Master ORDER BY Brand_Id DESC");
        while ($row = mysqli_fetch_assoc($res)) {
          $jsonRow = json_encode($row);
          echo "<div class='brand-item' onclick='viewDetails($jsonRow)'>
                  {$row['Brand_Name']}
                  <div class='actions'>
                    <a href='?edit={$row['Brand_Id']}'>Edit</a>
                    <a href='javascript:void(0)' class='delete' onclick='deleteBrand({$row['Brand_Id']})'>Delete</a>
                  </div>
                </div>";
        }
      ?>
    </div>
    <div id="brandDetails"></div>
  </div>
</div>

<script>
function filterBrands(query) {
  const items = document.querySelectorAll('.brand-item');
  items.forEach(item => {
    item.style.display = item.innerText.toLowerCase().includes(query.toLowerCase()) ? 'block' : 'none';
  });
}

function viewDetails(brand) {
  const html = `
    <h4>Brand Details</h4>
    <p><strong>ID:</strong> ${brand.Brand_Id}</p>
    <p><strong>Name:</strong> ${brand.Brand_Name}</p>
    <p><strong>Status:</strong> ${brand.Is_Active == 1 ? 'Active' : 'Inactive'}</p>
  `;
  document.getElementById("brandDetails").innerHTML = html;
}

function deleteBrand(id) {
  if (confirm("Are you sure you want to delete this brand?")) {
    window.location.href = "?delete=" + id;
  }
}
</script>
</body>
</html>
