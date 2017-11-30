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

		<section class="cont-page"> 
			<div class="fullscreen parallax" style="background-image:url('../images/bg1.jpg');" data-img-width="1920" data-img-height="900 " data-diff="100">
				<div class="overlay">
					<div class="container">
					<div class="row">
					<div class="col-lg-8 col-lg-offset-2 col-sm-10 col-xs-12">
						<div class="title text-center">
							<h1 class=" ">Create <span class="decor-h">New dApp</span></h1>
						</div>
					</div>
					</div>	
					</div>
				</div>
			</div>
		</section>
		<!--________________________________________________-->

		<div class="conteiner">
			<div class="row">
				<div class="col-lg-8 col-lg-offset-2 col-sm-10 col-sm-offset-1 col-xs-12">
					<div class="form-hackathon">					
						<form class="row" id="creation-form" method="post">
							<div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-lg-10 col-lg-offset-1">
								<div class="input-group creation-form">
									<span class="input-group-addon" id="dapp-name-label">dApp Name:</span>
									<input placeholder="enter dApp Name.." id="dapp-name" type="text" class="form-control" aria-describedby="dapp-name-label" required>
								</div>
								<div class="input-group creation-form">
									<span class="input-group-addon" id="dapp-type-label">dApp Type:</span>
									<select class="form-control" aria-describedby="dapp-type-label" id="dapp-type-select" required>
										<option>Nothing selected</option>
										<option value="voting">Voting</option>
										<option value="escrow">Escrow</option>
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
									<h4 class="text-center">List of Candidates:</h4>
									<div id="voting-candidates">
										<div class="input-group creation-form">
											<span class="input-group-addon">Candidate:</span>
											<input placeholder="Candidate.." type="text" class="form-control required-voting required-dapp">
											<span class="input-group-btn">
												<button class="btn btn-danger btn-remove" type="button"><i class="fa fa-fw fa-times"></i></button>
											</span>
										</div>
										<div class="input-group creation-form">
											<span class="input-group-addon">Candidate:</span>
											<input placeholder="Candidate.." type="text" class="form-control required-voting required-dapp">
											<span class="input-group-btn">
												<button class="btn btn-danger btn-remove" type="button"><i class="fa fa-fw fa-times"></i></button>
											</span>
										</div>
									</div>
									<div class="text-center creation-form">
										<button type="button" id="voting-add-candidate" class="btn btn-primary">Add a Candidate</button>
										<button type="submit" class="btn btn-primary">Create Voting dApp</button>
										<button type="button" style="display:none;" id="create-voting-dapp"></button>
									</div>
								</div>
								
								<div id="escrow-dapp" class="dapp-type-block">
									<div class="input-group creation-form">
										<span class="input-group-addon">Price (ETH):</span>
										<input id="escrow-price" min="0" step="0.001" type="number" class="form-control required-escrow required-dapp">
									</div>
									<div class="input-group eth-address-group creation-form">
										<span class="input-group-addon">Seller's address:</span>
										<input id="escrow-seller" placeholder="0x0000000000000000000000000000000000000000" type="text" class="form-control required-escrow required-dapp">
										<span class="input-group-btn">
											<button class="btn btn-me" type="button">Me</button>
										</span>
									</div>
									<div class="input-group eth-address-group creation-form">
										<span class="input-group-addon">Buyer's address:</span>
										<input id="escrow-buyer" placeholder="0x0000000000000000000000000000000000000000" type="text" class="form-control required-escrow required-dapp">
										<span class="input-group-btn">
											<button class="btn btn-me" type="button">Me</button>
										</span>
									</div>
									<div class="input-group eth-address-group creation-form">
										<span class="input-group-addon">Agent's address:</span>
										<input id="escrow-oracle" placeholder="0x0000000000000000000000000000000000000000" type="text" class="form-control required-escrow required-dapp">
										<span class="input-group-btn">
											<button class="btn btn-me" type="button">Me</button>
										</span>
									</div>
									<div class="input-group creation-form">
										<span class="input-group-addon">Agent's fee (ETH):</span>
										<input id="escrow-fee" step="0.001" min="0" value="0" type="number" class="form-control required-escrow required-dapp">
									</div>
									<div id="escrow-timelimit-group" style="display:none;" class="input-group creation-form">
										<span class="input-group-addon">Time limit (blocks):</span>
										<input id="escrow-timelimit" min="0" type="number" value="0" class="form-control required-escrow required-dapp">
									</div>
									<div class="text-center creation-form">
										<button type="button" id="escrow-set-limit" data-setted="0" class="btn btn-primary">Set time limit</button>
										<button type="submit" class="btn btn-primary">Create Escrow dApp</button>
										<button type="button" style="display:none;" id="create-escrow-dapp"></button>
									</div>
								</div>
								
								
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
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
		$(".required-dapp").removeAttr("required");
		$(".dapp-type-block").fadeOut();
		if (type == 'voting') {
			$("#voting-dapp").fadeIn();
			$(".required-voting").attr("required", "");
		} else if (type == 'escrow') {
			$("#escrow-dapp").fadeIn();
			$(".required-escrow").attr("required", "");
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
	function addApp(type, name, key_account = false) {
		var data = 'type=' + type + '&name=' + encodeURIComponent(name) + '&eth_account=' + encodeURIComponent(web3.eth.defaultAccount);
		if (key_account) {
			data += '&key_eth_account=' + encodeURIComponent(key_account);
		}
		$.ajax({
			url: '/builder/create_dapp.php',
			type: 'POST',
			dataType: 'json',
			cache: false,
			data: data,
			error: function(jqXHR, error){},
			success: function(data, status){
				if (!data.error && data.success) {
					$("#successModal").modal({backdrop: 'static', keyboard: false});
					if (type == 'voting') {
						setInterval(function(){
							voteDapp.checkDeployed(web3.eth.defaultAccount, name, data.success, true)
						}, 3000);
					} else if (type == 'escrow') {
						setInterval(function(){
							escrowDapp.checkDeployed($("#escrow-seller").val(), name, data.success, true)
						}, 3000);
					}
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
	$(".btn-me").click(function(){
		$(this).parents(".eth-address-group").find("input[type='text']").val(web3.eth.defaultAccount);
	});
	$("#escrow-set-limit").click(function(){
		var setted = $(this).attr("data-setted");
		if (setted == "1") {
			$(this).attr("data-setted", "0");
			$(this).text("Set time limit");
			$("#escrow-timelimit-group").fadeOut(function(){
				$("#escrow-timelimit-group input[type='number']").val("0");
			});
		} else {
			$(this).attr("data-setted", "1");
			$(this).text("Unset time limit");
			$("#escrow-timelimit-group").fadeIn();
		}
	});
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
		} else if (type == 'escrow') {
			var seller_addr = $("#escrow-seller").val();
			var buyer_addr = $("#escrow-buyer").val();
			var oracle_addr = $("#escrow-oracle").val();
			var price = parseInt($("#escrow-price").val());
			var fee = parseInt($("#escrow-fee").val());
			if (!seller_addr.match(/^0x[0-9a-zA-Z]+$/)) {
				showError('Incorrect seller\'s address');
				return false;
			}
			if (!buyer_addr.match(/^0x[0-9a-zA-Z]+$/)) {
				showError('Incorrect buyer\'s address');
				return false;
			}
			if (!oracle_addr.match(/^0x[0-9a-zA-Z]+$/)) {
				showError('Incorrect agent\'s address');
				return false;
			}
			if (fee >= price) {
				showError('The price should be greater than agent\'s fee');
				return false;
			}
			
			escrowDapp.checkName(seller_addr, name, function(){
				$('#create-escrow-dapp').click();
			});
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
				<p>Please wait while deployment of your dApp in blockchain is completed. DON'T CLOSE this page, you will be taken to your dApps in a few minutes</p>
			</div>
			<div class="modal-body"></div>
		</div>
	</div>
</div>

<?php require_once('common/footer.php'); ?>