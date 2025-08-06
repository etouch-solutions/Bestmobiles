<?php include 'db.php'; $conn = mysqli_connect($host, $user, $pass, $db); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Add Insurance Entry</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="navtop">
  <div class="logo">LOGO</div>
  <h1><span>➕</span> Add Insurance Entry</h1>
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
        <li><a href="Customer_Master.php">Customer Master</a></li>
        <li><a href="add_insurance.php">Insurance Master</a></li>
        <li><a href="add_defect.php" class="active">Defect Master</a></li>
        <li><a href="Insurance_Entry.php">Insurance Entry</a></li>
        <li><a href="serch.php">Claim</a></li>
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
              <option value="">-- Select Status --</option>
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
                      <a href='javascript:void(0)' onclick='deleteDefect(<?= $row['Defect_Id'] ?>)'><i class='fas fa-trash text-danger'></i></a>
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

<!-- JS -->
<script>
function toggleSidebar(){document.getElementById('sidebarMenu').classList.toggle('mobile-hidden');}
function updatePreview(type, el){
  const val = el.value || '-';
  const map = {model:'previewModel', imei1:'previewIMEI1', imei2:'previewIMEI2', value:'previewValue', bill:'previewBillDate', start:'previewStart', insStatus:'previewInsStatus', prodStatus:'previewProdStatus'};
  document.getElementById(map[type]).innerText = (type==='insStatus'||type==='prodStatus') ? el.options[el.selectedIndex].text : val;
}
function previewImage(inp, id){
  if(!inp.files[0])return;
  const r=new FileReader();
  r.onload=e=>document.getElementById(id).src=e.target.result;
  r.readAsDataURL(inp.files[0]);
}
function calculatePremiumAndEndDate(){
  const ins=document.getElementById('insurance_id'),opt=ins.options[ins.selectedIndex],p=opt.getAttribute('data-premium'),d=opt.getAttribute('data-duration'),val=parseFloat(document.getElementById('product_value').value)||0;
  if(p&&val){const pr=(val*parseFloat(p))/100;document.getElementById('premium_amount').value=pr.toFixed(2);document.getElementById('previewPremium').innerText='₹'+pr.toFixed(2);}
  const sd=document.getElementById('insurance_start').value; if(sd){const dt=new Date(sd);dt.setMonth(dt.getMonth()+parseInt(d));const iso=dt.toISOString().split('T')[0];document.getElementById('insurance_end').value=iso;document.getElementById('previewEnd').innerText=iso;}
}
function loadCustomerDetails(id){ if(!id)return document.getElementById('customerDetails').innerText="Select a customer to view details.";fetch(`fetch_customer.php?cus_id=${id}`).then(r=>r.text()).then(d=>document.getElementById('customerDetails').innerHTML=d);}
function loadBrandDetails(id){ if(!id)return document.getElementById('brandDetails').innerText="Select a brand to view details.";fetch(`fetch_brand.php?brand_id=${id}`).then(r=>r.text()).then(d=>document.getElementById('brandDetails').innerHTML=d);}
function loadInsuranceDetails(id){ if(!id)return document.getElementById('insuranceDetails').innerText="Select a plan to view details.";fetch(`fetch_insurance.php?insurance_id=${id}`).then(r=>r.text()).then(d=>document.getElementById('insuranceDetails').innerHTML=d);}
document.getElementById('insurance_id').addEventListener('change', calculatePremiumAndEndDate);
document.getElementById('product_value').addEventListener('input', calculatePremiumAndEndDate);
document.getElementById('insurance_start').addEventListener('change', calculatePremiumAndEndDate);
</script>

</body>
</html>
