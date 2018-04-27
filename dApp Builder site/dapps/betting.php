<?php
/**
 * Created by PhpStorm.
 * User: Fivehundreth
 * Date: 06.04.2018
 * Time: 14:51
 */
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/betting.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $name; ?></title>
    <script type="text/javascript" src="assets/js/jquery.min.js"></script>
</head>
<body>
<div class="container main text-center">
    <div class="dapp-block" id="info-block">
        <h1 id="name"></h1>
        <p>Creator: <span class="eth_address" id="creator"></span></p>
        <p>Arbitrator: <span class="eth_address" id="arbitrator"></span></p>
        <p>Arbitrator fee: <span class="eth_address" id="fee"></span><span class="eth_address">%</span></p>
        <p>Status: <span class="eth_address" id="status"></span></p>
        <p id="winner"></p>
    </div>
    <div class="dapp-block" id="bids">
        <h4>Bids:</h4>
        <div class="row bids-row"></div>
    </div>
    <div class="dapp-block nav-block">
        <div class="row">
            <div class="col-md-12">
                <button class="btn btn-danger finish-event btn-cancel">Finish event</button>
                <button class="btn btn-danger btn-cancel btn-large your-bets-btn">Your bets</button>
                <button class="btn btn-danger btn-cancel btn-large your-balance">Your balance</button>
            </div>
        </div>
    </div>
    <div class="dapp-block your-bets">
        <div class="row">
            <div class="col-md-12">
            </div>
        </div>
    </div>
    <div class="dapp-block balance">
        <div class="row">
            <div class="col-md-12">
            </div>
        </div>
    </div>
</div>
<div class="modal-txn container text-center">
    <div class="row">
        <div class="col-md-12">
            <h1>Transaction sent.</h1>
            <p>Please wait until it gets mined.</p>
            <p>Information on the page will update automatically.</p>
            <button class="btn btn-success btn-ok ok-back">Ok, back</button>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../common/dapp-placeholder.php'; ?>
<script type="text/javascript">
    var dapp = (function(){

        <?php if ($network == 'main') { ?>
        var contract_address = '<?php echo BETTING_MAIN_ADDRESS; ?>';
        <?php } elseif ($network == 'rinkeby') { ?>
        var contract_address = '<?php echo BETTING_RINKEBY_ADDRESS; ?>';
        <?php } ?>

        let ABI = <?php echo BETTING_ABI; ?>;
        return {
            event: {},
            init: function (creator, name) {
                window.contract = web3.eth.contract(ABI).at(contract_address);
                this.getId(creator, name)
                    .then(data => {
                        this.event.creator = creator;
                        this.event.name = name;
                        if (data[1] == true){
                            this.event.id = data[0].toNumber();
                            return this.getEvent(this.event.creator, this.event.id);
                        }
                    })
                    .then(eventData => {
                        this.event.arbitrator = eventData[3];
                        this.event.fee = eventData[5].toNumber();
                        this.event.winner = web3.toAscii(eventData[4]).replace(/\0/g,'');
                        switch (eventData[6].toNumber()){
                            case 0:
                                this.event.status = 'open';
                                break;
                            case 1:
                                this.event.status = 'finished';
                                break;
                            case 2:
                                this.event.status = 'closed';
                                break;
                        }
                        return this.getBids(this.event.creator, this.event.id);
                    })
                    .then(bids => {
                        this.event.bids = bids;
                        this.event.bids.sort(function(a,b){
                            if (a.id < b.id){
                                return -1;
                            } else {
                                return 1;
                            }
                        });
                        return this.getWhoBet(bids);
                    })
                    .then(bids =>{
                        this.event.bids = bids;
                        return this.getBets(this.event.creator, this.event.id);
                    })
                    .then(bets => {
                        this.event.bets = bets;
                        return this.getBalance();
                    })
                    .then(balance =>{
                        this.event.balance = balance;
                        this.renderEvent();
                        dapp.watch('bet');
                        dapp.watch('winner');
                        dapp.watch('finish');
                    })
            },
            getId: function(creator, name){
                return new Promise(resolve => {
                    contract.getEventId(creator, name, function(e,d){
                        resolve(d);
                    })
                })
            },
            getEvent: function(creator, id){
                return new Promise(resolve => {
                    contract.betEvents(creator, id, function(e,d){
                        resolve(d);
                    })
                })
            },
            getBalance: function(){
                return new Promise(resolve => {
                    contract.pendingWithdrawals(web3.eth.defaultAccount, function(e,d){
                        resolve(d.toNumber());
                    })
                })
            },
            getBids: function(creator, id){
                return new Promise(resolve => {
                    contract.getBidsNum(creator, id, function(e,d){
                        if (d){
                            let bidsNum = d.toNumber();
                            let bids = [];
                            for (let i=0;i<bidsNum;i++){
                                contract.getBid(creator, id, i, function(e,d){
                                    let bid = {};
                                    bid.id = d[0].toNumber();
                                    bid.name = d[1];
                                    bid.amountReceived = d[2].toNumber();
                                    bids.push(bid);
                                    if (bids.length == bidsNum){
                                        resolve(bids);
                                    }
                                })
                            }
                        }
                    })
                })
            },
            getWhoBet: function(bids){
                return new Promise(resolve =>{
                    for (let i = 0;i<bids.length;i++){
                        contract.getWhoBet(dapp.event.creator, dapp.event.id, i, function(e,d){
                            bids[i].whoBet = d;
                            if (i == bids.length-1){
                                resolve(bids);
                            }
                        })
                    }
                })
            },
            getBets: function(creator, eventId){
                return new Promise(resolve =>{
                    contract.getBetsNums(creator, eventId, function(e,d){
                        if (d.toNumber() > 0){
                            let betsNum = d.toNumber();
                            let bets = [];
                            for (let i = 0;i<betsNum;i++){
                                contract.getBet(creator, eventId, i, function(e,d){
                                    let bet = {};
                                    bet.id = i;
                                    bet.person = d[0];
                                    bet.bidName = web3.toAscii(d[1]).replace(/\0/g,'');
                                    bet.amount = d[2].toNumber();
                                    bets.push(bet);
                                    if (bets.length == betsNum){
                                        resolve(bets);
                                    }
                                })
                            }
                        } else {
                            resolve([]);
                        }
                    })
                })
            },
            bet: function(tEvent, bidName, amount){
                let txnData = contract.makeBet.getData(tEvent.creator, tEvent.id, web3.toHex(bidName));
                web3.eth.estimateGas({
                    to: contract_address,
                    data: txnData,
                    value: amount
                }, function(e,d){
                    web3.eth.sendTransaction({
                        to: contract_address,
                        data: txnData,
                        value: amount,
                        gas: d
                    }, function(e,d){
                        console.log('Sent! Txn hash : '+d);
                        if (d){
                            dapp.showModal();
                        }
                    })
                })
            },
            finishEvent: function(creator, id){
                contract.finishEvent(creator, id, function(e,d){
                    if (d){
                        dapp.showModal();
                    }
                })
            },
            determineWinner: function(creator, id, bidName){
                contract.determineWinner(creator, id, bidName, function(e,d){
                    if (d){
                        dapp.showModal();
                    }
                })
            },
            withdraw: function(){
                contract.requestWithdraw(function(e,d){
                    if(d){
                        dapp.watch('balance');
                        dapp.showModal();
                    }
                })
            },
            watch: function(type){
                switch(type){
                    case 'bet':
                        console.log('Watching for bets');
                        let timer = setInterval(function(){
                            dapp.event.bids.forEach(function(item, index, array){
                                let currentAmount = item.amountReceived;
                                contract.getBid(dapp.event.creator, dapp.event.id, item.id, function(e,d){
                                    if(currentAmount != d[2].toNumber()){
                                        item.amountReceived = d[2].toNumber();
                                        $('#received'+item.id).html('Amount received for this bid: <span class="eth_address">'+web3.fromWei(item.amountReceived, 'ether')+' ETH</span>');
                                    }
                                })
                            })
                        },5000);
                        let timerBets = setInterval(function(){
                            let currentBetsNum = dapp.event.bets.length;
                            contract.getBetsNums(dapp.event.creator, dapp.event.id, function(e,d){
                                let newBetsNum = d.toNumber();
                                if (newBetsNum > currentBetsNum){
                                    let bets = [];
                                    for (let i = 0;i<newBetsNum;i++){
                                        contract.getBet(dapp.event.creator, dapp.event.id, i, function(e,d){
                                            let bet = {};
                                            bet.id = i;
                                            bet.person = d[0];
                                            bet.bidName = web3.toAscii(d[1]).replace(/\0/g,'');
                                            bet.amount = d[2].toNumber();
                                            bets.push(bet);
                                            if (bets.length == newBetsNum){
                                                dapp.event.bets = bets;
                                                dapp.renderYourBets(bets);
                                            }
                                        })
                                    }
                                }
                            })
                        }, 5000);
                        break;
                    case 'finish':
                        console.log('Wathing for finish');
                        let finishTimer = setInterval(function(){
                            contract.betEvents(dapp.event.creator, dapp.event.id, function(e,d){
                                if (d[6].toNumber() == 1){
                                    dapp.event.status = 'finished';
                                    dapp.renderEvent();
                                }
                            })
                        }, 5000);
                        break;
                    case 'winner':
                        console.log('Watching for winner');
                        let winnerTimer = setInterval(function(){
                            contract.betEvents(dapp.event.creator, dapp.event.id, function(e,d){
                                if (d[6].toNumber() == 2){
                                    dapp.event.status = 'closed';
                                    dapp.event.winner = web3.toAscii(d[4]).replace(/\0/g,'');
                                    dapp.getBalance().then(balance => {
                                        dapp.event.balance = balance;
                                        dapp.renderEvent();
                                    });
                                }
                            })
                        },5000);
                        break;
                    case 'balance':
                        let currentBalance = dapp.event.balance;
                        let balanceTimer = setInterval(function(){
                            contract.pendingWithdrawals(web3.eth.defaultAccount, function(e,d){
                                if (currentBalance != d.toNumber()){
                                    clearInterval(balanceTimer);
                                    dapp.event.balance = d.toNumber();
                                    dapp.renderEvent();
                                }
                            })
                        }, 5000)
                }
            },
            renderEvent: function(){
                let tEvent = dapp.event;
                managePlaceHolders();
                $(document).unbind('click');
                $('.bids-row').html('');
                $('#creator').html(tEvent.creator);
                $('#arbitrator').html(tEvent.arbitrator);
                $('#fee').html(tEvent.fee);
                $('#status').html(tEvent.status);
                $('#name').html(tEvent.name);
                if (tEvent.status == 'closed' && tEvent.winner){
                    $('#winner').html('Winner: '+tEvent.winner);
                }
                renderBids();
                dapp.renderYourBets(tEvent.bets);
                dapp.renderButtons(tEvent);
                function renderBids(){
                    tEvent.bids.forEach(function(item,i,arr){
                        let bidWrap = $('<div class="col-md-6"></div>');
                        let bidWrapRow = $('<div class="row"></div>');
                        let bidWrapRowCol = $('<div class="col-md-12"></div>');
                        bidWrapRowCol.append('<h3>'+web3.toAscii(item.name)+'</h3>');
                        bidWrapRowCol.append('<p id="received'+item.id+'">Amount received for this bid: <span class="eth_address">'+web3.fromWei(item.amountReceived, 'ether')+' ETH</span></p>');
                        if (tEvent.status == 'open'){
                            bidWrapRowCol.append('<div class="form-group"><button class="btn btn-success btn-small btn-ok bet" id="toggle-bet'+item.id+'">Bet</button></div>');
                            bidWrapRowCol.append('<div id="enter-amount'+item.id+'" class="entr" style="display:none;">'
                                +'<div class="form-group"><input type="number" class="form-control" id="bet-value'+item.id+'" placeholder="Enter value in eth"><br>'
                                +'<button class="btn btn-success btn-ok btn-small bet-ok'+item.id+'" data-id="'+item.id+'">Ok</button></div></div>');
                        } else if (tEvent.status == 'finished' && tEvent.arbitrator == web3.eth.defaultAccount){
                            bidWrapRowCol.append('<div class="form-group"><button class="btn btn-success btn-ok btn-small determine-winner" id="winner'+item.id+'">Winner</button></div>');
                        } else if (tEvent.status == 'closed' && tEvent.winner == web3.toAscii(item.name).replace(/\0/g,'')){
                            bidWrapRowCol.append('<p class="winner-title eth_address">Winner</p>');
                        }
                        bidWrapRow.append(bidWrapRowCol);
                        bidWrap.append(bidWrapRow);
                        $('.bids-row').append(bidWrap);

                        $(document).on('click', '#toggle-bet'+item.id, function(){
                            if($('#enter-amount'+item.id).is(':visible')){
                                $('#enter-amount'+item.id).fadeOut();
                            } else {
                                $('.entr:visible').fadeOut();
                                $('#enter-amount'+item.id).fadeIn();
                            }
                        });
                        $(document).on('click', '.bet-ok'+item.id, function(){
                            let amount = $('#bet-value'+item.id).val();
                            if (amount > 0){
                                amount = web3.toWei(amount, 'ether');
                                $('#enter-amount'+item.id).fadeOut();
                                dapp.bet(tEvent, item.name, amount);
                            }
                        });

                        $(document).on('click', '#winner'+item.id, function(){
                            dapp.determineWinner(tEvent.creator, tEvent.id, item.name);
                        })
                    })
                }
            },
            renderYourBets: function(bets){
                $('.your-bets>.row>.col-md-12').html('');
                if (bets.length){
                    let your_bets = [];
                    bets.forEach(function(item, index, array){
                        if(item.person == web3.eth.defaultAccount){
                            your_bets.push(item);
                        }
                    });
                    if (your_bets.length){
                        $('.nav-block').show();
                        $('.your-bets-btn').show();
                        $('.your-bets-btn').unbind();
                        $('.your-bets-btn').click(function(){
                            $('.balance').hide();
                            if ($('.your-bets').is(':visible')){
                                $('.your-bets').fadeOut();
                            } else {
                                $('.your-bets').fadeIn();
                            }
                        });
                        your_bets.sort(function(a,b){
                            if (a.id < b.id){
                                return -1;
                            } else {
                                return 1;
                            }
                        });
                        let ul = $('<ul></ul>');
                        your_bets.forEach(function(item, i, arr){
                            ul.append('<li><span class="eth_address">'+web3.fromWei(item.amount, 'ether')+' ETH</span> for '+item.bidName+'</li>');
                        });
                        $('.your-bets>.row>.col-md-12').append('<h4>Your Bets:</h4>');
                        $('.your-bets>.row>.col-md-12').append(ul);
                    }
                }
            },
            renderButtons: function(tEvent){
                if (tEvent.arbitrator == web3.eth.defaultAccount && tEvent.status == 'open'){
                    $('.finish-event').show();
                    $('.nav-block').show();
                    $('.finish-event').unbind();
                    $('.finish-event').click(function(){
                        dapp.finishEvent(tEvent.creator, tEvent.id);
                    });
                } else {
                    $('.finish-event').hide();
                    $('.finish-event').unbind();
                }
                if (tEvent.balance > 0){
                    $('.nav-block').show();
                    $('.your-balance').show();
                    $('.your-balance').unbind();
                    $('.your-balance').click(function(){
                        $('.your-bets').hide();
                        if ($('.balance').is(':visible')){
                            $('.balance').fadeOut();
                        } else {
                            $('.balance>.row>.col-md-12').html('');
                            $('.balance>.row>.col-md-12').append('<p>Your balance is: <span class="eth_address">'+web3.fromWei(tEvent.balance, 'ether')+' ETH</span></p><button class="btn btn-success btn-ok withdraw">Withdraw</button>');
                            $('.balance').fadeIn();
                        }
                    });
                    $(document).on('click', '.withdraw', function(){
                        dapp.withdraw();
                    })
                } else {
                    $('.your-balance').hide();
                    $('.balance').hide();
                }
            },
            showModal: function(){
                $('.main').hide();
                $('.modal-txn').show();
                $('.ok-back').unbind();
                $('.ok-back').click(function(){
                    $('.modal-txn').hide();
                    $('.main').show();
                })
            }
        }
    })();

    window.addEventListener('load', function(){
        if (typeof(window.web3.eth.defaultAccount) != 'undefined' && window.web3.eth.defaultAccount) {
            dapp.init('<?php echo $eth_account; ?>', '<?php echo $name; ?>');
        } else {
            var initTimer = setInterval(function(){
                if (typeof(window.web3.eth.defaultAccount) != 'undefined' && window.web3.eth.defaultAccount) {
                    dapp.init('<?php echo $eth_account; ?>', '<?php echo $name; ?>');
                    clearInterval(initTimer);
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
    .eth_address{
        color: <?php echo $interface['eth_addresses_color']; ?>;
        font-size: 14px;
    }
    a, a:focus, a:hover, a:active{
        text-decoration: none;
        color: <?php echo $interface['links_color']; ?>;
    }
    .btn.btn-danger.btn-cancel{
        background-color: <?php echo $interface['cancel_buttons_color']; ?>;
        border-color: <?php echo $interface['cancel_buttons_color']; ?>;
    }
    .btn.btn-success.btn-ok{
        background-color: <?php echo $interface['ok_buttons_color']; ?>;
        border-color: <?php echo $interface['ok_buttons_color']; ?>;
    }
    .btn.btn-danger.btn-cancel:hover, .btn.btn-success.btn-ok:hover{
        opacity: 0.8;
    }
    #name{
        color: <?php echo $interface['headers_color']; ?>;
        margin-top: 10px;
    }
    .btn{
        padding: 8px 38px; text-transform: uppercase; margin:5px;
    }
    .dapp-block, #nav-block{
        padding-top: 10px;
        padding-bottom: 10px;
    }
    .dapp-block, .delimiter{
        border-bottom:1px solid <?php echo $interface['eth_addresses_color']; ?>;
    }
    .dapp-block.your-bets, .dapp-block.balance, .dapp-block.nav-block{
        border-bottom: 0;
    }
    .confirmedBy span{
        display: block;
    }
    .your-bets ul li{
        list-style: none;
    }
    .your-bets-btn{
        display: none;
    }
    .your-bets{
        display: none;
    }
    .your-bets ul{
        padding: 0;
    }
    .finish-event{
        display: none;
    }
    .your-balance{
        display: none;
    }
    .balance{
        display:none;
    }
    .nav-block{
        display: none;
    }
    .modal-txn{
        display: none;
        z-index: 999;
        width: 100%;
        height: 100%;
        position: absolute;
        left: 0;
        top: 0;
        background-color: <?php echo $interface['background_color']; ?>;
    }
    /*.modal-txn p{
        font-size: 20px;
    }*/
</style>
</body>
</html>