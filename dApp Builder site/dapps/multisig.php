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
    <div class="dapp-block" id="info-block">
        <h1 id="name"></h1>
        <h4>Balance: <span class="eth_address" id="balance"></span> ETH</h4>
        <h4>Needed approvals for transactions: <span class="eth_address" id="approvals"></span></h4>
        <h4>Wallet's owners:</h4>
        <div id="owners">
        </div>
    </div>
    <div id="unsent-wrapper">
        <div style="display:none;padding-bottom:0;border-bottom:0;" class="dapp-block" id="unsent-block">
            <h4 id="unsent-count"></h4>
            <div class="row"></div>
        </div>
    </div>
    <div style="display:none;padding-bottom:0;border-bottom:0;" class="dapp-block" id="sent-block">
        <h4 id="sent-count"></h4>
        <div class="row"></div>
    </div>
    <div style="display:none;padding-bottom:0;" class="dapp-block" id="new-tx-block">
        <form id="create-tx-form">
            <div class="form-group">
                <label for="tx-address">Destination address:</label>
                <input required type="text" class="form-control" id="tx-address" placeholder="0x0000000000000000000000000000000000000000">
            </div>
            <div class="form-group">
                <label for="tx-value">Value (ETH):</label>
                <input type="number" min="0" step="0.00001" value="0" class="form-control" id="tx-value">
            </div>
            <div class="form-group">
                <label for="tx-data">Data (will be converted to HEX):</label>
                <input type="text" class="form-control" id="tx-data">
            </div>
            <button style="display:none;" id="create-tx-form-submit" type="submit"></button>
        </form>
    </div>
    <div style="display:none;padding-bottom:0;" class="dapp-block" id="send-eth-block">
        <form id="send-eth-form" class="form-group">
            <label for="send-value">Value (ETH):</label>
            <input required type="number" min="0" step="0.00001" value="0" class="form-control" id="send-value">
            <button style="display:none;" id="send-eth-form-submit" type="submit"></button>
        </form>
        <p><a id="showlogs" data-toggle="collapse" aria-expanded="false" aria-controls="logs" href="#logs" style="display: none;">Show logs</a></p>
        <div class="collapse" id="logs" style="padding-bottom:10px;"></div>
    </div>
    <div id="nav-block">
        <button style="display:none;" type="button" class="btn btn-danger btn-cancel" id="show-sent-button">Show sent transactions</button>
        <button style="display:none;" type="button" class="btn btn-danger btn-cancel" id="back-button">Back</button>
        <button style="display:none;" type="button" class="btn btn-success btn-ok" id="new-tx-button">New transaction</button>
        <button style="display:none;" type="button" onclick="$('#create-tx-form-submit').click();" class="btn btn-success btn-ok" id="create-tx-button">Create</button>
        <button type="button" class="btn btn-danger btn-cancel" id="top-up-button">Send ETH to wallet</button>
        <button style="display:none;" type="button" onclick="$('#send-eth-form-submit').click();" class="btn btn-success btn-ok" id="send-eth-button">Send</button>
    </div>
</div>
    
<?php require_once __DIR__ . '/../common/dapp-placeholder.php'; ?>
    
<div class="modal" tabindex="-1" role="dialog" id="txn-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="color: black">
            <div class="modal-header">
                <h5 class="modal-title">Transaction sent</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Transaction has been submitted. Please wait until it got mined.</p>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
<script type="text/javascript">
    var dapp = (function(){
        
        <?php if ($network == 'main') { ?>
            var contract_address = '<?php echo MULTISIG_MAIN_ADDRESS; ?>';
        <?php } elseif ($network == 'rinkeby') { ?>
            var contract_address = '<?php echo MULTISIG_RINKEBY_ADDRESS; ?>';
        <?php } ?>
        
        var ABI = <?php echo MULTISIG_ABI; ?>;
    
	return {
            init: function (creator, name) {
                window.contract = web3.eth.contract(ABI).at(contract_address);
                console.log('Contract address is: '+contract_address);
                var twallet = {};
                this.getId(creator, name)
                    .then(data => {
                        twallet.creator = creator;
                        if (data[1] == true){
                            twallet.id = data[0].toNumber();
                            return this.getOwners(creator, data[0].toNumber());
                        }
                    })
                    .then(owners => {
                        if (owners != undefined){
                            twallet.owners = owners;
                            return this.getWalletInfo(twallet.creator, twallet.id);
                        } else {
                            return null;
                        }
                    })
                    .then(walletInfo => {
                        if (walletInfo){
                            twallet.name = web3.toAscii(walletInfo[0]).replace(/\0/g,'');
                            twallet.balance = web3.fromWei(walletInfo[3].toNumber(), 'ether');
                            twallet.approvals = walletInfo[4].toNumber();
                            return twallet;
                        }
                    })
                    .then(wallet => {
                        console.log('Getting transactions');
                        return this.getTransactions(wallet);
                    })
                    .then(wallet => {
                        console.log('Getting logs num');
                        return this.getLogsNum(wallet);
                    })
                    .then(wallet => {
                        console.log('Getting logs');
                        return this.getLogs(wallet);
                    })
                    .then(wallet => {
                        console.log('Rendering wallet');
                        if (wallet.hasOwnProperty('balance')){
                            this.renderWallet(wallet);
                            this.wallet = wallet;
                        }
                    })
                    .then(d => {
                        this.watchBalance(this.wallet);
                    })
            },
            getId: function(creator, name){
                return new Promise(resolve => {
                    contract.getWalletId(creator, name, function(e,d){
                        resolve(d);
                    })
                })
            },
            getOwners: function(creator, id){
                return new Promise(resolve => {
                    contract.getOwners(creator, id, function(e,d){
                        resolve(d);
                    })
                })
            },
            getWalletInfo: function(creator,id){
                return new Promise(resolve => {
                    contract.wallets(creator, id, function(e,d){
                        resolve(d);
                    })
                })
            },
            getTransactions: function(wallet){
                return new Promise(resolve => {
                    contract.getTxnNum(wallet.creator, wallet.id, function(e,d){
                        if (d){
                            var txns = d.toNumber();
                            if (txns > 0){
                                wallet.transactions = [];
                                for(var i = 0;i<txns;i++){
                                    contract.getTxn(wallet.creator, wallet.id, i, function(e,d){
                                        wallet.transactions.push(d);
                                        if (wallet.transactions.length == txns){
                                            resolve(wallet);
                                        }
                                    })
                                }
                            } else {
                                resolve(wallet);
                            }
                        }
                    })
                })
            },
            submitTransaction: function(txnData){
                console.log(txnData);
                contract.submitTransaction(txnData.walletCreator, txnData.destination, txnData.walletId, txnData.value, txnData.data, function(e,d){
                    if (d){
                        $("#back-button").trigger('click');
                        $('#txn-modal').modal({
                            keyboard: false,
                            show: true
                        })
                    }
                })
            },
            sendEth: function(creator, walletId, value){
                var transactionData = contract.topBalance.getData(creator, walletId);
                web3.eth.estimateGas({
                    to: contract_address,
                    data: transactionData,
                    value: value
                }, function(e,d){
                    web3.eth.sendTransaction({
                        to: contract_address,
                        data: transactionData,
                        value: value,
                        gas: d
                    }, function(e,d){
                        if (d){
                            console.log('Sent! Hash is: '+d);
                            $("#back-button").trigger('click');
                            $('#txn-modal').modal({
                                keyboard: false,
                                show: true
                            })
                        }
                    })
                })
            },
            getLogsNum: function(wallet){
                return new Promise(resolve => {
                    contract.getLogsNum(wallet.creator, wallet.id, function(e,d){
                        wallet.logsNum = d.toNumber();
                        resolve(wallet);
                    })
                })
            },
            getLogs: function(wallet){
                return new Promise(resolve => {
                    var logs = [];
                    var logsNum = wallet.logsNum;
                    (function recevoirProchain(logsNum,currentId){
                        if (logsNum > 0){
                            contract.getLog(wallet.creator, wallet.id, currentId, function(e,d){
                                logs.push(d);
                                if (currentId == logsNum-1){
                                    wallet.logs = logs;
                                    resolve(wallet);
                                } else {
                                    recevoirProchain(logsNum, currentId+1);
                                }
                            })
                        } else {
                            wallet.logs = [];
                            resolve(wallet);
                        }
                    })(logsNum, 0);
                })
            },
            confirmTransaction: function(creator, id, txnId){
                contract.confirmTransaction(creator,id,txnId, function(e,d){
                    if (d){
                        console.log('Sent! Tx hash is: '+d);
                        $('#txn-modal').modal({
                            keyboard: false,
                            show: true
                        })
                    }
                })
            },
            executeTxn: function(creator, id, txnId){
                contract.executeTxn(creator, id, txnId, function(e,d){
                    if (d){
                        $('#txn-modal').modal({
                            keyboard: false,
                            show: true
                        })
                    }
                })
            },
            watchBalance: function(wallet){
                var currentBalance = wallet.balance;
                console.log('Watching balance. Current balance is: '+currentBalance);
                setInterval(function(){
                    contract.wallets(wallet.creator, wallet.id, function(e,d){
                        var balance = web3.fromWei(d[3].toNumber(),'ether');
                        if (balance != currentBalance){
                            currentBalance = balance;
                            $(document).trigger('balanceChanged', {newBalance : balance});
                        }
                    })
                }, 3000)
            },
            renderWallet: function(wallet){
                var is_owner = false;
                var unsent_count = 0;
                var sent_count = 0;
                var that = this;
                
                if (wallet.hasOwnProperty('transactions')){
                    renderTransactions();
                }

                renderLogs();

                $(document).on('balanceChanged', async function(e){
                    wallet.logsNum += 1;
                    that.getLogs(wallet).then(updatedWallet => {
                        wallet = updatedWallet;
                        renderLogs();
                    })
                });

                if (sent_count) {
                    $("#show-sent-button").show();
                }
                if (unsent_count) {
                    $("#unsent-block").show();
                }
                
                $('#name').text(wallet.name);
                $('#balance').text(wallet.balance);
                $('#approvals').text(wallet.approvals);
                for (var i = 0; i < wallet.owners.length; i++) {
                    $("#owners").append('<p class="eth_address">' + wallet.owners[i] + '</p>');
                    if (wallet.owners[i].toLowerCase() == web3.eth.defaultAccount.toLowerCase()) is_owner = true;
                }
                if (is_owner) {
                    $("#owners").append('<p>You are the one of the wallet\'s owners</p>');
                    $("#new-tx-button").show();
                } else {
                    $("#owners").append('<p>You are not the wallet\'s owner</p>');
                }

                $("#show-sent-button").click(function(){
                    $("#show-sent-button").hide();
                    $("#new-tx-button").hide();
                    $("#info-block").hide();
                    $("#unsent-block").hide();
                    $("#unsent-wrapper").hide();
                    if (sent_count) $("#sent-block").show();
                    $("#back-button").show();
                    $("#top-up-button").hide();
                });
                $("#back-button").click(function(){
                    if (sent_count) $("#show-sent-button").show();
                    if (is_owner) $("#new-tx-button").show();
                    $("#info-block").show();
                    if (unsent_count) $("#unsent-block").show();
                    $("#unsent-wrapper").show();
                    $("#sent-block").hide();
                    $("#back-button").hide();
                    $("#new-tx-block").hide();
                    $("#create-tx-button").hide();
                    $("#send-eth-block").hide();
                    $("#send-eth-button").hide();
                    $("#top-up-button").show();
                });
                $("#new-tx-button").click(function(){
                    $("#unsent-wrapper").hide();
                    $("#show-sent-button").hide();
                    $("#new-tx-button").hide();
                    $("#info-block").hide();
                    $("#unsent-block").hide();
                    $("#new-tx-block").show();
                    $("#back-button").show();
                    $("#create-tx-button").show();
                    $("#top-up-button").hide();
                });
                $("#top-up-button").click(function(){
                    $("#unsent-wrapper").hide();
                    $("#show-sent-button").hide();
                    $("#new-tx-button").hide();
                    $("#info-block").hide();
                    $("#unsent-block").hide();
                    $("#send-eth-block").show();
                    $("#back-button").show();
                    $("#send-eth-button").show();
                    $("#top-up-button").hide();
                });
                $("#create-tx-form").submit(function(e){
                    e.preventDefault();
                    var txnData = {};
                    txnData.destination = $('#tx-address').val();
                    txnData.value = web3.toWei($('#tx-value').val(),'ether');
                    txnData.data = web3.toHex($('#tx-data').val());
                    txnData.walletCreator = wallet.creator;
                    txnData.walletId = wallet.id;
                    that.submitTransaction(txnData);
                    return false;
                });
                $("#send-eth-form").submit(function(e){
                    e.preventDefault();
                    var value = web3.toWei($('#send-value').val(), 'ether');
                    that.sendEth(wallet.creator, wallet.id, value);
                    return false;
                });
                var logs_shown = false;
                $('#showlogs').click(function(){
                    if (logs_shown) {
                        $('#showlogs').text("Show logs");
                        logs_shown = false;
                    } else {
                        $('#showlogs').text("Hide logs");
                        logs_shown = true;
                    }
                });
                
                managePlaceHolders();
                
                setInterval(function(){
                    contract.getTxnNum(wallet.creator, wallet.id, function(e,d){
                        if (d){
                            var txns = d.toNumber();
                            if (txns > 0){
                                wallet.transactions = [];
                                for(var i = 0;i<txns;i++){
                                    contract.getTxn(wallet.creator, wallet.id, i, function(e,d){
                                        wallet.transactions.push(d);
                                        if (wallet.transactions.length == txns){
                                            renderTransactions();
                                            return;
                                        }
                                    })
                                }
                            }
                        }
                    })
                }, 3000);

                function renderLogs(){
                    if (wallet.logs.length){
                        $('#showlogs').show();
                        $('#logs').html('');
                        for (var i = 0;i<wallet.logs.length;i++){
                            var logWrap = $('<div class="log"></div>');
                            logWrap.append('<span>Sender: <span class="eth_address">'+wallet.logs[i][0]+'</span></span>');
                            logWrap.append('<span>Value: <span class="eth_address">'+web3.fromWei(wallet.logs[i][1].toNumber(), 'ether')+'</span> ETH</span>');
                            $('#logs').append(logWrap);
                        }
                    };
                }
                function renderTransactions(){
                    sent_count = 0;
                    unsent_count = 0;
                    $("#sent-block .row").html('');
                    $("#unsent-block .row").html('');
                    $('#unsent-block').hide();
                    wallet.transactions.sort(function(a,b){
                        if (a[0].toNumber() < b[0].toNumber()){
                            return -1;
                        } else {
                            return 1;
                        }
                    });
                    wallet.transactions.forEach(function(val,index,arr){
                        var tx = {};
                        tx.id = val[0].toNumber();
                        tx.destination = val[1];
                        tx.value = web3.fromWei(val[2], 'ether');
                        tx.data = val[3];
                        tx.confirmed = val[5];
                        tx.creator = val[6];
                        switch (val[4].toNumber()){
                            case 0:
                                tx.status = 'Unconfirmed';
                                break;
                            case 1:
                                tx.status = 'Pending';
                                break;
                            case 2:
                                tx.status = 'Executed';
                                break;
                        }

                        if (val[4].toNumber() == 2) {
                            sent_count++;
                            var container = $("#sent-block .row");
                        } else {
                            unsent_count++;
                            var container = $("#unsent-block .row");
                        }
                        
                        var wrap = $('<div class="transaction text-left col-md-6"></div>');
                        wrap.append('<div>Transaction id: <span class="eth_address tx-id">'+tx.id+'</span></div>');
                        wrap.append('<div>Destination: <span class="eth_address">'+tx.destination+'</span></div>');
                        wrap.append('<div>Value: <span class="eth_address">'+tx.value+'</span> ETH</div>');
                        wrap.append('<div>Data: <span class="eth_address">'+tx.data+'</span></div>');
                        var confWrap = $('<div class="confirmedBy">Confirmed by:<div>');
                        tx.confirmed.forEach(function(val,index,arr){
                            confWrap.append('<span class="eth_address">'+val+'</span>');
                        });
                        wrap.append(confWrap);
                        wrap.append('<div>Creator: <span class="eth_address">'+tx.creator+'</span></div>');
                        wrap.append('<div>Status: <span class="eth_address">'+tx.status+'</span></div>');
                        if (is_owner && tx.status == 'Unconfirmed' && tx.confirmed.indexOf(web3.eth.defaultAccount) == -1){
                            wrap.append('<button class="btn btn-success btn-ok confirm-txn">Confirm</button>');
                        }
                        if (is_owner && tx.status == 'Pending'){
                            wrap.append('<button class="btn btn-success btn-ok execute-txn">Execute</button>');
                        }
                        wrap.append('<div class="delimiter"></div>');
                        $(container).append(wrap);
                    });
                    if (sent_count) {
                        if (sent_count == 1) {
                            $("#sent-count").html('There is <span class="eth_address">1</span> sent transaction:');
                        } else {
                            $("#sent-count").html('There are <span class="eth_address">'+sent_count+'</span> sent transactions:');
                        }
                        if ($('#info-block').is(':visible')){
                            $("#show-sent-button").show();
                        }
                    }
                    if (unsent_count) {
                        $('#unsent-block').show();
                        if (unsent_count == 1) {
                            $("#unsent-count").html('There is <span class="eth_address">1</span> unsent transaction:');
                        } else {
                            $("#unsent-count").html('There are <span class="eth_address">'+unsent_count+'</span> unsent transactions:');
                        }
                    }
                }

                /* confirm click handler */
                $('body').on('click','.confirm-txn',function(e){
                    var txnId = $(e.target).parent().find('.tx-id').html();
                    that.confirmTransaction(wallet.creator, wallet.id, txnId);
                });

                /* execute txn click handler */
                $('body').on('click', '.execute-txn', function(e){
                    var txnId = $(e.target).parent().find('.tx-id').html();
                    that.executeTxn(wallet.creator, wallet.id, txnId);
                });

                /* change balance listener */
                $(document).on('balanceChanged', function(e, data){
                    $('#balance').html(data.newBalance);
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
    .transaction{
        padding-top: 10px;
    }
    .delimiter{
        padding-top:10px;
    }
    .close{
        position: absolute;
        top: 15px;
        right: 15px;
    }
    .log>span{
        display: block;
    }
    #showlogs{
        cursor: pointer;
        text-decoration-style: dashed;
        text-decoration-line: underline;
        color: <?php echo $interface['links_color']; ?>;
    }
    .confirmedBy span{
        display: block;
    }
</style>
</body>
</html>