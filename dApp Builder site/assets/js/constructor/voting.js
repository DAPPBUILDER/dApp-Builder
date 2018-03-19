var voteDapp = (function(){
	var voteContractAdress = '0x6f79417f9ef721e0c2d6f0843e6084d79386dcbd';
	var voteContractABI = [{"constant":true,"inputs":[{"name":"chainperson","type":"address"},{"name":"ballot","type":"uint256"}],"name":"getVoted","outputs":[{"name":"","type":"address[]"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"chainperson","type":"address"},{"name":"ballot","type":"uint256"},{"name":"proposalNum","type":"uint256"}],"name":"vote","outputs":[{"name":"success","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"name":"","type":"address"},{"name":"","type":"uint256"}],"name":"ballots","outputs":[{"name":"name","type":"bytes32"},{"name":"chainperson","type":"address"},{"name":"blind","type":"bool"},{"name":"finished","type":"bool"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"ballot","type":"bytes32"}],"name":"finishBallot","outputs":[{"name":"success","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"name":"chainperson","type":"address"},{"name":"ballot","type":"uint256"}],"name":"getProposalsNum","outputs":[{"name":"count","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"chainperson","type":"address"},{"name":"ballot","type":"uint256"}],"name":"isVoted","outputs":[{"name":"result","type":"bool"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"chainperson","type":"address"},{"name":"ballot","type":"uint256"},{"name":"proposalName","type":"bytes32"}],"name":"getProposalIndex","outputs":[{"name":"index","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"chainperson","type":"address"},{"name":"ballotName","type":"bytes32"}],"name":"getBallotIndex","outputs":[{"name":"index","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"chainperson","type":"address"},{"name":"ballotIndex","type":"uint256"}],"name":"getWinner","outputs":[{"name":"winnerName","type":"bytes32"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"chainperson","type":"address"},{"name":"ballot","type":"uint256"},{"name":"proposalName","type":"bytes32"}],"name":"getVotesCount","outputs":[{"name":"count","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"","type":"address"},{"name":"","type":"uint256"},{"name":"","type":"uint256"}],"name":"proposals","outputs":[{"name":"name","type":"bytes32"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"chainperson","type":"address"}],"name":"getBallotsNum","outputs":[{"name":"count","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"ballotName","type":"bytes32"},{"name":"blindParam","type":"bool"},{"name":"proposalNames","type":"bytes32[]"}],"name":"startNewBallot","outputs":[{"name":"success","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"name":"chainperson","type":"address"},{"name":"ballot","type":"uint256"},{"name":"voter","type":"address"}],"name":"getVotedData","outputs":[{"name":"proposalNum","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"anonymous":false,"inputs":[{"indexed":false,"name":"votedPerson","type":"address"},{"indexed":false,"name":"proposalIndex","type":"uint256"}],"name":"Vote","type":"event"},{"anonymous":false,"inputs":[{"indexed":false,"name":"finished","type":"bool"}],"name":"Finish","type":"event"}];
    return {
        init: function(){
            window.web3 = new Web3(web3.currentProvider);
            window.voteContract = web3.eth.contract(voteContractABI).at(voteContractAdress);
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
									addApp(type, pure_name);
								}
                            })
                    }
                });
                return false;
            });
			
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
													location.href = '/builder/my-dapps.php';
												} else {
													delete window.votingUndeployed[id];
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
    }
})();

window.addEventListener('load', function(){
	voteDapp.init();
});