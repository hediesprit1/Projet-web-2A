<?php 

if (isset($_POST['first_name']) &&
	isset($_POST['password'])  &&
	isset($_POST['id'])  &&
	isset($_POST['email']) &&
	isset($_POST['role']) 
   ) {
	include "../db_conn.php";

	$first_name = $_POST['first_name'];
	$last_name = $_POST['password'];
	$email = $_POST['email'];
	$id = $_POST['id'];
	$role = $_POST['role'];
    

	if (empty($first_name) || 
		empty($password)  || 
		empty($id)         || 
		empty($email))  {
		$em = "Please fill out all fields";
	   header("Location: ../update.php?error=$em&id=$id");
	}else {
        $sql = "UPDATE users SET first_name=?, password=?, email=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$first_name, $password, $email, $id]);

        $sm = "Successfully updated";
	    header("Location: ../update.php?success=$sm&id=$id");
	}
}