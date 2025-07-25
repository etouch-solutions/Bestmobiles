<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Users List</title>
</head>
<body>
    <h2>All Users</h2>
    <a href="add.php">Add New User</a>
    <table border="1" cellpadding="10">
        <tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Actions</th></tr>
        <?php
        $result = $conn->query("SELECT * FROM users");
        while($row = $result->fetch_assoc()){
            echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['NAME']}</td>
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
</body>
</html>
