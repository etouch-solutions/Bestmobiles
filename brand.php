<?php
// brand_master.php
include 'db.php';
$conn = mysqli_connect($host, $user, $pass, $db);

// Insert logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['brand_name'])) {
    $name = mysqli_real_escape_string($conn, $_POST['brand_name']);
    $status = $_POST['is_active'];
    mysqli_query($conn, "INSERT INTO Brands_Master (Brand_Name, Is_Active) VALUES ('$name', '$status')");
    header("Location: brand.php");
    exit();
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
  </style>
</head>
<body>
<h2>Brand Master</h2>
<div class="container">
  <!-- Form Box -->
  <div class="form-box">
    <h3>Add Brand</h3>
    <form method="POST">
      <label>Brand Name:</label>
      <input type="text" name="brand_name" required>

      <label>Status:</label>
      <select name="is_active">
        <option value="1">Active</option>
        <option value="0">Inactive</option>
      </select>

      <button type="submit">Add Brand</button>
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
          echo "<div class='brand-item' onclick='viewDetails(".json_encode($row).")'>{$row['Brand_Name']}</div>";
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
</script>
</body>
</html>
