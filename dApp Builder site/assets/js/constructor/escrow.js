var escrowDapp = (function(){
	var escrowContractAddress = '0x51Dd62DfB8bFC468c7Fad54756335dD2319aE3F8';
	var escrowContractABI = [{"constant":true,"inputs":[{"name":"","type":"address"},{"name":"","type":"uint256"}],"name":"bids","outputs":[{"name":"name","type":"bytes32"},{"name":"oracle","type":"address"},{"name":"seller","type":"address"},{"name":"buyer","type":"address"},{"name":"price","type":"uint256"},{"name":"timeout","type":"uint256"},{"name":"status","type":"uint8"},{"name":"fee","type":"uint256"},{"name":"isLimited","type":"bool"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"seller","type":"address"},{"name":"name","type":"bytes32"}],"name":"getBidIndex","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"seller","type":"address"},{"name":"bidId","type":"uint256"}],"name":"refund","outputs":[{"name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"seller","type":"address"},{"name":"bidId","type":"uint256"}],"name":"sendAmount","outputs":[],"payable":true,"stateMutability":"payable","type":"function"},{"constant":false,"inputs":[{"name":"name","type":"bytes32"},{"name":"seller","type":"address"},{"name":"oracle","type":"address"},{"name":"buyer","type":"address"},{"name":"price","type":"uint256"},{"name":"timeout","type":"uint256"},{"name":"fee","type":"uint256"}],"name":"createBid","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"seller","type":"address"},{"name":"bidId","type":"uint256"}],"name":"rejectBid","outputs":[{"name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"seller","type":"address"},{"name":"bidId","type":"uint256"}],"name":"closeBid","outputs":[{"name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"name":"","type":"address"}],"name":"pendingWithdrawals","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"seller","type":"address"}],"name":"getBidsNum","outputs":[{"name":"bidsNum","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"anonymous":false,"inputs":[{"indexed":false,"name":"seller","type":"address"},{"indexed":false,"name":"bidId","type":"uint256"}],"name":"amountRecieved","type":"event"},{"anonymous":false,"inputs":[{"indexed":false,"name":"seller","type":"address"},{"indexed":false,"name":"bidId","type":"uint256"}],"name":"bidClosed","type":"event"},{"anonymous":false,"inputs":[{"indexed":false,"name":"seller","type":"address"},{"indexed":false,"name":"name","type":"bytes32"},{"indexed":false,"name":"bidId","type":"uint256"}],"name":"bidCreated","type":"event"},{"anonymous":false,"inputs":[{"indexed":false,"name":"seller","type":"address"},{"indexed":false,"name":"bidId","type":"uint256"}],"name":"refundDone","type":"event"},{"anonymous":false,"inputs":[{"indexed":false,"name":"person","type":"address"},{"indexed":false,"name":"amount","type":"uint256"}],"name":"withdrawDone","type":"event"},{"anonymous":false,"inputs":[{"indexed":false,"name":"seller","type":"address"},{"indexed":false,"name":"bidId","type":"uint256"}],"name":"bidRejected","type":"event"}];
	
	return {
		init: function(){
			window.escrowContract = web3.eth.contract(escrowContractABI).at(escrowContractAddress);
			this.event();
		},
		create: function(){
			var pure_name = $('#dapp-name').val();
			var type = $('#dapp-type-select').val();
			
			var name = web3.toHex(pure_name);
			var seller = $('#escrow-seller').val().toLowerCase();
			var price = web3.toWei($('#escrow-price').val(), 'ether');
			var buyer = $('#escrow-buyer').val().toLowerCase();
			var oracle = $('#escrow-oracle').val().toLowerCase();
			var fee = web3.toWei($('#escrow-fee').val(), 'ether');
			var timelimit = $('#escrow-timelimit').val();

			var transactionData = escrowContract.createBid.getData(name, seller, oracle, buyer, price, timelimit, fee);
			web3.eth.estimateGas({
				to: escrowContractAddress,
				data: transactionData
			}, function(e,d){
				web3.eth.sendTransaction({
					to: escrowContractAddress,
					data: transactionData,
					gas: d
				}, function(er,da){
					console.log(da);
					if (!er && da) {
						addApp(type, pure_name, seller);
					}
				})
			})
		},
		checkName: function(address, name, callback){
			var flag = true;
			var count = 0;
			escrowContract.getBidsNum(address,function(error,data){
                var len = data.c[0];
				if (len == 0) {
					callback();
					return;
				}
                for (var i = 0;i<len;i++){
                    (function(f){
                        escrowContract.bids(address,f, function(err,dat){
                            if (dat){
                                var escrowName = web3.toAscii(dat[0]).replace(/\0/g,'');
								count++;
								if (escrowName == name) {
									flag = false;
									showNameError();
								}
								if (count == len && flag) {
									callback();
								}
                            }
                        });
                    })(i)
                }
            });
		},
		checkDeployed: function(address, name, id, redirect = false){
            escrowContract.getBidsNum(address,function(error,data){
                var len = data.c[0];
                for (var i = 0;i<len;i++){
                    (function(f){
                        escrowContract.bids(address,f, function(err,dat){
                            if (dat){
                                var escrowName = web3.toAscii(dat[0]).replace(/\0/g,'');
								if (escrowName == name) {
									$.ajax({
										url: '/builder/deploy_dapp.php',
										type: 'POST',
										dataType: 'json',
										cache: false,
										data: 'id=' + id,
										error: function(jqXHR, error){},
										success: function(data, status){
											if (!data.error && data.success) {
												if (redirect) {
													location.href = '/builder/my-dapps.php';
												} else {
													delete window.escrowUndeployed[id];
													$("#my-dapps-li").fadeIn();
												}
											}
										}
									});
								}
                            }
                        });
                    })(i)
                }
            });
        },
		event: function(){
			$('#create-escrow-dapp').click(function(){
				escrowDapp.create();
			});
			/*var createdEvent = escrowContract.bidCreated();
			createdEvent.watch(function(e,d){
				console.log(d.args);
			})*/
			if (!$.isEmptyObject(window.escrowUndeployed)) {
				setInterval(function(){
					for (var key in window.escrowUndeployed) {
						escrowDapp.checkDeployed(window.escrowUndeployed[key].address, window.escrowUndeployed[key].name, key);
					}
				}, 3000);
			}
		}
	}
})()


window.addEventListener('load', function(){
    escrowDapp.init();
});