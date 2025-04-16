<?php 
if (isset($_GET['id'])) {
	include "../db_conn.php";
	$id = $_GET['id'];
	$sql = "DELETE FROM users WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $sm = "Successfully Deleted";
	header("Location: ../indexB.php?success=$sm");
}else {
	header("Location: ../indexB.php");
}

 ?>