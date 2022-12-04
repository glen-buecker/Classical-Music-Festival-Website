<?php 

$armadillo = Slim::getInstance();
if ( $_SESSION['role'] === 'admin' ): 

?>

<!-- Users Admin Panel -->
<div class="usersAdminPanel">
    <h2 class="usersAdminTab">
    	<?php echo Armadillo_Language::msg('ARM_USER_TEXT_PLURAL'); ?>
    	<a class="btn btn-success btn-sm pull-right" href="./new/">
    		<i class='fa fa-plus'></i>
    		<span class='text'>
    			&nbsp;
    			<?php echo Armadillo_Language::msg('ARM_USER_CREATE_NEW_TEXT'); ?>
			</span>
		</a>
	</h2>
    <?php Armadillo_User::listUsers(); ?>
</div>

<?php 

else: $armadillo->redirect('./../'); endif;

?>