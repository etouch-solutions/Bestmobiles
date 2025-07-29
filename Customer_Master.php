<?php
include 'db.php';
$conn = mysqli_connect($host, $user, $pass, $db);
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle Insert or Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['cus_id'] ?? null;
    $name = $_POST['cus_name'];
    $cno = $_POST['cus_cno'];
    $email = $_POST['cus_email'];
    $address = $_POST['cus_address'];
    $ref = $_POST['cus_ref'];
    $ref_cno = $_POST['cus_ref_cno'];
    $branch_id = $_POST['branch_id'];
    $status = $_POST['cus_status'];

    // File uploads
    $photo = $_FILES['cus_photo']['name'] ?? '';
    $id_copy = $_FILES['cus_id_copy']['name'] ?? '';

    $photoPath = '';
    $idPath = '';

    if ($photo) {
        $photoPath = 'uploads/' . time() . '_' . basename($photo);
        move_uploaded_file($_FILES['cus_photo']['tmp_name'], $photoPath);
    }

    if ($id_copy) {
        $idPath = 'uploads/' . time() . '_' . basename($id_copy);
        move_uploaded_file($_FILES['cus_id_copy']['tmp_name'], $idPath);
    }

    if ($id) {
        // UPDATE
        $query = "UPDATE Customer_Master SET 
            Cus_Name=?, Cus_CNo=?, Cus_Email=?, Cus_Address=?, 
            Cus_Ref_Name=?, Cus_Ref_CNo=?, Branch_Id=?, Cus_Status=?";
        if ($photoPath) $query .= ", Cus_Photo=?";
        if ($idPath) $query .= ", Cus_Id_Copy=?";
        $query .= " WHERE Cus_Id=?";

        $stmt = $conn->prepare($query);
        if ($photoPath && $idPath) {
            $stmt->bind_param("sisssiiissi", $name, $cno, $email, $address, $ref, $ref_cno, $branch_id, $status, $photoPath, $idPath, $id);
        } elseif ($photoPath) {
            $stmt->bind_param("sisssiiisi", $name, $cno, $email, $address, $ref, $ref_cno, $branch_id, $status, $photoPath, $id);
        } elseif ($idPath) {
            $stmt->bind_param("sisssiiisi", $name, $cno, $email, $address, $ref, $ref_cno, $branch_id, $status, $idPath, $id);
        } else {
            $stmt->bind_param("sisssiii", $name, $cno, $email, $address, $ref, $ref_cno, $branch_id, $status, $id);
        }
    } else {
        // INSERT
        $stmt = $conn->prepare("INSERT INTO Customer_Master 
            (Cus_Name, Cus_CNo, Cus_Email, Cus_Address, Cus_Ref_Name, Cus_Ref_CNo, Branch_Id, Cus_Photo, Cus_Id_Copy, Cus_Status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sisssisssi", $name, $cno, $email, $address, $ref, $ref_cno, $branch_id, $photoPath, $idPath, $status);
    }

    $stmt->execute();
    $stmt->close();
    header("Location: customer_master.php");
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $delId = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM Customer_Master WHERE Cus_Id=$delId");
    header("Location: customer_master.php");
    exit;
}

// Fetch for edit
$edit = null;
if (isset($_GET['edit'])) {
    $eid = intval($_GET['edit']);
    $res = mysqli_query($conn, "SELECT * FROM Customer_Master WHERE Cus_Id=$eid");
    $edit = mysqli_fetch_assoc($res);
}

// Fetch branches
$branches = mysqli_query($conn, "SELECT * FROM Branch_Master WHERE Branch_Status = 1");

// Search and customer list
$search = $_GET['search'] ?? '';
$condition = $search ? "WHERE Cus_Name LIKE '%$search%' OR Cus_CNo LIKE '%$search%'" : '';
$customers = mysqli_query($conn, "SELECT * FROM Customer_Master $condition ORDER BY Cus_Id DESC");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Customer Master</title>
  <style>
    body { font-family: Arial; background: #f2f2f2; display: flex; padding: 20px; gap: 20px; }
    .form-box, .list-box, .preview-box {
      background: white; padding: 20px; border-radius: 10px;
      box-shadow: 0 0 5px rgba(0,0,0,0.1);
    }
    .form-box { width: 35%; }
    .list-box { width: 30%; overflow-y: auto; height: 90vh; }
    .preview-box { width: 30%; }
    input, select, textarea { width: 100%; padding: 8px; margin-bottom: 10px; }
    .item { padding: 8px; border-bottom: 1px solid #ddd; cursor: pointer; }
    .item:hover { background: #f9f9f9; }
    .actions a { margin-right: 10px; color: blue; text-decoration: none; }
    .actions a.delete { color: red; }
  </style>
  <script>
    function showPreview(data) {
      document.getElementById('preview').innerHTML = `
        <h3>Customer Preview</h3>
        <p><b>Name:</b> ${data.name}</p>
        <p><b>Contact:</b> ${data.cno}</p>
        <p><b>Email:</b> ${data.email}</p>
        <p><b>Address:</b> ${data.address}</p>
        <p><b>Ref:</b> ${data.ref} (${data.ref_cno})</p>
        <p><b>Branch:</b> ${data.branch}</p>
        <p><b>Status:</b> ${data.status == 1 ? 'Active' : 'Inactive'}</p>
        ${data.photo ? `<p><b>Photo:</b><br><img src='${data.photo}' width='100'></p>` : ''}
        ${data.id_copy ? `<p><b>ID Copy:</b><br><img src='${data.id_copy}' width='100'></p>` : ''}
      `;
    }

    function confirmDelete(id) {
      if (confirm("Delete this customer?")) {
        location.href = "?delete=" + id;
      }
    }
  </script>
</head>
<body>

<!-- Customer Form -->
<div class="form-box">
  <h3><?= $edit ? 'Edit' : 'Add' ?> Customer</h3>
  <form method="POST" enctype="multipart/form-data">
    <?php if ($edit): ?>
      <input type="hidden" name="cus_id" value="<?= $edit['Cus_Id'] ?>">
    <?php endif; ?>
    <label>Name</label>
    <input type="text" name="cus_name" required value="<?= $edit['Cus_Name'] ?? '' ?>">
    
    <label>Contact No</label>
    <input type="number" name="cus_cno" required value="<?= $edit['Cus_CNo'] ?? '' ?>">

    <label>Email</label>
    <input type="email" name="cus_email" value="<?= $edit['Cus_Email'] ?? '' ?>">

    <label>Address</label>
    <textarea name="cus_address"><?= $edit['Cus_Address'] ?? '' ?></textarea>

    <label>Reference Name</label>
    <input type="text" name="cus_ref" value="<?= $edit['Cus_Ref_Name'] ?? '' ?>">

    <label>Reference Contact</label>
    <input type="number" name="cus_ref_cno" value="<?= $edit['Cus_Ref_CNo'] ?? '' ?>">

    <label>Branch</label>
    <select name="branch_id" required>
      <option value="">-- Select Branch --</option>
      <?php while ($b = mysqli_fetch_assoc($branches)): ?>
        <option value="<?= $b['Branch_Id'] ?>" <?= (isset($edit['Branch_Id']) && $edit['Branch_Id'] == $b['Branch_Id']) ? 'selected' : '' ?>>
          <?= $b['Branch_Name'] ?>
        </option>
      <?php endwhile; ?>
    </select>

    <label>Photo <?= $edit ? '(Leave blank to keep existing)' : '' ?></label>
    <input type="file" name="cus_photo" accept="image/*">

    <label>ID Copy <?= $edit ? '(Leave blank to keep existing)' : '' ?></label>
    <input type="file" name="cus_id_copy" accept="image/*">

    <label>Status</label>
    <select name="cus_status">
      <option value="1" <?= (isset($edit['Cus_Status']) && $edit['Cus_Status'] == 1) ? 'selected' : '' ?>>Active</option>
      <option value="0" <?= (isset($edit['Cus_Status']) && $edit['Cus_Status'] == 0) ? 'selected' : '' ?>>Inactive</option>
    </select>

    <button type="submit"><?= $edit ? 'Update' : 'Add' ?> Customer</button>
  </form>
</div>

<!-- Customer List -->
<div class="list-box">
  <h3>Customers</h3>
  <form method="GET">
    <input type="text" name="search" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">
  </form>
  <hr>
  <?php while ($c = mysqli_fetch_assoc($customers)):
    $safeName = htmlspecialchars($c['Cus_Name'], ENT_QUOTES);
  ?>
    <div class="item" onclick='showPreview({
      name: "<?= $safeName ?>",
      cno: "<?= $c['Cus_CNo'] ?>",
      email: "<?= $c['Cus_Email'] ?>",
      address: "<?= $c['Cus_Address'] ?>",
      ref: "<?= $c['Cus_Ref_Name'] ?>",
      ref_cno: "<?= $c['Cus_Ref_CNo'] ?>",
      branch: "<?= $c['Branch_Id'] ?>",
      status: "<?= $c['Cus_Status'] ?>",
      photo: "<?= $c['Cus_Photo'] ?>",
      id_copy: "<?= $c['Cus_Id_Copy'] ?>"
    })'>
      <b><?= $c['Cus_Name'] ?></b>
      <div class="actions">
        <a href="?edit=<?= $c['Cus_Id'] ?>">Edit</a>
        <a href="javascript:void(0)" class="delete" onclick="confirmDelete(<?= $c['Cus_Id'] ?>)">Delete</a>
      </div>
    </div>
  <?php endwhile; ?>
</div>

<!-- Preview Panel -->
<div class="preview-box" id="preview">
  <h3>Customer Preview</h3>
  <p>Select a customer to view details.</p>
</div>

</body>
</html>
