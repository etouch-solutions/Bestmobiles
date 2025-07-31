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
      <li><a style="text-decoration: none; color: #135031ff; font-weight: 500; font-size: 14px;"  href="#">Dashboard</a></li>
     <a style="text-decoration: none; color: #2f855a; font-weight: 500; font-size: 14px;" href="branch.php" class="active"> <li>Branch Master</li></a>
     <a style="text-decoration: none; color: #2f855a; font-weight: 500; font-size: 14px;"  href="brand.php" > <li>Brand Master</li></a>
      <a style="text-decoration: none; color: #2f855a; font-weight: 500; font-size: 14px;"  href="add_staff.php"><li>Staff Master</li></a>
      <a style="text-decoration: none; color: #2f855a; font-weight: 500; font-size: 14px;"  href="Customer_Master.php"><li>Customer Master</li></a>
      <a style="text-decoration: none; color: #2f855a; font-weight: 500; font-size: 14px;"  href="add_insurance.php"><li>Insurance Master</li></a>
      <a style="text-decoration: none; color: #2f855a; font-weight: 500; font-size: 14px;"  href="add_defect.php"><li>Defect Master</li></a>
      <a style="text-decoration: none; color: #2f855a; font-weight: 500; font-size: 14px;"  href="insurance_entry.php"><li>Insurance Entry</li></a>
      <a style="text-decoration: none; color: #2f855a; font-weight: 500; font-size: 14px;"  href="serch.php"><li>Claim</li></a>
      </ul>
    </aside>

    <main class="main-content">
      <div class="content-area">
        <section class="add-branch">
          <h3>Add Branch</h3>
          <form id="branchForm">
            <input type="text" id="branchName" placeholder="Branch Name" required />
            <input type="text" id="branchHead" placeholder="Branch Head Name" required />
            <textarea id="branchAddress" placeholder="Branch Address" required></textarea>
            <input type="number" id="contactNumber" placeholder="Contact Number" required />
            <select id="status">
              <option value="">Select Status</option>
              <option value="Active">Active</option>
              <option value="Inactive">Inactive</option>
            </select>
            <button type="submit">Add Branch</button>
          </form>
        </section>

        <section class="overview">
          <h3>Branch Overview</h3>
          <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="searchInput" placeholder="Search by name..." />
          </div>

          <div class="table-responsive">
            <table id="branchTable">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Contact No</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </section>
      </div>
    </main>
  </div>

  <!-- Popup Modal -->
  <div class="popup-overlay" id="popupOverlay">
    <div class="popup-content" id="popupContent">
      <span class="close-btn" onclick="closePopup()">&times;</span>
      <h3>Branch Details</h3>
      <div id="popupDetails"></div>
    </div>
  </div>

  <script src="script.js"></script>
</body>
</html>
