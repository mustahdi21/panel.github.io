<?php
session_start();
require("../../mainconfig.php");
$msg_type = "nothing";

if (isset($_SESSION['user'])) {
	$sess_username = $_SESSION['user']['username'];
	$check_user = mysqli_query($db, "SELECT * FROM users WHERE username = '$sess_username'");
	$data_user = mysqli_fetch_assoc($check_user);
	if (mysqli_num_rows($check_user) == 0) {
		header("Location: ".$cfg_baseurl."account/logout");
	} else if ($data_user['status'] == "Suspended") {
		header("Location: ".$cfg_baseurl."account/logout");
	} else if ($data_user['level'] != "Developers") {
		header("Location: ".$cfg_baseurl);
	} else {
		if (isset($_POST['add'])) {
			$post_username = trim($_POST['username']);
			$post_password = $_POST['password'];
			$post_balance = $_POST['balance'];
			$post_level = $_POST['level'];
			$post_email = $_POST['email'];

			$checkdb_user = mysqli_query($db, "SELECT * FROM users WHERE username = '$post_username'");
			$datadb_user = mysqli_fetch_assoc($checkdb_user);
			if (empty($post_username) || empty($post_password)) {
				$msg_type = "error";
				$msg_content = "<b>Gagal:</b> Mohon mengisi semua input.";
			} else if ($post_level != "Member" AND $post_level != "Reseller" AND $post_level != "Admin" AND $post_level != "Agen") {
				$msg_type = "error";
				$msg_content = "<b>Gagal:</b> Input tidak sesuai.";
			} else if (mysqli_num_rows($checkdb_user) > 0) {
				$msg_type = "error";
				$msg_content = "<b>Gagal:</b> Username $post_username sudah terdaftar dalam database.";
			} else if (strlen($post_password) > 12) {
			$msg_type = "error";
			$msg_content = "<b>Failed:</b> New password is too long, at least less than 12 characters.";
		} else {
				$post_api = random(20);
				$insert_user = mysqli_query($db, "INSERT INTO users (username, password, balance, level, registered, status, api_key, email, uplink) VALUES ('$post_username', '$post_password', '$post_balance', '$post_level', '$date', 'Active', '$post_api', '$post_email', '$sess_username')");
				$insert_user = mysqli_query($db, "INSERT INTO hof (username) VALUES ('$post_username')");
				if ($insert_user == TRUE) {
					$msg_type = "success";
					$msg_content = "<b>Berhasil:</b> Pengguna berhasil ditambahkan.<br /><b>Username:</b> $post_username<br /><b>Password:</b> $post_password<br /><b>Level:</b> $post_level<br /><b>Saldo:</b> Rp ".number_format($post_balance,0,',','.');
				} else {
					$msg_type = "error";
					$msg_content = "<b>Gagal:</b> Error system.";
				}
			}
		}

				include("../../lib/header.php");
?>

            <div class="wrapper">
<div class="container-fluid" style="padding: 50px 20px;">
<div class="row">
<div class="col-lg-12">
</div>
</div>
<div class="row">
<div class="col-lg-12">
<div class="card m-b-30">
<h6 class="card-header"><i class="mdi mdi-history"></i> Tambah Pengguna</h6>
<div class="card-body">
										<?php 
										if ($msg_type == "success") {
										?>
										<div class="alert alert-success">
											<a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
											<i class="fa fa-check-circle"></i>
											<?php echo $msg_content; ?>
										</div>
										<?php
										} else if ($msg_type == "error") {
										?>
										<div class="alert alert-danger">
											<a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
											<i class="fa fa-times-circle"></i>
											<?php echo $msg_content; ?>
										</div>
										<?php
										}
										?>
										<form class="form-horizontal" role="form" method="POST">
											<div class="form-group">
												<label class="col-md-2 control-label">Level</label>
												<div class="col-md-10">
													<select class="form-control" name="level">
														<option value="Member">Member</option>
														<option value="Agen">Agen</option>
														<option value="Reseller">Reseller</option>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-2 control-label">E-Mail</label>
												<div class="col-md-10">
													<input type="text" name="email" class="form-control" placeholder="E-Mail aktif">
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-2 control-label">Username</label>
												<div class="col-md-10">
													<input type="text" name="username" class="form-control" placeholder="Username">
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-2 control-label">Password</label>
												<div class="col-md-10">
													<input type="text" name="password" class="form-control" placeholder="Password">
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-2 control-label">Saldo</label>
												<div class="col-md-10">
													<input type="number" name="balance" class="form-control" placeholder="Balance">
												</div>
											</div>
											<div class="form-group">
												<div class="col-md-offset-2 col-md-10">
												<button type="submit" class="btn btn-primary" name="add"><i class="fa fa-plus"></i> Tambah</button>
											<a href="<?php echo $cfg_baseurl; ?>admin/users.php" class="btn btn-warning"><i class="fa fa-refresh"></i> Kembali </a>
											    </div>
											</div>										
										</form>
									</div>
								</div>
							</div>
						</div>
						<!-- end row -->
<?php
	include("../../lib/footer.php");
	}
} else {
	header("Location: ".$cfg_baseurl);
}
?>