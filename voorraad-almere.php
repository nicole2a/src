<?php
include 'connectie.php'; // Connect to the database

// Fetch data
$sql_select = "SELECT l.naam AS locatie, p.product, p.type, p.fabriek, v.aantal
FROM voorraad v
JOIN producten p ON v.product_id = p.id
JOIN locaties l ON v.locatie_id = l.id
ORDER BY l.naam";

$result = $conn->query($sql_select);

$current_location = "almere";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if ($current_location !== $row['locatie']) {
            if ($current_location !== "almere") {
                echo "</tbody></table>"; // Close previous table if exists
            }
            $current_location = $row['locatie'];
            echo "<h3>$current_location</h3>"; // Display the location as a header
            echo "<table class='table table-bordered'>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Type</th>
                            <th>Fabriek</th>
                            <th>Aantal</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>";
        }
        echo "<tr>
                <td>{$row['product']}</td>
                <td>{$row['type']}</td>
                <td>{$row['fabriek']}</td>
                <td>{$row['aantal']}</td>
                <td>
                    <button class='btn btn-warning btn-sm' data-bs-toggle='modal' data-bs-target='#editModal'
                            onclick='loadProductData({$row['id']}, \"{$row['product']}\", \"{$row['type']}\", \"{$row['fabriek']}\")'>Edit</button>
                    <a href='delete.php?id={$row['id']}' class='btn btn-danger btn-sm'>Delete</a>
                </td>
              </tr>";
    }
    echo "</tbody></table>"; // Close the last table
} else {
    echo "<div class='alert alert-info'>No products found.</div>";
}

$conn->close();
?>
