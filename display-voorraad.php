<?php
include 'connectie.php'; // Ensure this file is in the correct path

// SQL query to get product details, stock, and locations
$sql = "
SELECT 
    p.product AS product_name, 
    p.type AS product_type,
    v.voorraad AS voorraad,
    l.locatie AS locatie,
    pl.aantal AS aantal_in_locatie
FROM producten p
JOIN voorraad_has_producten vhp ON p.id = vhp.producten_id
JOIN voorraad v ON vhp.voorraad_id = v.id
JOIN producten_has_locaties pl ON p.id = pl.producten_id
JOIN locaties l ON pl.locaties_id = l.id
";

// Execute the query
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Table to display the results
    echo "<table class='table table-striped'>";
    echo "<thead>
            <tr>
                <th>Product Name</th>
                <th>Product Type</th>
                <th>Voorraad</th>
                <th>Locatie</th>
                <th>Aantal in Locatie</th>
            </tr>
          </thead>";
    echo "<tbody>";
    
    // Fetch each row of the result
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['product_name'] . "</td>";
        echo "<td>" . $row['product_type'] . "</td>";
        echo "<td>" . $row['voorraad'] . "</td>";
        echo "<td>" . $row['locatie'] . "</td>";
        echo "<td>" . $row['aantal_in_locatie'] . "</td>";
        echo "</tr>";
        echo "<td>
        <a href='edit.php?id=" . $row['product_id'] . "' class='btn btn-warning btn-sm'>Edit</a>
        <a href='delete.php?id=" . $row['product_id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\")'>Delete</a>
      </td>";
echo "</tr>";
    }
    
    echo "</tbody>";
    echo "</table>";
} else{
    echo "geen producten";
}


$conn->close(); // Close the database connection
?>
