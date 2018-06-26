var voteDapp = (function(){
    if (web3.version.network == "1") {
        var voteContractAdress = votingMainAddress;
        var network = 'main';
    } else if (web3.version.network == "4") {
        var voteContractAdress = votingRinkebyAddress;
        var network = 'rinkeby';
    } else {
        return;
    }
        
    return {
        init: function(){
            window.web3 = new Web3(web3.currentProvider);
            window.voteContract = web3.eth.contract(votingABI).at(voteContractAdress);
            this.event();
        },
        event: function(){
            $('#voting-add-candidate').click(function(){
                $('#voting-candidates').append(
					'<div style="display:none;" class="input-group creation-form">' +
						'<span class="input-group-addon">Candidate:</span>' +
						'<input placeholder="Candidate.." type="text" class="form-control required-voting required-dapp" required>' +
						'<span class="input-group-btn">' +
							'<button class="btn btn-danger btn-remove" type="button"><i class="fa fa-fw fa-times"></i></button>' +
						'</span>' +
					'</div>'
                );
				$('#voting-candidates>div').fadeIn();
                return false;
            });
			
			$("#voting-candidates").on('click', '.btn-remove', function(){
				$(this).parents(".creation-form").fadeOut(function(){$(this).remove()});
			});

            $('#create-voting-dapp').click(function(){
                var options = [];
				var pure_name = $('#dapp-name').val();
				var type = $('#dapp-type-select').val();
                var name = web3.fromAscii(pure_name,32);
				var blind = $('#voting-blind').val();
				if (blind == '1') blind = true;
				else blind = false;
                $(document).find('#voting-candidates input').each(function(){
                    val = $(this).val();
                    opt = web3.fromAscii(val,32);
                    options.push(opt);
                });

                var data = voteContract.startNewBallot.getData(name, blind, options);
                web3.eth.estimateGas({to: voteContractAdress, data: data}, function(e,d){
                    if (d){
                        web3.eth.sendTransaction(
                            {
                                to: voteContractAdress,
                                data: data,
                                gas: d
                            },
                            function(e,d){
								if (!e && d) {
									addApp(type, pure_name, network);
								}
                            })
                    }
                });
                return false;
            });
			if (network == "main") {
                            window.votingUndeployed = window.votingMainUndeployed;
                        } else if (network == "rinkeby") {
                            window.votingUndeployed = window.votingRinkebyUndeployed;
                        }
			if (!$.isEmptyObject(window.votingUndeployed)) {
				setInterval(function(){
					for (var key in window.votingUndeployed) {
						voteDapp.checkDeployed(window.votingUndeployed[key].address, window.votingUndeployed[key].name, key);
					}
				}, 3000);
			}
			
        },
		checkName: function(address, name, callback){
			var flag = true;
			var count = 0;
			voteContract.getBallotsNum(address,function(error,data){
                var len = data.c[0];
				if (len == 0) {
					callback();
					return;
				}
                for (var i = 0;i<len;i++){
                    (function(f){
                        voteContract.ballots(address,f, function(err,dat){
                            if (dat){
                                var ballotName = web3.toAscii(dat[0]).replace(/\0/g,'');
								count++;
								if (ballotName == name) {
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
            voteContract.getBallotsNum(address,function(error,data){
                var len = data.c[0];
                for (var i = 0;i<len;i++){
                    (function(f){
                        voteContract.ballots(address,f, function(err,dat){
                            if (dat){
                                var ballotName = web3.toAscii(dat[0]).replace(/\0/g,'');
								if (ballotName == name) {
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
													delete window.votingUndeployed[id];
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
    }
})();

window.addEventListener('load', function(){
	voteDapp.init();
});