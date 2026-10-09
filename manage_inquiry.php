
<?php
$conn = new mysqli("localhost", "root", "", "sanctify_db");

if ($conn->connect_error) {
    die("Database connection failed.");
}

// Delete inquiry when requested
if ($_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST["delete_id"])) {

    $id = filter_var($_POST["delete_id"], FILTER_VALIDATE_INT);

    if ($id !== false && $id > 0) {
        $stmt = $conn->prepare(
            "DELETE FROM inquiries WHERE id = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: manage_inquiries.php");
    exit;
}

// Display inquiries
$result = $conn->query(
    "SELECT id, name, email, subject, message, created_at
     FROM inquiries ORDER BY created_at DESC"
);

while ($row = $result->fetch_assoc()) {
    echo "<h3>" . htmlspecialchars($row["subject"]) . "</h3>";
    echo "<p>Name: " . htmlspecialchars($row["name"]) . "</p>";
    echo "<p>Email: " . htmlspecialchars($row["email"]) . "</p>";
    echo "<p>" . nl2br(htmlspecialchars($row["message"])) . "</p>";

    echo '<form method="POST"
          onsubmit="return confirm(\'Delete this inquiry?\')">';
    echo '<input type="hidden" name="delete_id" value="'
        . (int)$row["id"] . '">';
    echo '<button type="submit">Delete</button>';
    echo '</form><hr>';
}

$conn->close();
?>
