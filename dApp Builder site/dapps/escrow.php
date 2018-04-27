<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
	<link href="assets/css/bootstrap.min.css" rel="stylesheet">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $name; ?></title>
    <script type="text/javascript" src="assets/js/jquery.min.js"></script>
</head>
<body>
<div class="container text-center">
	<h1 id="name"></h1>
	<h4 id="status"></h4>
	<p>Seller: <span class="eth_address" id="seller"></span></p>
	<p>Buyer: <span class="eth_address" id="buyer"></span></p>
	<p>Agent: <span class="eth_address" id="oracle"></span></p>
	<p>Price: <span id="price"></span></p>
	<p>Agent's fee: <span id="oracle-fee"></span></p>
	<p id="timelimit"></p>
	<p style="display:none;" id="cur-block"></p>
    <div class="actions-wrapper">
		<button style="display:none;" type="button" class="btn btn-success btn-ok" id="paybutton">Pay</button>
		<button style="display:none;" type="button" class="btn btn-danger btn-cancel" id="refund">Refund</button>
		<button style="display:none;" type="button" class="btn btn-success btn-ok" id="close">Close the Deal</button>
		<button style="display:none;" type="button" class="btn btn-danger btn-cancel" id="cancel">Cancel the Deal</button>
    </div>
</div>
    
<?php require_once __DIR__ . '/../common/dapp-placeholder.php'; ?>
    
<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
<script type="text/javascript">
var dapp = (function(){
	var timerClock;
        
        <?php if ($network == 'main') { ?>
            var addressc = '<?php echo ESCROW_MAIN_ADDRESS; ?>';
        <?php } elseif ($network == 'rinkeby') { ?>
            var addressc = '<?php echo ESCROW_RINKEBY_ADDRESS; ?>';
        <?php } ?>
        
	var seller = '<?php echo $key_eth_account; ?>';
        
	var ABI = <?php echo ESCROW_ABI; ?>;

	return {
		init: function(name){
			window.contract = web3.eth.contract(ABI).at(addressc);
			this.getBidIndex(name)
				.then(index => {
					return this.getBid(index);
				})
				.then(bid => {
					this.render(bid);
					this.event(bid);
				})
		},
		getBidIndex: function(name){
			return new Promise(resolve =>{
				contract.getBidIndex(seller, web3.toHex(name), function(e,d){
					resolve(d.c[0]);
				})
			})
		},
		getBid: function(index){
			return new Promise(resolve =>{
				contract.bids(seller, index, function(e,d){
					d[9] = index;
					resolve(d);
				})
			})
		},
		render: function(bid){
			console.log(bid);
                        var escrow_name = web3.toAscii(bid[0]).replace(/\0/g,'');
			$('#name').html(escrow_name);
			$('#seller').html(bid[2]);
			$('#buyer').html(bid[3]);
			$('#oracle').html(bid[1]);
			$('#oracle-fee').html(web3.fromWei(bid[7],'ether') + ' ETH');
			$('#price').html(web3.fromWei(bid[4], 'ether') + ' ETH');
			var timelimited = bid[8];
			if (!timelimited) {
				var timelimit = "The deal is not limited in time";
			} else {
				var timelimit = "The deal is valid before the block <span class=\"eth_address\">#" + bid[5].c[0] + "</span>";
			}
			$('#timelimit').html(timelimit);
			var status = bid[6].c[0];
			var status_message;
			switch (status) {
				case 0:
					status_message = 'The order is unpaid';
					break;
				case 1:
					status_message = 'The order is paid by the buyer';
					break;
				case 2:
					status_message = 'The deal is successfully closed';
					break;
				case 3:
					status_message = 'The deal is cancelled';
					break;
				case 4:
					status_message = 'The buyer returned his money after the time limit has expired';
			}
			$('#status').html(status_message);
			if (bid[3] == web3.eth.defaultAccount && status == 0){
				$('#paybutton').show();
				$('#paybutton').on('click', function(){
					$(window).trigger('pay', {bid: bid});
				});
			} else {
				$('#paybutton').hide();
			}
			
			web3.eth.getBlockNumber(function(e,result){
				var blockNumber = result;
				if (bid[8] == true && bid[5].c[0] < blockNumber && status != 4){
					if (bid[3] == web3.eth.defaultAccount && status == 1) {
						$('#refund').show();
						$('#refund').unbind('click');
						$('#refund').on('click', function(){
							$(window).trigger('refund', {bid: bid});
						});
					}
					$('#close').hide();
					$('#paybutton').hide();
					$('#cancel').hide();
					$('#status').html('The time limit has expired');
				} else {
					$('#refund').hide();
				}
				if (bid[8] == true) {
					$('#cur-block').html('The current block is <span class="eth_address">#' + blockNumber + '</span>');
					$('#cur-block').show();
				}
			});
			
			if (bid[1] == web3.eth.defaultAccount && status == 1){
				$('#close').show();
				$('#cancel').show();
				$('#close').on('click', function(){
					$(window).trigger('close', {bid: bid});
				});
				$('#cancel').on('click', function(){
					$(window).trigger('reject', {bid: bid});
				});
			} else {
				$('#close').hide();
				$('#cancel').hide();
			}
			
			/*var filter = web3.eth.filter({fromBlock:'latest'});
			filter.watch(function(error, result){
				var blockNumber = result.blockNumber;
				if (bid[8] == true && bid[5].c[0] < blockNumber && status != 4){
					if (bid[3] == web3.eth.defaultAccount && status == 1) {
						$('#refund').show();
						$('#refund').unbind('click');
						$('#refund').on('click', function(){
							$(window).trigger('refund', {bid: bid});
						});
					}
					$('#close').hide();
					$('#paybutton').hide();
					$('#cancel').hide();
					$('#status').html('The time limit has expired');
				} else {
					$('#refund').hide();
				}
				if (bid[8] == true) {
					$('#cur-block').html('The current block is <span class="eth_address">#' + blockNumber + '</span>');
					$('#cur-block').show();
				}
			});*/
			
                        if (escrow_name) managePlaceHolders();
                        
			timerClock = setInterval(function(){
				web3.eth.getBlockNumber(function(e,result){
					var blockNumber = result;
					console.log(blockNumber);
					if (bid[8] == true && bid[5].c[0] < blockNumber && status != 4){
						if (bid[3] == web3.eth.defaultAccount && status == 1) {
							$('#refund').show();
							$('#refund').unbind('click');
							$('#refund').on('click', function(){
								$(window).trigger('refund', {bid: bid});
							});
						}
						$('#close').hide();
						$('#paybutton').hide();
						$('#cancel').hide();
						$('#status').html('The time limit has expired');
					} else {
						$('#refund').hide();
					}
					if (bid[8] == true) {
						$('#cur-block').html('The current block is <span class="eth_address">#' + blockNumber + '</span>');
						$('#cur-block').show();
					}
				});
			}, 1000);
		},
		pay: function(bid){
			var transactionData = contract.sendAmount.getData(seller,bid[9]);
			web3.eth.estimateGas({
				to: addressc,
				data: transactionData,
				value: bid[4]
			}, function(e,d){
				web3.eth.sendTransaction({
					to: addressc,
					data: transactionData,
					value: bid[4],
					gas: d
				}, function(er,da){
					console.log(da);
				})
			})
		},
		close: function(bid){
			var transactionData = contract.closeBid.getData(seller,bid[9]);
			web3.eth.estimateGas({
				to: addressc,
				data: transactionData
			}, function(e,d){
				web3.eth.sendTransaction({
					to: addressc,
					data: transactionData,
					gas: d
				},function(ed,da){
					console.log(da);
				})
			})
		},
		reject: function(bid){
			var transactionData = contract.rejectBid.getData(seller,bid[9]);
			web3.eth.estimateGas({
				to: addressc,
				data: transactionData
			}, function(e,d){
				web3.eth.sendTransaction({
					to: addressc,
					data: transactionData,
					gas: d
				},function(ed,da){
					console.log(da);
				})
			})
		},
		refund: function(bid){
			console.log(bid);
			var transactionData = contract.refund.getData(seller,bid[9]);
			web3.eth.estimateGas({
				to: addressc,
				data: transactionData
			}, function(e,d){
				web3.eth.sendTransaction({
					to: addressc,
					data: transactionData,
					gas: d
				},function(ed,da){
					console.log(da);
				})
			})
		},
		event: function(bid){
			var payEvent = window.contract.amountRecieved();
			payEvent.watch(function(e,d){
				console.log('pay event');
				if (d.args.seller == seller && bid[9] == d.args.bidId){
					$('button').unbind('click');
					bid[6].c[0] = 1;
					clearInterval(timerClock);
					dapp.render(bid);
				}
			});
			var closeEvent = window.contract.bidClosed();
			closeEvent.watch(function(e,d){
				console.log('close event');
				if (d.args.seller == seller){
					$('button').unbind('click');
					bid[6].c[0] = 2;
					clearInterval(timerClock);
					dapp.render(bid);
				}
			});
			var refundEvent = window.contract.refundDone();
			refundEvent.watch(function(e,d){
				console.log('refund event');
				if (d.args.seller == seller){
					$('button').unbind('click');
					bid[6].c[0] = 4;
					clearInterval(timerClock);
					dapp.render(bid);
				}
			});
			var rejectEvent = window.contract.bidRejected();
			rejectEvent.watch(function(e,d){
				console.log('reject event');
				if (d.args.seller == seller && bid[9] == d.args.bidId){
					$('button').unbind('click');
					bid[6].c[0] = 3;
					clearInterval(timerClock);
					dapp.render(bid);
				}
			});
			$(window).on('pay', function(el,data){
				console.log('trigger');
				dapp.pay(data.bid);
			});
			$(window).on('close', function(el,data){
				console.log('trigger');
				dapp.close(data.bid);
			});
			$(window).on('reject', function(el,data){
				console.log('trigger');
				dapp.reject(data.bid);
			});
			$(window).on('refund', function(el,data){
				console.log('trigger');
				dapp.refund(data.bid);
			});
		}
	}
})()


window.addEventListener('load', function(){
	if (typeof(window.web3.eth.defaultAccount) != 'undefined' && window.web3.eth.defaultAccount) {
		dapp.init('<?php echo $name; ?>');
	} else {
		var initTimer = setInterval(function(){
			if (typeof(window.web3.eth.defaultAccount) != 'undefined' && window.web3.eth.defaultAccount) {
				dapp.init('<?php echo $name; ?>');
				clearInterval(initTimer);
			}
		}, 1000);
	}
});
</script>

<style>
	body{
		color: <?php echo $interface['text_color']; ?>;
		background-color: <?php echo $interface['background_color']; ?>;	}
	.eth_address{color: <?php echo $interface['eth_addresses_color']; ?>;	}
	a, a:focus, a:hover, a:active{
		text-decoration: none;
		color: <?php echo $interface['links_color']; ?>;	}
	.btn.btn-danger.btn-cancel{
		background-color: <?php echo $interface['cancel_buttons_color']; ?>;
		border-color: <?php echo $interface['cancel_buttons_color']; ?>;	}
	.btn.btn-success.btn-ok{
		background-color: <?php echo $interface['ok_buttons_color']; ?>;
		border-color: <?php echo $interface['ok_buttons_color']; ?>;	}
	.btn.btn-danger.btn-cancel:hover, .btn.btn-success.btn-ok:hover{ opacity: 0.8;	}
    #name {color: <?php echo $interface['headers_color']; ?>;}
	.btn {padding: 8px 38px; text-transform: uppercase; margin:5px;}
</style>

</body>
</html>