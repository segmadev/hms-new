<?php
    require_once "include/auth-ini.php";
    require_once "content/header/head.php"; ?>
<body>
    <div class="main-wrapper account-wrapper">
        <div class="account-page">
			<div class="account-center">
				<div class="account-box">
                    <form action="auth" class="form-signin" method="POST" autocomplete="off" id="foo">
                       
						<div class="account-logo">
                            <a href=""><img src="assets/img/logo-dark.png" alt=""></a>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" autofocus="" placeholder="email@holder.host" value="<?php if(isset($_POST['email'])){ echo $_POST['email']; } ?>" name="email" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" placeholder="****" class="form-control">
                        </div>
                        <div class="form-group text-right">
                            <a href="forgot-password.html">Forgot your password?</a>
                        </div>
                        <input type="hidden" name="signin" value="1">
                        <div class="form-group text-center">
                            <button type="submit" name="login" class="btn btn-primary account-btn">Login</button>
                        </div>
                    </form>
                </div>
			</div>
        </div>
    </div>
    <script src="assets/js/jquery-3.2.1.min.js"></script>
	<script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/app.js"></script>
    <script src="js/v1/my.js"></script>
</body>


<!-- login23:12-->
</html>