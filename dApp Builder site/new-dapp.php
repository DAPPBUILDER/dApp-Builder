<?php

require_once('classes/Helper.php');
session_start();

$currentUser = Helper::getCurrentUser();

if (!$currentUser) {
	header('Location: /builder/');
	exit;
}

//Profile data
$id = $currentUser->getId();
$username = $currentUser->getUsername();
$application = $currentUser->getApplication();
$api_id = $currentUser->getApiId();
$api_key = $currentUser->getApiKey();
$deployed_dapps = $currentUser->getDeployedDapps();
$undeployed_dapps = $currentUser->getUndeployedDapps();
$added_dapps = $currentUser->getAddedDapps();

require_once('common/header.php');
?>
<div id="page-wrapper">
	<div id="hackathon-container" class="container-fluid page-content">
		<h1 class="text-center">Create New dApp</h1>
		<form class="row" id="creation-form" method="post">
			<div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2 col-lg-4 col-lg-offset-4">
				<div class="input-group creation-form">
					<span class="input-group-addon" id="dapp-name-label">dApp Name:</span>
					<input id="dapp-name" type="text" class="form-control" aria-describedby="dapp-name-label" required>
				</div>
				<div class="input-group creation-form">
					<span class="input-group-addon" id="dapp-type-label">dApp Type:</span>
					<select class="form-control" aria-describedby="dapp-type-label" id="dapp-type-select" required>
						<option></option>
						<option value="voting">Voting</option>
					</select>
				</div>
				<div id="voting-dapp" class="dapp-type-block">
					<div class="input-group creation-form">
						<span class="input-group-addon" id="voting-blind-label">The Voting is Blind:</span>
						<select class="form-control" id="voting-blind" aria-describedby="voting-blind-label">
							<option value="0">No</option>
							<option value="1">Yes</option>
						</select>
					</div>
					<p class="text-center">List of Candidates:</p>
					<div id="voting-candidates">
						<div class="input-group creation-form">
							<span class="input-group-addon">Candidate:</span>
							<input type="text" class="form-control" required>
							<span class="input-group-btn">
								<button class="btn btn-danger btn-remove" type="button"><i class="fa fa-fw fa-trash"></i></button>
							</span>
						</div>
						<div class="input-group creation-form">
							<span class="input-group-addon">Candidate:</span>
							<input type="text" class="form-control" required>
							<span class="input-group-btn">
								<button class="btn btn-danger btn-remove" type="button"><i class="fa fa-fw fa-trash"></i></button>
							</span>
						</div>
					</div>
					<div class="text-center creation-form">
						<button type="button" id="voting-add-candidate" class="btn btn-primary">Add a Candidate</button>
					</div>
					<div class="text-center creation-form">
						<button type="submit" class="btn btn-primary">Create Voting dApp</button>
						<button type="button" style="display:none;" id="create-voting-dapp"></button>
					</div>
				</div>
				
				<div class="ballots"></div>
				
			</div>
		</form>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		checkDappType();
	});
	$(window).on('load', function(){
		checkConnection();
	});
	$("#dapp-type-select").change(function(){
		checkDappType();
	});
	function checkDappType() {
		var type = $('#dapp-type-select').val();
		$(".dapp-type-block").fadeOut();
		if (type == 'voting') {
			$("#voting-dapp").fadeIn();
		} else if (type == 'betting') {
			
		} else if (type == 'escrow') {
			
		}
	}
	function showError(error, is_static = false) {
		$("#error-text").html(error);
		if (is_static) {
			$("#error-close").hide();
			$("#errorModal").modal({backdrop: 'static', keyboard: false});
		} else {
			$("#error-close").show();
			$("#errorModal").modal("show");
		}
	}
	function addApp(type, name) {
		$.ajax({
			url: '/builder/create_dapp.php',
			type: 'POST',
			dataType: 'json',
			cache: false,
			data: 'type=' + type + '&name=' + encodeURIComponent(name) + '&eth_account=' + encodeURIComponent(web3.eth.defaultAccount),
			error: function(jqXHR, error){},
			success: function(data, status){
				if (!data.error && data.success) {
					$("#successModal").modal({backdrop: 'static', keyboard: false});
					setInterval(function(){
						voteDapp.checkDeployed(web3.eth.defaultAccount, name, data.success, true)
					}, 3000);
				} else {
					showError(data.error);
				}
			}
		});
	}
	function showNameError() {
		showError('This dApp name already exists, please choose another one');
	}
	function checkConnection() {
		if (typeof(web3) == 'undefined' || !web3.eth.defaultAccount) {
			showError('You do not have Web3 connection, please install <a href="https://metamask.io/" target="_blank">MetaMask</a>, unlock your account, choose the Rinkeby Test Network and update this page', true);
			return false;
		}
		if (web3.version.network != "4") {
			showError('The Builder works in Rinkeby Test Network, please choose it in your MetaMask plugin and update the page', true);
			return false;
		}
		return true;
	}
	$("#creation-form").submit(function(e){
		e.preventDefault();
		var inputs_valid = true;
		$(this).find('input[type="text"]').each(function(){
			var text = $(this).val();
			if (text.match(/^[A-Za-z0-9\-_\s]*$/) == null) inputs_valid = false;
		});
		if (!inputs_valid) {
			showError("Please use only english letters, numbers, symbols '-' and '_' and whitespace symbol in text inputs");
			return false;
		}
		var type = $('#dapp-type-select').val();
		var name = $('#dapp-name').val();
		if (!name) {
			showError('Please enter the dApp name');
			return false;
		}
		if (!checkConnection()) return false;
		if (type == 'voting') {
			var candidates = 0;
			$('#voting-candidates input').each(function(){
				candidates++;
			});
			if (candidates < 2) {
				showError('Please add at least 2 candidates');
				return false;
			}
			voteDapp.checkName(web3.eth.defaultAccount, name, function(){
				$('#create-voting-dapp').click();
			});
		} else if (type == 'betting') {
			
		} else if (type == 'escrow') {
			
		}
		return false;
	});
</script>

<div id="errorModal" class="modal fade" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header text-center">    
				<button id="error-close" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h2>Error</h2>
				<p id="error-text"></p>
			</div>
			<div class="modal-body"></div>
		</div>
	</div>
</div>

<div id="successModal" class="modal fade" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header text-center">    
				<h2>Pending Transaction</h2>
				<p>Please wait for the deploying your dApp into the blockchain. Don't close this page, you will be automatically redirected to the list of your dApps in a few minutes</p>
			</div>
			<div class="modal-body"></div>
		</div>
	</div>
</div>

<?php require_once('common/footer.php'); ?>