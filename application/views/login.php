<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Login</title>
        <link href="<?php echo base_url('public/bootstrap/css/bootstrap.min.css')?>" rel="stylesheet">
        <link href="<?php echo base_url('public/datatables/css/dataTables.bootstrap.css')?>" rel="stylesheet">
        <link href="<?php echo base_url('public/css/login.css')?>" rel="stylesheet">
        <script src="<?php echo base_url('public/jquery/jquery-3.1.0.js')?>"></script>
        <script src="<?php echo base_url('public/bootstrap/js/bootstrap.min.js')?>"></script>
    </head>
    <body>
        <div class"container"></div>


        <div class="login">

            <div class="login-screen">

                <div class="app-title">
    				<h1>Login</h1>
    			</div>

    			<div class="login-form">
                    <form action=<?php echo site_url("auth/login")?> method="post">
        				<div class="control-group">
        				    <input type="text" class="login-field" name="username" value="" placeholder="username" id="login-name">
        				    <label class="login-field-icon fui-user" for="login-name"></label>
        				</div>

        				<div class="control-group">
        				    <input type="password" name="password" class="login-field" value="" placeholder="password" id="login-pass">
        				    <label class="login-field-icon fui-lock" for="login-pass"></label>
        				</div>
        				<input id="submit" type="submit" class="btn btn-primary btn-large btn-block" value="login">
                        <?php if(isset($error_msg)): ?>
                            <p class="text-danger"><?php echo $error_msg;?></p>
                        <?php endif; ?>
                    </form>
    			</div>
    		</div>
	    </div>
        <div id="footer"></div>
    </body>
</html>
