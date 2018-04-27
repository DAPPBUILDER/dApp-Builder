var escrowDapp = (function(){
        if (web3.version.network == "1") {
            var escrowContractAddress = escrowMainAddress;
            var network = 'main';
        } else if (web3.version.network == "4") {
            var escrowContractAddress = escrowRinkebyAddress;
            var network = 'rinkeby';
        } else {
            return;
        }
	
	return {
		init: function(){
			window.escrowContract = web3.eth.contract(escrowABI).at(escrowContractAddress);
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
						addApp(type, pure_name, network, seller);
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
                                                                                                    if (network == "main") {
                                                                                                        location.href = '/builder/my-dapps.php';
                                                                                                    } else if (network == "rinkeby") {
                                                                                                        location.href = '/builder/my-dapps.php?network=rinkeby';
                                                                                                    }
												} else {
													delete window.escrowUndeployed[id];
													if (network == "main") {
                                                                                                            $("#my-dapps-li").fadeIn();
                                                                                                        } else if (network == "rinkeby") {
                                                                                                            $("#my-test-dapps-li").fadeIn();
                                                                                                        }
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
                        if (network == "main") {
                            window.escrowUndeployed = window.escrowMainUndeployed;
                        } else if (network == "rinkeby") {
                            window.escrowUndeployed = window.escrowRinkebyUndeployed;
                        }
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