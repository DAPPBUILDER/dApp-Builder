<?php

require_once('classes/Helper.php');
session_start();

$currentUser = Helper::getCurrentUser();

if (!$currentUser) {
	header('Location: /login.php?redirect=builder');
	exit;
}

require_once 'common/lang_setter.php';

//Profile data
require_once 'common/profiledata.php';

require_once('common/header.php');
?>
<!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
<div id="page-wrapper">
	<div id="hackathon-container" class="container-fluid page-content">
		
		<section class="cont-page"> 
			<div class="container-fluid">
				<div class="row">
					<div class="col-lg-12">
						<div class="title">
							<h1>dApp Builder <i class="fa fa-chevron-right" aria-hidden="true" style="font-size: 15px;"></i> <?php echo $_newdapp['title_New_dApp']; ?></h1>
						</div>
					</div>
				</div>
			</div>
		</section>
		<!--________________________________________________-->

		<div class="conteiner">
			<div class="row">
				<div id="manage-panel" class="col-xs-12">
					<!-- <div class="title">
						<h1 class="text-center ">Create New dApp</h1>
					</div> -->
					
					<div class="form-hackathon">					
						<form class="row" id="creation-form" method="post">
							<div class="col-md-12">
                                
								<!-- Select SmartContract -->
								<!-- ______________________________________________________________________ -->		
                                <div class="select-contract text-center">
	                                
	                            <ul class="nav nav-tabs text-center dapps-plate">    
	                                <h2 class="title-step"><?php echo $_newdapp['step_one_title']; ?></h2> 
	                                    <li class="dapp-tab" data-value="voting">
	                                    	<div class="contract_block">
		                                        <a class="" data-toggle="tab" href="#" title="Voting">
		                                        <div class="contract_img"><img src="assets/images/voting_screen.png?05072018" alt="Voting" /></div>
		                                        <div class="contract_content">
		                                        	<h3 class="text-center" ><?php echo $_newdapp['voting']; ?></h3>
		                                        	<p class="description"><?php echo $_newdapp['voting_text']; ?></p>
		                                        	<button class="btn btn-create create"><?php echo $_newdapp['create_dApp']; ?></button>
		                                        </div>
		                                        </a>
	                                        </div>
	                                    </li>
	                                    <li class="dapp-tab" data-value="escrow">
	                                    	<div class="contract_block">
		                                        <a data-toggle="tab" href="#" title="Escrow">
		                                        <div class="contract_img"><img src="assets/images/Escrow_screen.png?05072018" alt="Escrow" /></div>
		                                        <div class="contract_content">
		                                        	<h3 class="text-center" ><?php echo $_newdapp['escrow']; ?></h3>
		                                        	<p class="description"><?php echo $_newdapp['escrow_text']; ?></p>
		                                        	<button class="btn btn-create create"><?php echo $_newdapp['create_dApp']; ?></button>
		                                        </div>
		                                        </a>
	                                        </div>
	                                    </li>
	                                    <li class="dapp-tab text-center" data-value="multisig">
	                                    	<div class="contract_block">
		                                    	<a data-toggle="tab" href="#" title="Multisignature Wallet">
		                                        <div class="contract_img"><img src="assets/images/wallet_screen.png?05072018" alt="Multisignature Wallet" /></div>
		                                    	<div class="contract_content">
		                                    		<h3 class="text-center"><?php echo $_newdapp['mult_wallet']; ?></h3>
		                                    		<p class="description"><?php echo $_newdapp['mult_wallet_text']; ?></p>
		                                    		<button class="btn btn-create create"><?php echo $_newdapp['create_dApp']; ?></button>
		                                    	</div>
		                                    	</a>
		                                    </div>   
	                                    </li>
	                                    <li class="dapp-tab" data-value="betting">
	                                    	<div class="contract_block">
		                                        <a data-toggle="tab" href="#" title="Betting">
		                                        <div class="contract_img"><img src="assets/images/betting_screen.png?05072018" alt="Betting dApp" /></div>
		                                        <div class="contract_content">
		                                        	<h3 class="text-center"><?php echo $_newdapp['betting']; ?></h3>
		                                        	<p class="description"><?php echo $_newdapp['betting_text']; ?></p>
		                                        	<button class="btn btn-create create"><?php echo $_newdapp['create_dApp']; ?></button>
		                                        </div>
		                                        </a>
	                                        </div>
	                                    </li>
                                        <li class="dapp-tab" data-value="custom-token">
                                            <div class="contract_block">
                                                <a data-toggle="tab" href="#" title="Custom token">
                                                    <div class="contract_img"><img src="assets/images/custom_token_screen.png" alt="Custom Token" /></div>
                                                    <div class="contract_content">
                                                        <h3 class="text-center">Custom token</h3>
                                                        <p class="description">Customizable ERC20 token.</p>
                                                        <button class="btn btn-create create"><?php echo $_newdapp['create_dApp']; ?></button>
                                                    </div>
                                                </a>
                                            </div>
                                        </li>
									</ul>
								</div>
								<!-- ______________________________________________________________________ -->
                                                            
                                <div class="back-block">
                                	<a href="#" type="button" id="back-button" class="btn-back"><i class="fa fa-reply" aria-hidden="true"></i> <?php echo $_newdapp['back']; ?></a>
                                </div>

                                <div class="dapp-name-block">
                                    <h2 id="dapp-type-h" class="text-center dapp-type-h-style"></h2>
                                    <h2 class="title-step text-center"><?php echo $_newdapp['step_two_title']; ?></h2>
                                    <div class="input-group creation-form">
                                        <span class="input-group-addon" id="dapp-name-label"><span class="req-color">*</span> <?php echo $_newdapp['dApp_Name']; ?></span>
                                        <input placeholder="<?php echo $_newdapp['enter_dApp_Name']; ?>" id="dapp-name" type="text" class="form-control" aria-describedby="dapp-name-label" required>
                                    </div>
                                </div>
								<div class="select-value input-group creation-form" style="">
									<span class="input-group-addon" id="dapp-type-label"><?php echo $_newdapp['dApp_Type']; ?></span>
									<select class="form-control" aria-describedby="dapp-type-label" id="dapp-type-select" required>
										<option value="voting"><?php echo $_newdapp['voting']; ?></option>
										<option value="escrow"><?php echo $_newdapp['escrow']; ?></option>
										<option value="multisig"><?php echo $_newdapp['mult_wallet']; ?></option>
										<option value="betting"><?php echo $_newdapp['betting']; ?></option>
                                        <option value="custom-token">Custom token</option>
									</select>
								</div>
								<!-- ______________________________________________________________________ -->
								
								<!-- Voting -->
								<!-- ______________________________________________________________________ -->
								<div id="voting-dapp" class="dapp-type-block">
									<h2 class="title-step text-center"><?php echo $_newdapp['step_three_title']; ?></h2>
									<div class="input-group creation-form">
										<span class="input-group-addon" id="voting-blind-label"><?php echo $_newdapp['voting_Blind']; ?></span>
										<select class="form-control" id="voting-blind" aria-describedby="voting-blind-label">
											<option value="0"><?php echo $_newdapp['no']; ?></option>
											<option value="1"><?php echo $_newdapp['yes']; ?></option>
										</select>
									</div>
									<h4 class="text-center"><?php echo $_newdapp['list_Cand']; ?></h4>
									<div id="voting-candidates">
										<div class="input-group creation-form">
											<span class="input-group-addon"><span class="req-color">*</span> <?php echo $_newdapp['candidate']; ?></span>
											<input placeholder="<?php echo $_newdapp['enter_cand_name']; ?>" type="text" class="form-control required-voting required-dapp">
											<span class="input-group-btn">
												<button class="btn btn-danger btn-remove" type="button"><i class="fa fa-fw fa-times"></i></button>
											</span>
										</div>
										<div class="input-group creation-form">
											<span class="input-group-addon"><span class="req-color">*</span> <?php echo $_newdapp['candidate']; ?></span>
											<input placeholder="<?php echo $_newdapp['enter_cand_name']; ?>" type="text" class="form-control required-voting required-dapp">
											<span class="input-group-btn">
												<button class="btn btn-danger btn-remove" type="button"><i class="fa fa-fw fa-times"></i></button>
											</span>
										</div>
									</div>
									<div class="creation-form text-right">
										<button type="button" id="voting-add-candidate" class="btn btn-add"><?php echo $_newdapp['add_Cand']; ?></button>
									</div>
									<div class="text-center creation-form">
										<button type="submit" class="btn btn-primary"><?php echo $_newdapp['create_voting_dapp']; ?></button>
										<button type="button" style="display:none;" id="create-voting-dapp"></button>
									</div>
								</div>
								<!-- ______________________________________________________________________ -->
								
								<!-- Escrow -->
								<!-- ______________________________________________________________________ -->
								<div id="escrow-dapp" class="dapp-type-block">
									<h2 class="title-step text-center"><?php echo $_newdapp['step_three_title']; ?></h2>
									<div class="input-group creation-form">
										<span class="input-group-addon"><span class="req-color">*</span> <?php echo $_newdapp['price_eth']; ?></span>
										<input id="escrow-price" min="0" step="0.001" type="number" placeholder="<?php echo $_newdapp['enter_amount_eth']; ?>" class="form-control required-escrow required-dapp">
									</div>
									<div class="input-group eth-address-group creation-form">
										<span class="input-group-addon"><span class="req-color">*</span> <?php echo $_newdapp['seller_eth']; ?></span>
										<input id="escrow-seller" placeholder="0x0000000000000000000000000000000000000000" type="text" class="form-control required-escrow required-dapp">
										<span class="input-group-btn">
											<button class="btn btn-me" type="button"><?php echo $_newdapp['current_account']; ?></button>
										</span>
									</div>
									<div class="input-group eth-address-group creation-form">
										<span class="input-group-addon"><span class="req-color">*</span> <?php echo $_newdapp['buyer_eth_add']; ?></span>
										<input id="escrow-buyer" placeholder="0x0000000000000000000000000000000000000000" type="text" class="form-control required-escrow required-dapp">
										<span class="input-group-btn">
											<button class="btn btn-me" type="button"><?php echo $_newdapp['current_account']; ?></button>
										</span>
									</div>
									<div class="input-group eth-address-group creation-form">
										<span class="input-group-addon"><span class="req-color">*</span> <?php echo $_newdapp['arbitrator_eth_add']; ?></span>
										<input id="escrow-oracle" placeholder="0x0000000000000000000000000000000000000000" type="text" class="form-control required-escrow required-dapp">
										<span class="input-group-btn">
											<button class="btn btn-me" type="button"><?php echo $_newdapp['current_account']; ?></button>
										</span>
									</div>
									<div class="input-group creation-form">
										<span class="input-group-addon"><span class="req-color">*</span> <?php echo $_newdapp['arbitrator_fee_eth']; ?></span>
										<input id="escrow-fee" step="0.001" min="0" value="0" type="number" class="form-control required-escrow required-dapp">
									</div>
									<div id="escrow-timelimit-group" style="display:none;" class="input-group creation-form">
										<span class="input-group-addon"><span class="req-color">*</span> <?php echo $_newdapp['time_limit']; ?></span>
										<input id="escrow-timelimit" min="0" type="number" value="0" class="form-control required-escrow required-dapp">
									</div>
									<div class="text-right creation-form">
										<button type="button" id="escrow-set-limit" data-setted="0" class="btn btn-add"><?php echo $_newdapp['set_time_limit']; ?></button>
									</div>
									<div class="text-center creation-form">
										<button type="submit" class="btn btn-primary"><?php echo $_newdapp['create_ escro_dApp']; ?></button>
										<button type="button" style="display:none;" id="create-escrow-dapp"></button>
									</div>
								</div>
								<!-- ______________________________________________________________________ -->
                                                            
								<!-- Multisig -->
								<!-- ______________________________________________________________________ -->
								<div id="multisig-dapp" class="dapp-type-block">
									<h2 class="title-step text-center"><?php echo $_newdapp['step_three_title']; ?></h2>
									<div class="input-group creation-form">
										<span class="input-group-addon"><span class="req-color">*</span> <?php echo $_newdapp['initial_balance']; ?></span>
										<input id="multisig-balance" min="0" step="0.001" value="0" type="number" class="form-control required-multisig required-dapp">
									</div>
                                                                    
									<div class="input-group creation-form">
										<span class="input-group-addon"><span class="req-color">*</span> <?php echo $_newdapp['approvals_number']; ?></span>
										<input id="multisig-approvals" min="1" step="1" value="2" type="number" class="form-control required-multisig required-dapp">
									</div>
                                                                    
									<h4 class="text-center"><?php echo $_newdapp['wallet_owners']; ?></h4>
                                                                        <p class='text-center grey-comment'><?php echo $_newdapp['wallet_owners_text_one']; ?> <span id="wallets-owners">2</span> <?php echo $_newdapp['owners']; ?></p>
                                                                        <p class='text-center grey-comment'><?php echo $_newdapp['wallet_owners_text_two']; ?> <a id="wallets-confirmations">2</a> <?php echo $_newdapp['owners']; ?></p>
                                                                        <p class='text-center grey-comment'><?php echo $_newdapp['wallet_owners_text_three']; ?></p>
									<div id="multisig-owners">
										<div class="input-group creation-form">
											<!--<span class="input-group-addon"><span class="req-color">*</ Owner's Address:</span>-->
											<input id="multisig-first-owner" placeholder="0x0000000000000000000000000000000000000000" type="text" class="form-control required-multisig required-dapp">
											<span class="input-group-btn">
												<button class="btn btn-danger btn-remove" type="button"><i class="fa fa-fw fa-times"></i></button>
											</span>
										</div>
									    <div class="input-group creation-form">
											<input placeholder="0x0000000000000000000000000000000000000000" type="text" class="form-control required-multisig required-dapp">
											<span class="input-group-btn">
											<button class="btn btn-danger btn-remove" type="button"><i class="fa fa-fw fa-times"></i></button>
											</span>
										</div>
									</div>
									<div class=" text-right creation-form">
										<button type="button" id="multisig-add-owner" class="btn btn-add"><?php echo $_newdapp['add_an_owner']; ?></button>
									</div>
									<div class="text-center creation-form">
										<button type="submit" class="btn btn-primary"><?php echo $_newdapp['create_mult_wallet']; ?></button>
										<button type="button" style="display:none;" id="create-multisig-dapp"></button>
									</div>
								</div>
								<!-- ______________________________________________________________________ -->

								<!-- Betting -->
								<!-- ______________________________________________________________________ -->
								<div id="betting-dapp" class="dapp-type-block">
									<h2 class="title-step text-center"><?php echo $_newdapp['step_three_title']; ?></h2>
									<div class="input-group creation-form eth-address-group">
										<span class="input-group-addon"><span class="req-color">*</span> <?php echo $_newdapp['arbitrator_eth_add']; ?></span>
										<input id="betting-arbitrator" type="text" placeholder="0x0000000000000000000000000000000000000000" class="form-control required-dapp required-betting">
										<span class="input-group-btn">
											<button class="btn btn-me" type="button"><?php echo $_newdapp['current_account']; ?></button>
										</span>
									</div>
									<div class="input-group creation-form">
										<span class="input-group-addon"><span class="req-color">*</span> <?php echo $_newdapp['arbitrator_fee_perc']; ?></span>
										<input id="betting-fee" type="number" value="0" min="0" max="99" class="form-control required-dapp">
									</div>
									<h4 class="text-center"><?php echo $_newdapp['list_bids']; ?></h4>
									<div id="betting-bids">
										<div class="input-group creation-form">
											<span class="input-group-addon"><span class="req-color">*</span> <?php echo $_newdapp['bid_name']; ?></span>
											<input placeholder="<?php echo $_newdapp['enter_bid_name']; ?>" type="text" class="form-control required-betting required-dapp">
												<span class="input-group-btn">
													<button class="btn btn-danger btn-remove" type="button"><i class="fa fa-fw fa-times"></i></button>
												</span>
										</div>
										<div class="input-group creation-form">
											<span class="input-group-addon"><span class="req-color">*</span> <?php echo $_newdapp['bid_name']; ?></span>
											<input placeholder="<?php echo $_newdapp['enter_bid_name']; ?>" type="text" class="form-control required-betting required-dapp">
												<span class="input-group-btn">
													<button class="btn btn-danger btn-remove" type="button"><i class="fa fa-fw fa-times"></i></button>
												</span>
										</div>
									</div>
									<div class="text-right creation-form">
										<button type="button" id="betting-add-bid" class="btn btn-add"><?php echo $_newdapp['add_bid']; ?></button>
									</div>
									<div class="text-center creation-form">
										<button type="submit" class="btn btn-primary"><?php echo $_newdapp['create_betting_dApp']; ?></button>
										<button type="button" style="display:none;" id="create-betting-dapp"></button>
									</div>
								</div>
								<!-- ______________________________________________________________________ -->
                                <!-- Custom token -->
                                <!-- ______________________________________________________________________ -->
                                <div id="custom-token-dapp" class="dapp-type-block">
                                    <h2 class="title-step text-center">Step 3: Fill the Form</h2>
                                    <div class="input-group creation-form">
                                        <span class="input-group-addon">* Token symbol:</span>
                                        <input id="custom-token-symbol" type="text" class="form-control required-dapp required-custom-token">
                                    </div>
                                    <div class="input-group creation-form">
                                        <span class="input-group-addon">* Token decimals:</span>
                                        <input id="custom-token-decimals" type="number" value="18" min="0" max="18" class="form-control required-custom-token">
                                    </div>
                                    <div class="input-group creation-form">
                                        <span class="input-group-addon">* Token total supply:</span>
                                        <input id="custom-token-supply" type="number" value="0" min="0" class="form-control required-custom-token">
                                    </div>
                                    <div class="input-group creation-form eth-address-group">
                                        <span class="input-group-addon">* Beneficiary address:</span>
                                        <input id="custom-token-benefeciary" type="text" placeholder="0x0000000000000000000000000000000000000000" class="form-control required-dapp required-custom-token">
                                        <span class="input-group-btn">
											<button class="btn btn-me" type="button">Current Account</button>
										</span>
                                    </div>
                                    <div class="text-center creation-form">
                                        <button type="submit" class="btn btn-primary">Create Custom Token</button>
                                        <button type="button" style="display:none;" id="create-custom-token-dapp"></button>
                                    </div>
                                </div>
                                <div class="text-center back-block">
                                	<a href="#" type="button" id="back-button" class="btn-back"><i class="fa fa-arrow-left " aria-hidden="true"></i> <?php echo $_newdapp['back']; ?></a>
                                </div>
							</div>
						</form>
					</div>
				</div>
                <div id="simulator" style="display:none;" class="col-xs-12 col-lg-4 col-md-5">
                    <div class="form-hackathon">
                        <iframe id="simulator-iframe" style="width:100%;height:600px;"></iframe>
                    </div>
                </div>
			</div>
		</div>
	</div>
</div>
<!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->

<script type="text/javascript">
	$(document).ready(function(){
		var dapp_template = false;
		if (typeof localStorage.dapp_template != 'undefined' && localStorage.dapp_template) {
			dapp_template = JSON.parse(localStorage.dapp_template);
		}
		if (dapp_template && typeof dapp_template.type != 'undefined') {
			if (typeof dapp_template.name != 'undefined' && dapp_template.name) {
				$('#dapp-name').val(dapp_template.name);
			}
			$('.dapp-tab[data-value="' + dapp_template.type + '"]').click();
			if (dapp_template.type == 'voting') {
				if (typeof dapp_template.blind != 'undefined') {
					$('#voting-blind option[value="' + dapp_template.blind + '"]').prop('selected', true);
				}
				/*for (var i = 2, len = dapp_template.candidates.length; i < len; i++) {
					$("#voting-add-candidate").click();
				}*/
				for (var i = 0, len = dapp_template.candidates.length; i < len; i++) {
					if (i < 2) {
						var flag = true;
						$('#voting-candidates').find('input').each(function(){
							if ($(this).val() == '' && flag) {
								$(this).val(dapp_template.candidates[i]);
								flag = false;
							}
						});
					} else {
						$('#voting-candidates').append(
							'<div style="display:none;" class="input-group creation-form">' +
								'<span class="input-group-addon"><span class="req-color">*</span> <?php echo $_newdapp['candidate']; ?></span>' +
								'<input placeholder="<?php echo $_newdapp['enter_cand_name']; ?>" value="' + dapp_template.candidates[i] + '" type="text" class="form-control required-voting required-dapp" required>' +
								'<span class="input-group-btn">' +
									'<button class="btn btn-danger btn-remove" type="button"><i class="fa fa-fw fa-times"></i></button>' +
								'</span>' +
							'</div>'
						);
					}
				}
				$('#voting-candidates>div').fadeIn();
			} else if (dapp_template.type == 'escrow') {
				if (typeof dapp_template.price != 'undefined') {
					$('#escrow-price').val(dapp_template.price);
				}
				if (typeof dapp_template.seller != 'undefined') {
					$('#escrow-seller').val(dapp_template.seller);
				}
				if (typeof dapp_template.buyer != 'undefined') {
					$('#escrow-buyer').val(dapp_template.buyer);
				}
				if (typeof dapp_template.oracle != 'undefined') {
					$('#escrow-oracle').val(dapp_template.oracle);
				}
				if (typeof dapp_template.fee != 'undefined') {
					$('#escrow-fee').val(dapp_template.fee);
				}
				if (typeof dapp_template.timelimit != 'undefined' && dapp_template.timelimit != '0') {
					$('#escrow-timelimit').val(dapp_template.timelimit);
					$("#escrow-set-limit").click();
				}
                        } else if (dapp_template.type == 'multisig') {
                                if (typeof dapp_template.balance != 'undefined') {
					$('#multisig-balance').val(dapp_template.balance);
				}
                                if (typeof dapp_template.approvals != 'undefined') {
					$('#multisig-approvals').val(dapp_template.approvals);
				}
                                for (var i = 0, len = dapp_template.owners.length; i < len; i++) {
					if (i < 2) {
						var flag = true;
						$('#multisig-owners').find('input').each(function(){
							if ($(this).val() == '' && flag) {
								$(this).val(dapp_template.owners[i]);
								flag = false;
							}
						});
					} else {
						$('#multisig-owners').append(
                                                        '<div style="display:none;" class="input-group creation-form">' +
                                                            '<input required placeholder="0x0000000000000000000000000000000000000000" value="' + dapp_template.owners[i] + '" type="text" class="form-control required-multisig required-dapp">' +
                                                            '<span class="input-group-btn">' +
                                                                '<button class="btn btn-danger btn-remove" type="button"><i class="fa fa-fw fa-times"></i></button>' +
                                                            '</span>' +
                                                        '</div>'
						);
					}
				}
				$('#multisig-owners>div').fadeIn();
                                countWalletOwners();
                                countWalletApprovals();
                        } else if (dapp_template.type == 'betting') {
                                if (typeof dapp_template.arbitrator != 'undefined') {
					$('#betting-arbitrator').val(dapp_template.arbitrator);
				}
                                if (typeof dapp_template.fee != 'undefined') {
					$('#betting-fee').val(dapp_template.fee);
				}
                                for (var i = 0, len = dapp_template.bids.length; i < len; i++) {
					if (i < 2) {
						var flag = true;
						$('#betting-bids').find('input').each(function(){
							if ($(this).val() == '' && flag) {
								$(this).val(dapp_template.bids[i]);
								flag = false;
							}
						});
					} else {
						$('#betting-bids').append(
							'<div style="display:none;" class="input-group creation-form">' +
								'<span class="input-group-addon"><?php echo $_newdapp['bid_name']; ?></span>' +
								'<input placeholder="<?php echo $_newdapp['enter_bid_name']; ?>" value="' + dapp_template.bids[i] + '" type="text" class="form-control required-betting required-dapp" required>' +
								'<span class="input-group-btn">' +
									'<button class="btn btn-danger btn-remove" type="button"><i class="fa fa-fw fa-times"></i></button>' +
								'</span>' +
							'</div>'
						);
					}
				}
				$('#betting-bids>div').fadeIn();
			}
			else if ( dapp_template.type == 'custom-token'){
                if (typeof dapp_template.symbol != 'undefined') {
                    $('#custom-token-symbol').val(dapp_template.symbol);
                }
                if (typeof dapp_template.decimals != 'undefined'){
                    $('#custome-token-decimals').val(dapp_template.decimals);
                }
                if (typeof dapp_template.totalSupply != 'undefined'){
                    $('#custom-token-supply').val(dapp_template.totalSupply);
                }
                if (typeof dapp_template.benefeciary != 'undefined'){
                    $('#custom-token-benefeciary').val(dapp_template.benefeciary);
                }
            }
		} else {
			//checkDappType();
		}
                simulator();
                
                $("#main-deploy-btn").click(function(){
                    console.log("rr");
                    rinkeby_alert_shown = true;
                    $("#rinkebyModal .close").click();
                    $("#creation-form").submit();
                });
	});
	$(window).on('load', function(){
		//checkConnection();
	});
	$("#dapp-type-select").change(function(){
		checkDappType();
	});
	$(".dapp-tab").click(function(){
		//$(".dapp-tab").removeClass("active");
		//$(this).addClass("active");
		var value = $(this).attr("data-value");
		$('#dapp-type-select option[value="' + value + '"]').prop('selected', true);
		$('#dapp-type-select').change();
	});
        $("#back-button").click(function(){
            $("#back-button").hide();
            $(".dapps-plate").show();
            $(".dapp-type-block").hide();
            $(".dapp-name-block").hide();
            $("#manage-panel").removeClass("col-lg-8 col-md-7");
            $("#simulator").hide();
        });
	function checkDappType() {
		var type = $('#dapp-type-select').val();
		$(".required-dapp").removeAttr("required");
		$(".dapp-type-block").hide();
                $(".dapp-name-block").show();
                $(".dapps-plate").hide();
                $("#back-button").show();
                //$("#manage-panel").addClass("col-lg-8 col-md-7");
                //$("#simulator").show();
		if (type == 'voting') {
                        $("#manage-panel").addClass("col-lg-8 col-md-7");
                        $("#simulator").show();
                        $("#dapp-type-h").text("<?php echo $_newdapp['voting_dApp']; ?>");
			$("#voting-dapp").show();
			$(".required-voting").attr("required", "");
		} else if (type == 'escrow') {
                        $("#dapp-type-h").text("<?php echo $_newdapp['escrow_dApp']; ?>");
			$("#escrow-dapp").show();
			$(".required-escrow").attr("required", "");
		} else if (type == 'multisig') {
                        $("#dapp-type-h").text("<?php echo $_newdapp['mult_wallet']; ?>");
			$("#multisig-dapp").show();
			$(".required-multisig").attr("required", "");
		} else if (type == 'betting'){
                        $("#dapp-type-h").text("<?php echo $_newdapp['betting_dApp']; ?>");
			$('#betting-dapp').show();
			$(".required-betting").attr("required", "");
		} else if (type == 'custom-token'){
            $("#dapp-type-h").text("<?php echo $_newdapp['custom_token_dApp']; ?>");
		    $('#custom-token-dapp').show();
            $(".required-custom-token").attr("required", "");
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
	function addApp(type, name, network, key_account = false, address = null) {
		console.log('Given type to function addApp is:'+type+', and network is: '+network);
		var data = 'type=' + type + '&name=' + encodeURIComponent(name) + '&eth_account=' + encodeURIComponent(web3.eth.defaultAccount) + '&network=' + network + '&address='+address;
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
			success: function(data, status) {
				if (!data.error && data.success) {
					$("#successModal").modal({backdrop: 'static', keyboard: false});
					if (type == 'voting') {
						setInterval(function () {
							voteDapp.checkDeployed(web3.eth.defaultAccount, name, data.success, true)
						}, 3000);
					} else if (type == 'escrow') {
						setInterval(function () {
							escrowDapp.checkDeployed($("#escrow-seller").val(), name, data.success, true)
						}, 3000);
					} else if (type == 'multisig') {
						setInterval(function () {
							multisigDapp.checkDeployed(web3.eth.defaultAccount, name, data.success, true)
						}, 3000);
					} else if (type == 'betting') {
                        setInterval(function () {
                            bettingDapp.checkDeployed(web3.eth.defaultAccount, name, data.success, true)
                        }, 3000);
                    } else if (type == 'custom-token'){
                        setInterval(function () {
                            tokenDapp.checkDeployed(window.tokenUndeployed.txnHash, data.success, true)
                        }, 3000);
					} else {
						showError(data.error);
					}
				}
			}
		});
	}
	function showNameError() {
		showError('This dApp name already exists, please choose another one');
	}
	function checkConnection() {
		if (typeof(web3) == 'undefined' || !web3.eth.defaultAccount) {
			showError('<?php echo $_newdapp['web_connection']; ?> <a href="https://metamask.io/" target="_blank">MetaMask</a>, <?php echo $_newdapp['unlock_your_account']; ?><br><button type="button" class="btn btn-primary" onclick="location.reload();">Refresh the Page</button>', true);
			return false;
		}
		if (web3.version.network != "1" && web3.version.network != "4") {
			showError('<?php echo $_index_dictionary['works_Main_Eth_Net']; ?><br><button type="button" class="btn btn-primary" onclick="location.reload();">Refresh the Page</button>', true);
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

	//* --------- FORM SUBMIT ---------- *//
	$("#creation-form").submit(function(e){
		e.preventDefault();
		localStorage.removeItem('dapp_template');
		var inputs_valid = true;
		$(this).find('input[type="text"]').each(function(){
			var text = $(this).val();
			if (text.match(/^[A-Za-z0-9\-_\s]*$/) == null) inputs_valid = false;
		});
		if (!inputs_valid) {
			showError("<?php echo $_newdapp['use_only_english']; ?> '-' <?php echo $_newdapp['and']; ?> '_' <?php echo $_newdapp['whitespace_symbol']; ?>");
			return false;
		}
		var type = $('#dapp-type-select').val();
		var name = $('#dapp-name').val();
		if (!name) {
			showError('Please enter the dApp name');
			return false;
		}

		//* ----------------- CHECK CONNECTION ---------------- *//
		if (!checkConnection()) {
			var dapp_template = getDappInfo();
			localStorage.setItem('dapp_template', JSON.stringify(dapp_template));
			return false;
		}
                if (web3.version.network == "1" && !rinkeby_alert_shown) {
                        $("#rinkebyModal").modal("show");
                        var dapp_template = getDappInfo();
			localStorage.setItem('dapp_template', JSON.stringify(dapp_template));
			return false;
                }
                rinkeby_alert_shown = false;
                localStorage.removeItem('dapp_template');
		if (type == 'voting') {
			var candidates = 0;
			$('#voting-candidates input').each(function(){
				candidates++;
			});
			if (candidates < 2) {
				showError('<?php echo $_newdapp['add_least_cand']; ?>');
				return false;
			}
			voteDapp.checkName(web3.eth.defaultAccount, name, function(){
				$('#create-voting-dapp').click();
			});
		}
		else if (type == 'escrow') {
			var seller_addr = $("#escrow-seller").val();
			var buyer_addr = $("#escrow-buyer").val();
			var oracle_addr = $("#escrow-oracle").val();
			var price = parseInt($("#escrow-price").val());
			var fee = parseInt($("#escrow-fee").val());
			if (!seller_addr.match(/^0x[0-9a-fA-F]+$/)) {
				showError('<?php echo $_newdapp['incorrect_seller']; ?>');
				return false;
			}
			if (!buyer_addr.match(/^0x[0-9a-fA-F]+$/)) {
				showError('<?php echo $_newdapp['incorrect_buyer']; ?>');
				return false;
			}
			if (!oracle_addr.match(/^0x[0-9a-fA-F]+$/)) {
				showError('<?php echo $_newdapp['incorrect_arbitrator']; ?>');
				return false;
			}
			if (fee >= price) {
				showError('<?php echo $_newdapp['should_greater']; ?>');
				return false;
			}
			escrowDapp.checkName(seller_addr, name, function(){
				$('#create-escrow-dapp').click();
			});
		} else if (type == 'multisig') {
                        var approvals = parseInt($("#multisig-approvals").val());
                        var owners = 0;
                        var owners_arr = [];
                        var incorrect_address = false;
                        $('#multisig-owners input').each(function(){
                            var owner = $(this).val();
                            if (!owner.match(/^0x[0-9a-fA-F]+$/)) incorrect_address = true;
                            owners_arr.push(owner.toLowerCase());
                            owners++;
			});
                        if (incorrect_address) {
                            showError('<?php echo $_newdapp['more_owners_incorrect']; ?>');
                            return false;
                        }
                        for (var i = 0; i < owners_arr.length; i++) {
                            for (var j = 0; j < owners_arr.length; j++) {
                                if (owners_arr[i] == owners_arr[j] && i != j) {
                                    showError('<?php echo $_newdapp['owner_add_different']; ?>');
                                    return false;
                                }
                            }
                        }
                        if (approvals < 1) {
                            showError('<?php echo $_newdapp['multisig_approval']; ?>');
                            return false;
                        }
                        if (approvals > owners) {
                            showError('<?php echo $_newdapp['must_greater_equal']; ?>');
                            for (i = owners; i < approvals; i++) {
                                $('#multisig-add-owner').click();
                            }
                            return false;
                        }
                        multisigDapp.checkName(web3.eth.defaultAccount, name, function(){
                            $('#create-multisig-dapp').click();
			});
                } else if (type == 'betting'){
					var bidsNum = $('#betting-bids input').length;
					if (bidsNum < 2){
						showError('<?php echo $_newdapp['number_bids_should']; ?>');
						return false;
					}
					var Arbitratorfee = $('#betting-fee').val();
					if (Arbitratorfee >= 100 || Arbitratorfee < 0){
						showError('<?php echo $_newdapp['fee_should_between']; ?>');
						return false;
					}
					var abritratorAddress = $('#betting-arbitrator').val();
					if (!web3.isAddress(abritratorAddress)){
						showError('<?php echo $_newdapp['enter_valid_arbitrator']; ?>');
						return false;
					}
					bettingDapp.checkName(web3.eth.defaultAccount, name, function(){
						$('#create-betting-dapp').click();
					})
		}
		else if (type == 'custom-token'){
            let symbolLen = $('#custom-token-symbol').val().length;
            let decimals = $('#custom-token-decimals').val();
            let totalSupply = $('#custom-token-supply').val();
            let benefeciary = $('#custom-token-benefeciary').val();
            let symbol = $('#custom-token-symbol').val();

            if (symbol == ""){
                showError('Please enter token symbol');
                return false;
            }
            if (decimals = ""){
                showError('Please enter decimals');
                return false;
            }
            if (symbolLen > 10){
                showError('Token symbol should be shorter than 10 letters');
                return false;
            }
            if (totalSupply = "" || totalSupply <= 0){
                showError('Please enter total supply');
                return false;
            }
            if (!benefeciary.match(/^0x[0-9a-fA-F]+$/)) {
                showError('Please enter valid benefeciary address');
                return false;
            }
            $('#create-custom-token-dapp').click();
        }
		return false;
	});
        
        $("#wallets-confirmations").click(function(){
            $("#multisig-approvals").focus();
        });
        $("#multisig-approvals").change(function(){
            countWalletApprovals();
        });
        function countWalletOwners(){
            var owners = 0;
            $("#multisig-owners input").each(function(){
                owners++;
            });
            $("#wallets-owners").text(owners);
        }
        function countWalletApprovals(){
            var approvals = $("#multisig-approvals").val();
            $("#wallets-confirmations").text(approvals);
        }
        
        function getDappInfo(){
            var type = $('#dapp-type-select').val();
            var name = $('#dapp-name').val();
            var dapp_template = {
                name: name,
                type: type,
            };
            if (type == 'voting') {
                dapp_template.candidates = [];
                dapp_template.blind = $('#voting-blind').val();
                $('#voting-candidates input').each(function(){
                    dapp_template.candidates.push($(this).val());
                });
            } else if (type == 'escrow') {
                dapp_template.price = $('#escrow-price').val();
                dapp_template.seller = $('#escrow-seller').val();
                dapp_template.buyer = $('#escrow-buyer').val();
                dapp_template.oracle = $('#escrow-oracle').val();
                dapp_template.fee = $('#escrow-fee').val();
                dapp_template.timelimit = $('#escrow-timelimit').val();
            } else if (type == 'multisig') {
                dapp_template.balance = $('#multisig-balance').val();
                dapp_template.approvals = $('#multisig-approvals').val();
                dapp_template.owners = [];
                $('#multisig-owners input').each(function(){
                    dapp_template.owners.push($(this).val());
                });
            } else if (type == 'betting') {
                dapp_template.bids = [];
                dapp_template.arbitrator = $('#betting-arbitrator').val();
                dapp_template.fee = $('#betting-fee').val();
                $('#betting-bids input').each(function(){
                    dapp_template.bids.push($(this).val());
                });
            } else if (type == 'custom-token'){
                dapp_template.symbol = $('#custom-token-symbol').val();
                dapp_template.decimals = $('#custom-token-decimals').val();
                dapp_template.totalSupply = $('#custom-token-supply').val();
                dapp_template.benefeciary = $('#custom-token-benefeciary').val();
            }
            return dapp_template;
        }
        function simulator(){
            var info = getDappInfo();
            info.current_address = (typeof(web3) != 'undefined' && web3.eth.defaultAccount) ? web3.eth.defaultAccount : '';
            var params = JSON.stringify(info);
            var simulator_url = "simulator.php?params=" + encodeURIComponent(params);
            $("#simulator-iframe").attr("src", simulator_url);
        }
        $("#manage-panel").on("change", "input, select", function(){
            simulator();
        });
        $("#manage-panel").on("click", ".btn-remove", function(){
            setTimeout(function(){
                simulator();
            }, 1000);
        });
</script>

<div id="errorModal" class="modal fade" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header text-center">    
				<button id="error-close" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h2><?php echo $_newdapp['error']; ?></h2>
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
				<h2><?php echo $_newdapp['pending_transaction']; ?></h2>
				<p><?php echo $_newdapp['wait_deployment']; ?></p>
			</div>
			<div class="modal-body"></div>
		</div>
	</div>
</div>

<div id="rinkebyModal" class="modal fade" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header text-center">    
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h2><?php echo $_index_dictionary['confirm_Transaction']; ?></h2>
				<p><?php echo $_index_dictionary['creating_transaction']; ?></p>
                                <p><?php echo $_index_dictionary['test_our_platform']; ?> <a href="https://www.rinkeby.io/" target="_blank">Rinkeby Test Network</a>, <?php echo $_index_dictionary['because_Main']; ?> <a href="https://faucet.rinkeby.io/" target="_blank">Rinkeby Crypto Faucet</a>.</p>
                                <p><?php echo $_index_dictionary['using_Rinkeby_Test']; ?> <a onclick="location.reload();" style="cursor:pointer;"><?php echo $_index_dictionary['refresh_page']; ?></a>.</p>
                                <button type="button" id="main-deploy-btn" class="btn btn-primary"><?php echo $_index_dictionary['deploy_Main']; ?></button>
			</div>
			<div class="modal-body"></div>
		</div>
	</div>
</div>
<?php require_once('common/footer.php'); ?>