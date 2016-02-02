<div id="account-links">
	<div class="row">
		<div class="col-xs-12">
			<ul class="list-unstyled">
				<?php
				print '<li><a class="btn btn-default" href="index.php">Instructor Contact Table</a></li>';
				if (isset($loggedIn))
				{
					print '<li><a class="btn btn-default" href="change-editor-password.php">Change Password</a></li>';
					print '<li><a class="btn btn-default" href="logout.php">Logout</a></li>';
					
				}
				else
				{
					print '<li><a class="btn btn-default" href="login.php">Editor Login</a></li>';
					print '<li><a class="btn btn-default" href="add-editor.php">Create New Editor Account</a></li>';
				}
				?>
			</ul>
		</div>
	</div>
</div>