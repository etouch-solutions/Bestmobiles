<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Users List</title>
</head>
<body>

<a href="branch_form.html">add a branch</a> <br>
<a href="add_staff.html">add a staff</a> <br>
<a href="add_brand.html">add a brand</a> <br>
<br>
<a href="add_defect.html">add a defect</a> <br>
<a href="add_insurance.html">add an insurance</a> <br>

    <h2>All Users</h2>
    <a href="add.php">Add New User</a>
    <table border="1" cellpadding="10">
        <tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Actions</th></tr>
        <?php
        $result = $conn->query("SELECT * FROM users");
        while($row = $result->fetch_assoc()){
            echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['name 1`']}</td>
                <td>{$row['email']}</td>
                <td>{$row['phone']}</td>
                <td>
                    <a href='edit.php?id={$row['id']}'>Edit</a> |
                    <a href='delete.php?id={$row['id']}'>Delete</a>
                </td>
            </tr>";
        }
        ?>
    </table>

    <h2>Upload Customer Photo and ID</h2>
    <form action="sample_upload.php" method="POST" enctype="multipart/form-data">
        Name: <input type="text" name="name" required><br><br>
        Photo: <input type="file" name="photo" required><br><br>
        ID Copy: <input type="file" name="id_copy" required><br><br>
        <input type="submit" name="submit" value="Upload">
    </form>   

<h2>Uploaded Customers</h2>
<?php
$result = $conn->query("SELECT * FROM sample");
while($row = $result->fetch_assoc()){
    echo "<b>Name:</b> {$row['name']}<br>";
    echo "<b>Photo:</b><br><img src='data:image/jpeg;base64," . base64_encode($row['photo']) . "' height='100'><br>";
    echo "<b>ID Copy:</b><br><img src='data:image/jpeg;base64," . base64_encode($row['id_copy']) . "' height='100'><br><hr>";
}
?>

    
</body>
</html>
