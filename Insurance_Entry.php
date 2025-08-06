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
