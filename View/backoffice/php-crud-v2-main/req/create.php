<?php 

if (isset($_POST['first_name']) &&
	isset($_POST['password'])  &&
	isset($_POST['email']) && isset($_POST['role']) 
   ) {
	include "../db_conn.php";

	$first_name = $_POST['first_name'];
	$password= $_POST['password'];
	$email = $_POST['email'];
    $role = $_POST['role'];

	if (empty($first_name) || 
		empty($password)  || 
		empty($email)) {
		$em = "Please fill out all fields";
	   header("Location: ../create.php?error=$em");
	}else {
        $sql = "INSERT INTO users(first_name, password, email,role) VALUES (?,?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$first_name, $password, $email, $role]);

        $sm = "Successfully created";
	    header("Location: ../create.php?success=$sm");
	}
}