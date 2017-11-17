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
	<p id="blind"></p>
	<p>The list of candidates:</p>
	<div class="proposals row"></div>
	<p style="padding-top:15px;">
		<button type="button" style="display:none;" class="btn btn-danger" id="finish">Finish the voting</button>
	</p>
</div>
<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
<script type="text/javascript">
var dapp = (function(){
    var chainPerson = '<?php echo $eth_account; ?>';
	var contractAdress = '0x6f79417f9ef721e0c2d6f0843e6084d79386dcbd';
    var contractABI = [{"constant":true,"inputs":[{"name":"chainperson","type":"address"},{"name":"ballot","type":"uint256"}],"name":"getVoted","outputs":[{"name":"","type":"address[]"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"chainperson","type":"address"},{"name":"ballot","type":"uint256"},{"name":"proposalNum","type":"uint256"}],"name":"vote","outputs":[{"name":"success","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"name":"","type":"address"},{"name":"","type":"uint256"}],"name":"ballots","outputs":[{"name":"name","type":"bytes32"},{"name":"chainperson","type":"address"},{"name":"blind","type":"bool"},{"name":"finished","type":"bool"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"ballot","type":"bytes32"}],"name":"finishBallot","outputs":[{"name":"success","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"name":"chainperson","type":"address"},{"name":"ballot","type":"uint256"}],"name":"getProposalsNum","outputs":[{"name":"count","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"chainperson","type":"address"},{"name":"ballot","type":"uint256"}],"name":"isVoted","outputs":[{"name":"result","type":"bool"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"chainperson","type":"address"},{"name":"ballot","type":"uint256"},{"name":"proposalName","type":"bytes32"}],"name":"getProposalIndex","outputs":[{"name":"index","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"chainperson","type":"address"},{"name":"ballotName","type":"bytes32"}],"name":"getBallotIndex","outputs":[{"name":"index","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"chainperson","type":"address"},{"name":"ballotIndex","type":"uint256"}],"name":"getWinner","outputs":[{"name":"winnerName","type":"bytes32"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"chainperson","type":"address"},{"name":"ballot","type":"uint256"},{"name":"proposalName","type":"bytes32"}],"name":"getVotesCount","outputs":[{"name":"count","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"","type":"address"},{"name":"","type":"uint256"},{"name":"","type":"uint256"}],"name":"proposals","outputs":[{"name":"name","type":"bytes32"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"chainperson","type":"address"}],"name":"getBallotsNum","outputs":[{"name":"count","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"ballotName","type":"bytes32"},{"name":"blindParam","type":"bool"},{"name":"proposalNames","type":"bytes32[]"}],"name":"startNewBallot","outputs":[{"name":"success","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"name":"chainperson","type":"address"},{"name":"ballot","type":"uint256"},{"name":"voter","type":"address"}],"name":"getVotedData","outputs":[{"name":"proposalNum","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"anonymous":false,"inputs":[{"indexed":false,"name":"votedPerson","type":"address"},{"indexed":false,"name":"proposalIndex","type":"uint256"}],"name":"Vote","type":"event"},{"anonymous":false,"inputs":[{"indexed":false,"name":"finished","type":"bool"}],"name":"Finish","type":"event"}];
    window.web3 = new Web3(web3.currentProvider);
    window.contract = web3.eth.contract(contractABI).at(contractAdress);
	
    return {
        init: function(name){
            $('#name').html(name);
            this.getBallotIndex(name)
                .then(index => {
                    return index;
                })
                .then(index => {
                    return this.getBallotData(chainPerson, index);
                })
                .then(data => {
                    return this.getProposalsNum(data);
                })
                .then(data => {
                    if (data) {return data}
                })
                .then(data => {
                    return this.getProposals(data);
                })
                .then(data => {
                    if (data.ballot[2] == false) {
                        return this.getVoteCount(data);
                    } else {
                        return data;
                    }
                })
                .then(data => {
                    if (data.ballot[2] == false) {
                        return this.getWhoVoted(data);
                    } else {
                        return data;
                    }
                })
                .then(data => {
                    return this.getProposalsIndex(data);
                })
                .then(data => {
                    if (typeof(data.voted) != 'undefined' && data.voted.length>0){
                        return this.getVotedDatas(data);
                    } else {
                        return data;
                    }
                })
                .then(data => {
                    return this.getIsVoted(data);
                })
				.then(data => {
                    if (data.ballot[3] == true){
                        return this.getWinner(data);
                    } else {
                        return data;
                    }
                })
                .then(data => {
                    console.log(data);
                    this.fillProposals(data);
					this.event(data);
                });
        },
        getBallotIndex: function(name){
            return new Promise(function(resolve,reject){
                var nameInBytes = web3.fromAscii(name,32);
                contract.getBallotIndex(chainPerson, nameInBytes, function(e,d){
                    resolve(d.c[0]);
                })
            })
        },
		getWinner: function(data){
            return new Promise(function(resolve,reject){
                contract.getWinner(chainPerson, data.index, function(e,d){
                    data.winner = d;
                    resolve(data);
                })
            })
        },
        getBallotData: function(chainPerson,ballotIndex){
            return new Promise(resolve => {
                contract.ballots(chainPerson, ballotIndex, function(e,d){
                    resolve({index: ballotIndex, ballot: d});
                })
            })
        },
        getProposalsNum: function(data){
                return new Promise(function(resolve, reject){
                    contract.getProposalsNum(chainPerson, data.index, function(e,d){
                        if (d){
                            resolve({proposalsNum:d.c[0], index: data.index, ballot: data.ballot});
                        }
                        else {
                            reject(e);
                        }
                    })
                })
        },
        getProposals: function(data){
            return new Promise(resolve => {
                var proposals = [];
                for (var i=0;i<data.proposalsNum;i++){
                    (function(i){
                        new Promise(rslv => {
                            contract.proposals(chainPerson, data.index, i, function(e,d){
                                rslv(d);
                            })
                        })
                        .then(d => {
                                proposals.push({name: d});
                                if (proposals.length == data.proposalsNum) {
                                    resolve({index: data.index, ballot: data.ballot, proposals: proposals});
                                }
                            })
                    })(i)
                }
            })
        },
        getVoteCount: function(data){
            return new Promise(resolve => {
                var proposals = [];
                for (var i=0;i<data.proposals.length;i++){
                    (function(i){
                        contract.getVotesCount(chainPerson, data.index, data.proposals[i].name, function(e,d){
                            proposals.push({name: data.proposals[i].name, voteCount: d.c[0]});
                            if (proposals.length == data.proposals.length){
                                resolve({index: data.index, ballot: data.ballot, proposals: proposals});
                            }
                        })
                    })(i)
                }
            })
        },
        getWhoVoted: function(data){
            return new Promise(resolve => {
                contract.getVoted(chainPerson, data.index, function(e,d){
                    data.voted = d;
                    resolve(data);
                })
            })
        },
        getVotedDatas: function(data){
            return new Promise(resolve => {
                var newVoted = [];
                for (var i=0;i<data.voted.length;i++){
                    (function(i){
                        contract.getVotedData(chainPerson, data.index, data.voted[i], function(e,d){
                            newVoted.push({address: data.voted[i], proposalIndex: d.c[0]});
                            if (newVoted.length == data.voted.length){
                                data.voted = newVoted;
                                resolve(data);
                            }
                        })
                    })(i)
                }
            })
        },
        getProposalsIndex: function(data){
            return new Promise(resolve => {
                var proposals = [];
                for (var i=0;i<data.proposals.length;i++){
                    (function(i){
                        contract.getProposalIndex(chainPerson, data.index, data.proposals[i].name, function(e,d){
                            proposals.push({name: data.proposals[i].name, voteCount: data.proposals[i].voteCount, index: d.c[0]});
                            if (proposals.length == data.proposals.length){
                                resolve({index: data.index, ballot: data.ballot, proposals: proposals, voted: data.voted});
                            }
                        })
                    })(i)
                }
            })
        },
        getIsVoted: function(data){
            return new Promise(resolve => {
                contract.isVoted(chainPerson, data.index, function(e,d){
                    data.isVoted = d;
                    resolve(data);
                })
            })
        },
        fillProposals: function(data){
            for (var i=0;i<data.proposals.length;i++){
                (function(i){
                    var el = $('<div class="col-md-6 proposal-'+data.proposals[i].index+'"><h3>'+web3.toAscii(data.proposals[i].name).replace(/\0/g,'')+'</h3></div>');
                    $('.proposals').append(el);
                    if (data.ballot[2] == false){
                        $('#blind').html('This voting is not secret, you can see other votes.');
                        var count = $('<p>Votes count: <span class="votes-count">'+data.proposals[i].voteCount+'</span></p><p><a style="display:none;" id="collapse-link'+data.proposals[i].index+'" data-toggle="collapse" aria-expanded="false" aria-controls="collapseExample'+data.proposals[i].index+'" href="#whovoted'+data.proposals[i].index+'">Show votes</a></p>');
                        el.append(count);
						var votedWrapper = $('<div class="whovoted collapse" id="whovoted'+data.proposals[i].index+'"></div>');
						for (var x=0;x<data.voted.length;x++){
							if (data.voted[x].proposalIndex == data.proposals[i].index){
								votedWrapper.append('<p class="voted_address">'+data.voted[x].address+'</p>');
								$("#collapse-link"+data.proposals[i].index).show();
							}
						}
						$("#collapse-link"+data.proposals[i].index).click(function(){
							if ($(this).text() == 'Show votes') {
								$(this).text('Votes:');
							} else {
								$(this).text('Show votes');
							}
						});
						el.append(votedWrapper);
                    } else {
                        $('#blind').html('This voting is secret, you can not see other votes.');
                    }
                    if (data.ballot[3] == false && data.isVoted == false){
                        var link = $('<p><button type="button" class="btn btn-success vote-button">Vote</button></p>');
                        el.append(link);
                        link.on('click',function(){
                            $(window).trigger('vote',{ballot: data.index,proposal: data.proposals[i].index});
                            return false;
                        });
                    }
                })(i)
            }
            if (data.ballot[1] == web3.eth.defaultAccount && data.ballot[3] == false){
                $('#finish').on('click', function(){
                    $(window).trigger('finish', {name: data.ballot[0]});
                    return false;
                });
				$('#finish').show();
            } else {
                $('#finish').hide();
            }
			if (data.ballot[3] == true){
                $('#blind').after('<p class="finished">This voting is finished, no votes accept anymore.</p>');
                $('.finished').after('<p class="winner">The winner is: '+web3.toAscii(data.winner).replace(/\0/g,'')+'</p>');
                $(window).find('.vote_link').hide();
            }
        },
        vote: function(ballotIndex, proposalIndex){
            var transactionData = contract.vote.getData(chainPerson, ballotIndex, proposalIndex);
            web3.eth.estimateGas({to: contractAdress, data: transactionData}, function(e,d){
                if (d){
                    web3.eth.sendTransaction({
                        to: contractAdress,
                        data: transactionData,
                        gas: d
                    }, function(er,da){
                        console.log('Error: '+er+' Data: '+da);
                    })
                }
            })
        },
        finish: function(ballotName){
            var transactionData = contract.finishBallot.getData(ballotName);
            web3.eth.estimateGas({to: contractAdress, data: transactionData}, function(e,d){
                if (d){
                    web3.eth.sendTransaction({
                        to: contractAdress,
                        data: transactionData,
                        gas: d
                    }, function(er,da){
                        if (er){
                            console.log('Error: '+er);
                        } else {
                            console.log('Data: '+da);
                        }
                    })
                }
            })
        },
		closeBallot: function(data){
            contract.getWinner(chainPerson, data.index, function(e,d){
                var winner = d;
				$('#blind').after('<p class="finished">This voting is finished, no votes accept anymore.</p>');
                $('.finished').after('<p class="winner">The winner is: '+web3.toAscii(winner).replace(/\0/g,'')+'</p>');
                $(window).find('.vote_link').hide();
				$('#finish').hide();
            })
        },
        event: function(data){
            $(window).on('vote', function(element,data){
                dapp.vote(data.ballot, data.proposal);
            });
            $(window).on('finish', function(element,data){
                dapp.finish(data.name);
            })
			
			var voteEvent = contract.Vote();
			var voted = false;
			voteEvent.watch(function(e,d){
				if (!e && !voted){
					console.log(d.args);
					var proposalIndex = d.args.proposalIndex.c[0];
					$('.proposal-'+proposalIndex).find('.whovoted').append('<p class="voted_address">'+d.args.votedPerson+'</p>');
					if (d.args.votedPerson == web3.eth.defaultAccount) {
						$('.vote-button').hide();
						$('#collapse-link'+proposalIndex).show();
						$('.proposal-'+proposalIndex).find('.votes-count').text(parseInt($('.proposal-'+proposalIndex).find('.votes-count').text()) + 1);
						voted = true;
					}
				} else if(e) {
					console.log(e);
				}
			});
			var finishEvent = contract.Finish();
			var finished = false;
            finishEvent.watch(function(e,d){
                if (!e && !finished){
                    console.log(d.args.finished);
                    dapp.closeBallot(data);
					finished = true;
                }
            });
        }
    }
})();

window.addEventListener('load', function(){
	if (typeof(window.web3.eth.defaultAccount) != 'undefined' && window.web3.eth.defaultAccount) {
		dapp.init('<?php echo $name; ?>');
	} else {
		var initTimer = setInterval(function(){
			if (typeof(window.web3.eth.defaultAccount) != 'undefined' && window.web3.eth.defaultAccount) {
				dapp.init('<?php echo $name; ?>');
				clearInterval(initTimer);
			} else {
				console.log(window.web3.eth.defaultAccount);
			}
		}, 1000);
	}
});
</script>

<style>
	body{
		color: <?php echo $interface['text_color']; ?>;
		background-color: <?php echo $interface['background_color']; ?>;
	}
	.voted_address{
		color: <?php echo $interface['eth_addresses_color']; ?>;
	}
	a, a:focus, a:hover, a:active{
		text-decoration: none;
		color: <?php echo $interface['links_color']; ?>;
	}
</style>

</body>
</html>