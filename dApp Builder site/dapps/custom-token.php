<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/custom-token.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $name; ?></title>
    <script type="text/javascript" src="assets/js/jquery.min.js"></script>
</head>
<body>
<div class="container main text-center">
    <div class="dapp-block" id="info-block">
        <h1 id="name"><?php echo $name; ?></h1>
        <p>Token address: <span class="eth_address" id="creator"><?php echo $address ?></span></p>
        <p>Token symbol: <span class="eth_address" id="symbol"></span></p>
        <p>Token decimals: <span class="eth_address" id="decimals"></span></p>
        <p>Token total supply: <span class="eth_address" id="supply"></span></p>
        <p>Your balance: <span class="eth_address" id="balance"></span></p>
    </div>
    <div class="dapp-block" id="send-block">
        <div class="form-group">
            <label for="token-value">Address:</label>
            <input type="text" class="form-control" id="send-address">
        </div>
        <div class="form-group">
            <label for="token-value">Tokens to send:</label>
            <input type="number" class="form-control" id="token-value">
        </div>
        <button class="btn btn-success btn-ok" id="send-ok">Send</button>
        <button class="btn btn-danger btn-cancel" id="send-close">Close</button>
    </div>
    <div class="dapp-block" id="check-balance-block">
        <div class="form-group">
            <label for="token-value">Check balance of address:</label>
            <input type="text" class="form-control" id="check-address">
        </div>
        <p id="check-balance"></p>
        <button class="btn btn-success btn-ok" id="check-ok">Ok</button>
        <button class="btn btn-danger btn-cancel" id="check-close">Close</button>
    </div>
    <div class="dapp-block" id="approve-block">
        <div class="form-group">
            <label for="token-value">Address:</label>
            <input type="text" class="form-control" id="approve-address">
        </div>
        <div class="form-group">
            <label for="approve-value">Tokens to approve:</label>
            <input type="number" class="form-control" id="approve-value">
        </div>
        <p id="approve-balance"></p>
        <button class="btn btn-success btn-ok" id="approve-ok">Appove</button>
        <button class="btn btn-success btn-ok" id="approve-check">Check current allowance</button>
        <button class="btn btn-danger btn-cancel" id="approve-increase">Increase approval</button>
        <button class="btn btn-danger btn-cancel" id="approve-decrease">Decrease approval</button>
        <button class="btn btn-danger btn-cancel" id="approve-close">Close</button>
    </div>
    <div class="dapp-block" id="transfer-from-block">
        <div class="form-group">
            <label for="transfer-from-address">Owner's address:</label>
            <input type="text" class="form-control" id="transfer-from-address">
        </div>
        <div class="form-group">
            <label for="transfer-from-to">To:</label>
            <input type="text" class="form-control" id="transfer-from-to">
        </div>
        <div class="form-group">
            <label for="transfer-from-value">Tokens to spend:</label>
            <input type="number" class="form-control" id="transfer-from-value">
        </div>
        <p id="transfer-from-balance"></p>
        <button class="btn btn-success btn-ok" id="spend-ok">Spend</button>
        <button class="btn btn-success btn-ok" id="spend-check">Check current allowance</button>
        <button class="btn btn-danger btn-cancel" id="spend-close">Close</button>

    </div>
    <div class="dapp-block nav-block">
        <div class="row">
            <div class="col-md-12">
                <button class="btn btn-danger btn-cancel" id="send">Transfer tokens</button>
            </div>
            <div class="col-md-12">
                <button class="btn btn-danger btn-cancel" id="check">Check balance</button>
            </div>
            <div class="col-md-12">
                <button class="btn btn-danger btn-cancel" id="approve">Approve tokens</button>
            </div>
            <div class="col-md-12">
                <button class="btn btn-danger btn-cancel" id="transfer-from">Transfer from</button>
            </div>
        </div>
    </div>
</div>
<div class="modal-txn container text-center" style="display: none;">
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

        var contract_address = '<?php echo $address; ?>';

        let ABI = <?php echo CUSTOM_TOKEN_ABI; ?>;
        
        let decimals = 0;
        
        let contract;
        
        return {
            event: {},
            init: function(){
                contract = web3.eth.contract(ABI).at(contract_address);
                managePlaceHolders();
                this.getDecimals();
                this.getSymbol();
                this.triggers();
                this.watchBalance();
            },
            getDecimals: function(){
                contract.decimals(function(e,d){
                    decimals = d.toNumber();
                    $('#decimals').html(d.toNumber());
                    dapp.getTotalSupply(d.toNumber());
                    dapp.getBalance(d.toNumber());
                })
            },
            getSymbol: function(){
                contract.symbol(function(e,d){
                    $('#symbol').html(d);
                })
            },
            getTotalSupply: function(decimals){
                contract.totalSupply(function(e,d){
                    $('#supply').html(d.toNumber()/(10**decimals));
                })
            },
            getBalance: function(decimals){
                contract.balanceOf(web3.eth.defaultAccount, function(e,d){
                    if (d){
                        let balance = d.toNumber()/(10**decimals);
                        $('.nav-block').show();
                        if (balance > 0){
                            $('#send').show();
                            $('#balance').html(balance.toPrecision(decimals));
                        } else {
                            $('#send').hide();
                            $('#balance').html(balance);
                        }
                    }
                })
            },
            watchBalance: function(){
                console.log('Watching balance');
                setInterval(function(){
                    let currentBalance = +$('#balance').html();
                    contract.balanceOf(web3.eth.defaultAccount, function(e,d){
                        if (d){
                            let cbalance = d.toNumber()/(10**decimals);
                            if (cbalance != currentBalance){
                                console.log('balance changed');
                                console.log(cbalance);
                                $('#balance').html(cbalance);
                            }
                            if (cbalance > 0){
                                $('#send').show();
                            } else {
                                $('#send').hide();
                            }
                        }
                    })
                }, 3000)
            },
            getBalanceOf: function(address){
                $('#check-balance').html('');
                contract.balanceOf(address, function(e,d){
                    let balance = d.toNumber()/(10**decimals);
                    $('#check-balance').show();
                    $('#check-balance').html(balance+' tokens');
                })
            },
            triggers: function(){
                $('#send').click(function(){
                    $('.nav-block').hide();
                    $('#send-block').show();
                });
                $('#send-close').click(function(){
                    $('#send-block').hide();
                    $('.nav-block').show();
                });
                $('#send-ok').click(function(){
                    let value = $('#token-value').val();
                    value = +value;
                    let to = $('#send-address').val();
                    if (value > 0){
                        if ((value^0) === value){
                            value = value*(10**decimals);
                        } else {
                            let count = 0;
                            let val = value*10;
                            while (!(val^0) === val){
                                count++;
                                val = val*10;
                            }
                            value = value*(10**(decimals-count));
                        }
                        contract.transfer(to, value, function(e,d){
                            if (d){
                                dapp.showModal();
                            }
                        })
                    }
                });
                $('.modal-txn .btn-ok').click(function(){
                    $('.modal-txn').hide();
                    $('#info-block').show();
                    $('.nav-block').show();
                });
                $('#check').click(function(){
                    $('#check-balance-block').show();
                    $('.nav-block').hide();
                });
                $('#check-ok').click(function(){
                    let address = $('#check-address').val();
                    if (web3.isAddress(address)){
                        dapp.getBalanceOf(address);
                    } else {
                        $('#check-balance').show();
                        $('#check-balance').html('Please enter valid ETH address');
                    }
                });
                $('#check-close').click(function(){
                    $('#check-balance-block').hide();
                    $('.nav-block').show();
                });
                $('#approve').click(function(){
                    $('#info-block').hide();
                    $('#approve-block').show();
                    $('.nav-block').hide();
                });
                $('#approve-ok').click(function(){
                    let address = $('#approve-address').val();
                    let value = $('#approve-value').val();
                    if (web3.isAddress(address)){
                        if (value > 0){
                            if ((value^0) === value){
                                value = value*(10**decimals);
                            } else {
                                let count = 0;
                                let val = value*10;
                                while (!(val^0) === val){
                                    count++;
                                    val = val*10;
                                }
                                value = value*(10**(decimals-count));
                            }
                            contract.approve(address, value, function(e,d){
                                if (d){
                                    dapp.showModal();
                                }
                            })
                        } else {
                            $('#approve-balance').show();
                            $('#approve-balance').html('Value should be greater than 0');
                        }
                    } else {
                        $('#approve-balance').show();
                        $('#approve-balance').html('Please enter valid ETH address');
                    }
                });
                $('#approve-check').click(function(){
                    let address = $('#approve-address').val();
                    if (web3.isAddress(address)){
                        contract.allowance(web3.eth.defaultAccount, address, function(e,d){
                            let allowance = d.toNumber()/(10**decimals);
                            $('#approve-balance').show();
                            $('#approve-balance').html('Allowance is: '+allowance+' tokens');
                        })
                    } else {
                        $('#approve-balance').show();
                        $('#approve-balance').html('Please enter valid ETH address');
                    }
                });
                $('#approve-close').click(function(){
                    $('#info-block').show();
                    $('#approve-block').hide();
                    $('.nav-block').show();
                });
                $('#approve-increase').click(function(){
                    let address = $('#approve-address').val();
                    let value = $('#approve-value').val();
                    if (web3.isAddress(address)){
                        if (value > 0){
                            if ((value^0) === value){
                                value = value*(10**decimals);
                            } else {
                                let count = 0;
                                let val = value*10;
                                while (!(val^0) === val){
                                    count++;
                                    val = val*10;
                                }
                                value = value*(10**(decimals-count));
                            }
                            contract.increaseApproval(address, value, function(e,d){
                                if (d){
                                    dapp.showModal();
                                }
                            })
                        } else {
                            $('#approve-balance').show();
                            $('#approve-balance').html('Value should be greater than 0');
                        }
                    } else {
                        $('#approve-balance').show();
                        $('#approve-balance').html('Please enter valid ETH address');
                    }
                });
                $('#approve-decrease').click(function(){
                    let address = $('#approve-address').val();
                    let value = $('#approve-value').val();
                    if (web3.isAddress(address)){
                        if (value > 0){
                            if ((value^0) === value){
                                value = value*(10**decimals);
                            } else {
                                let count = 0;
                                let val = value*10;
                                while (!(val^0) === val){
                                    count++;
                                    val = val*10;
                                }
                                value = value*(10**(decimals-count));
                            }
                            contract.decreaseApproval(address, value, function(e,d){
                                if (d){
                                    dapp.showModal();
                                }
                            })
                        } else {
                            $('#approve-balance').show();
                            $('#approve-balance').html('Value should be greater than 0');
                        }
                    } else {
                        $('#approve-balance').show();
                        $('#approve-balance').html('Please enter valid ETH address');
                    }
                });
                $('#transfer-from').click(function(){
                    $('#transfer-from-block').show();
                    $('.nav-block').hide();
                    $('#info-block').hide();
                });
                $('#spend-close').click(function(){
                    $('#transfer-from-block').hide();
                    $('#info-block').show();
                    $('.nav-block').show();
                });
                $('#spend-check').click(function(){
                    let address = $('#transfer-from-address').val();
                    if (web3.isAddress(address)){
                        contract.allowance(web3.eth.defaultAccount, address, function(e,d){
                            let allowance = d.toNumber()/(10**decimals);
                            $('#transfer-from-balance').html('Current allowance is '+allowance+' tokens');
                        })
                    } else {
                        $('#transfer-from-balance').show().html('Please enter valid ETH address');
                    }
                });
                $('#spend-ok').click(function(){
                    let address = $('#transfer-from-address').val();
                    let value = $('#transfer-from-value').val();
                    let to = $('#transfer-from-to').val();
                    if (web3.isAddress(address) && web3.isAddress(to)){
                        if (value > 0){
                            if ((value^0) === value){
                                value = value*(10**decimals);
                            } else {
                                let count = 0;
                                let val = value*10;
                                while (!(val^0) === val){
                                    count++;
                                    val = val*10;
                                }
                                value = value*(10**(decimals-count));
                            }
                            contract.transferFrom(address, to, value, function(e,d){
                                if (d){
                                    dapp.showModal();
                                }
                            })
                        } else {
                            $('#transfer-from-balance').show();
                            $('#transfer-from-balance').html('Value should be greater than 0');
                        }
                    } else {
                        $('#transfer-from-balance').show();
                        $('#transfer-from-balance').html('Please enter valid ETH address');
                    }
                })
            },
            showModal: function(){
                $('.modal-txn').show();
                $('#send-block').hide();
                $('#info-block').hide();
                $('#approve-block').hide();
            }
        }
    })();

    window.addEventListener('load', function(){
        if (typeof(window.web3.eth.defaultAccount) != 'undefined' && window.web3.eth.defaultAccount) {
            dapp.init();
        } else {
            var initTimer = setInterval(function(){
                if (typeof(window.web3.eth.defaultAccount) != 'undefined' && window.web3.eth.defaultAccount) {
                    dapp.init();
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
    .nav-block{
        display: none;
    }
    #send-block{
        display: none;
    }
    #check-balance-block{
        display: none;
    }
    #check-balance{
        display: none;
    }
    #approve-block{
        display: none;
    }
    #transfer-from-block{
        display: none;
    }
</style>
</body>
</html>