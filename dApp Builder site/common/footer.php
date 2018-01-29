<script type="text/javascript">
	var votingUndeployed = {};
	<?php if ($undeployed_dapps) { foreach ($undeployed_dapps as $dapp) {  ?>
		<?php if ($dapp->getDappType() == 'voting') { ?>
			votingUndeployed['<?php echo $dapp->getId(); ?>'] = {address: '<?php echo $dapp->getEthAccount(); ?>', name: '<?php echo $dapp->getName(); ?>'};
		<?php } ?>
	<?php } } ?>
</script>

<div class="admin-footer">
            <div class="text-center " style="font-size: 14px;">
                <p class="copyright-txt">
                    <a href="https://dapps.ibuildapp.com/privacy.html" >Privacy Policy</a> | <a href="https://dapps.ibuildapp.com/terms-of-use.html" >Terms of Use</a>
                </p>                    
            </div>
        </div>
    </div>

    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
    <!-- MODAL -->
    <div id="myModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header text-center">    
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h2>Change Password</h2>
                        <p>You can change your account password:</p>
                    </div>
                    <div class="modal-body">                    
                        <form id="change_pw" class="form-modal">
							<div class="message"></div>
                            <div class="form-group">
                                <input class="form-control" type="password" id="password" placeholder="Type your current password" name="password" required>
                            </div>
                            <div class="form-group">
                                <input class="form-control" type="password" id="new_password" placeholder="Type your new password" name="new_password" required>
                            </div>
                            <div class="form-group">
                                <input class="form-control" type="password" id="rep_password" placeholder="Repeat your new password" name="rep_password" required>
                            </div>
							<input type="submit" style="display:none;">
                        </form>
                        <div class="text-center">
                            <button class="btn-default" id="change_ok">Change_Password</button>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    <!-- END_MODAL -->
    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~--> 
	<div id="usernameModal" class="modal fade" role="dialog" aria-labelledby="usernameModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header text-center">    
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h2>Change Username</h2>
					<p>You can change your username:</p>
				</div>
				<div class="modal-body">                    
					<form id="change_name" class="form-modal" data-old="<?=$username?>">
						<div class="message"></div>
						<div class="form-group">
							<input class="form-control" type="text" id="new_name" placeholder="Type your new username" name="new_name" required>
						</div>
						<input type="submit" style="display:none;">
					</form>
					<div class="text-center">
						<button class="btn-default" id="username_ok">Change_Username</button>
					</div>
				</div>
			</div>
		</div>
    </div>
	
	
    <!--==========================================================================-->

    <script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="assets/js/jquery.cookie.js"></script>
	<script type="text/javascript" src="assets/js/main.js"></script>
	<script type="text/javascript" src="assets/js/constructor/voting.js"></script>

</body>
</html>