<!DOCTYPE html>
<html>
<head>
  <title>Add Insurance Entry</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    /* Add the following styles to styles.css if not already present */
    .main-layout {
      display: flex;
      height: 100vh;
    }
    .sidebar {
      width: 220px;
      background-color: #1f2937;
      color: #fff;
      padding: 20px;
    }
    .sidebar h2 {
      font-size: 18px;
      margin-bottom: 20px;
      text-align: center;
    }
    .sidebar a {
      color: #fff;
      display: block;
      margin: 10px 0;
      text-decoration: none;
    }
    .header {
      background-color: #4b5563;
      color: white;
      padding: 10px 20px;
      font-size: 20px;
    }
    .content {
      flex: 1;
      padding: 20px;
      background-color: #f3f4f6;
      overflow-y: auto;
    }
    .form-preview-wrapper {
      display: flex;
      gap: 20px;
    }
    .form-section, .preview-section {
      background-color: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      flex: 1;
    }
    .form-section label {
      font-weight: bold;
      display: block;
      margin-top: 10px;
    }
    .form-section input,
    .form-section select {
      width: 100%;
      padding: 8px;
      margin-bottom: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    .form-section button {
      padding: 10px 20px;
      background-color: #16a34a;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      margin-top: 10px;
    }
    .preview-section img {
      max-width: 100px;
      margin-top: 10px;
    }
  </style>
</head>
<body>
  <div class="main-layout">
    <div class="sidebar">
      <h2>INSURANCE</h2>
      <a href="#">Dashboard</a>
      <a href="#">Customers</a>
      <a href="#">Insurance Entry</a>
      <a href="#">Claims</a>
    </div>
    <div class="content">
      <div class="header">Add Insurance Entry</div>
      <div class="form-preview-wrapper">
        <form class="form-section" action="insert_insurance_entry.php" method="POST" enctype="multipart/form-data">
          <!-- Your form fields (same as your original form) go here -->
          <!-- Use same input names and add necessary PHP/JS logic as in your working file -->
        </form>

        <div class="preview-section">
          <h3>Customer Details</h3>
          <div id="customerDetails">Select a customer to view details.</div>
          <div id="brandDetails">Select a brand to view details.</div>
          <div id="insuranceDetails">Select a plan to view details.</div>
          <h3>Live Preview</h3>
          <div id="previewModel">Model: -</div>
          <div id="previewIMEI1">IMEI 1: -</div>
          <div id="previewIMEI2">IMEI 2: -</div>
          <div id="previewValue">Product Value: -</div>
          <div id="previewPremium">Premium: -</div>
          <div id="previewBillDate">Bill Date: -</div>
          <div id="previewStart">Start Date: -</div>
          <div id="previewEnd">End Date: -</div>
          <div id="previewInsStatus">Insurance Status: -</div>
          <div id="previewProdStatus">Product Insurance Status: -</div>
          <div>
            Product Photo: <br><img id="previewProductPhoto" />
          </div>
          <div>
            Bill Photo: <br><img id="previewBillPhoto" />
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Add your existing <script> from above here -->
</body>
</html>
