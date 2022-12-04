<?php $armadillo = Slim::getInstance(); ?>
<div class="row">
	<div class="loginForm col-xs-12 col-sm-4 col-md-4 col-md-offset-4">
		<div class="panel panel-default">
		    <div class="loginFormTab panel-heading"><h3 class="panel-title"><?php echo Armadillo_Language::msg('ARM_LOGIN_FORM_TAB_TITLE'); ?></h3></div>
		    <div class="panel-body">
			    <form action="./../" method="post">
			    	<div class="form-group">
				        <label for="name"><?php echo Armadillo_Language::msg('ARM_LOGIN_FORM_USERNAME_FIELD'); ?></label>
				        <input class="form-control" type="text" name="username" placeholder="<?php echo Armadillo_Language::msg('ARM_LOGIN_FORM_USERNAME_FIELD'); ?>" autocapitalize="none" />
			        </div>
			        <div class="form-group">
				        <label for="password"><?php echo Armadillo_Language::msg('ARM_LOGIN_FORM_PASSWORD_FIELD'); ?></label>
				        <input class="form-control" type="password" name="password" placeholder="<?php echo Armadillo_Language::msg('ARM_LOGIN_FORM_PASSWORD_FIELD'); ?>" />
			        </div>
			        <input type="hidden" id="armadilloURL" name="armadilloURL" value="<?php echo armadilloURL(); ?>" />
			        <input type="hidden" name="action" value="login" />
			        <button class="btn btn-green" type="submit" value=""><?php echo Armadillo_Language::msg('ARM_LOGIN_FORM_TAB_TITLE'); ?></button>
			    </form>
		    </div>
		</div>
		<p class="loginPageLink text-center"><a href="<?php echo $armadillo->request()->getRootUri(); ?>/../../../../../"><?php echo Armadillo_Language::msg('ARM_LOGIN_FORM_RETURN_TO_SITE_LINK'); ?></a><br/><a href="./forgot/"><?php echo Armadillo_Language::msg('ARM_LOGIN_FORM_FORGOT_PASSWORD_LINK'); ?></a></p>
	</div>
</div>