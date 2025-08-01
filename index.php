<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="styles.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body>
  
 






    <div class="navtop">
    
    <div class="logo">LOGO</div>
    <h1> Best Mobile Insurance Software</h1>
    <div class="hamburger" onclick="toggleSidebar()">☰</div>
  </div>

  <div class="container">
    <aside class="sidebar mobile-hidden" id="sidebarMenu">
      <ul>
      <a style="text-decoration: none; color: #144d30ff; font-weight: 500; font-size: 14px;"  href="index.php"><li>Dashboard</li></a>
     <a style="text-decoration: none; color: #144d30ff; font-weight: 500; font-size: 14px;" href="branch.php" class="active"> <li>Branch Master</li></a>
     <a style="text-decoration: none; color: #144d30ff; font-weight: 500; font-size: 14px;"  href="brand.php" > <li>Brand Master</li></a>
      <a style="text-decoration: none; color: #144d30ff; font-weight: 500; font-size: 14px;"  href="add_staff.php"><li>Staff Master</li></a>
      <a style="text-decoration: none; color: #144d30ff; font-weight: 500; font-size: 14px;"  href="Customer_Master.php"><li>Customer Master</li></a>
      <a style="text-decoration: none; color: #144d30ff; font-weight: 500; font-size: 14px;"  href="add_insurance.php"><li>Insurance Master</li></a>
      <a style="text-decoration: none; color: #144d30ff; font-weight: 500; font-size: 14px;"  href="add_defect.php"><li>Defect Master</li></a>
      <a style="text-decoration: none; color: #144d30ff; font-weight: 500; font-size: 14px;"  href="add_insurance.php"><li>Insurance Entry</li></a>
      <a style="text-decoration: none; color: #144d30ff; font-weight: 500; font-size: 14px;"  href="serch.php"><li>Claim</li></a>
      </ul>
    </aside>
</div>
 

  <script src="script.js"></script>
</body>
</html>
