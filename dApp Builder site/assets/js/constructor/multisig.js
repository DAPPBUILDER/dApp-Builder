var multisigDapp = (function(){
    var multisigContractAddress = '0xBb87F856b7dbD307Ba98347a9eC9bF6bA97712DF';
    var multisigContractABI = [
        {
            "constant": true,
            "inputs": [
                {
                    "name": "",
                    "type": "address"
                },
                {
                    "name": "",
                    "type": "uint256"
                }
            ],
            "name": "wallets",
            "outputs": [
                {
                    "name": "name",
                    "type": "bytes32"
                },
                {
                    "name": "creator",
                    "type": "address"
                },
                {
                    "name": "id",
                    "type": "uint256"
                },
                {
                    "name": "allowance",
                    "type": "uint256"
                },
                {
                    "name": "appovalsreq",
                    "type": "uint256"
                }
            ],
            "payable": false,
            "stateMutability": "view",
            "type": "function"
        },
        {
            "constant": true,
            "inputs": [
                {
                    "name": "creator",
                    "type": "address"
                },
                {
                    "name": "id",
                    "type": "uint256"
                },
                {
                    "name": "logId",
                    "type": "uint256"
                }
            ],
            "name": "getLog",
            "outputs": [
                {
                    "name": "",
                    "type": "address"
                },
                {
                    "name": "",
                    "type": "uint256"
                }
            ],
            "payable": false,
            "stateMutability": "view",
            "type": "function"
        },
        {
            "constant": true,
            "inputs": [
                {
                    "name": "creator",
                    "type": "address"
                },
                {
                    "name": "id",
                    "type": "uint256"
                }
            ],
            "name": "getOwners",
            "outputs": [
                {
                    "name": "",
                    "type": "address[]"
                }
            ],
            "payable": false,
            "stateMutability": "view",
            "type": "function"
        },
        {
            "constant": true,
            "inputs": [
                {
                    "name": "creator",
                    "type": "address"
                },
                {
                    "name": "name",
                    "type": "bytes32"
                }
            ],
            "name": "getWalletId",
            "outputs": [
                {
                    "name": "",
                    "type": "uint256"
                },
                {
                    "name": "",
                    "type": "bool"
                }
            ],
            "payable": false,
            "stateMutability": "view",
            "type": "function"
        },
        {
            "constant": true,
            "inputs": [
                {
                    "name": "creator",
                    "type": "address"
                },
                {
                    "name": "id",
                    "type": "uint256"
                }
            ],
            "name": "getTxnNum",
            "outputs": [
                {
                    "name": "",
                    "type": "uint256"
                }
            ],
            "payable": false,
            "stateMutability": "view",
            "type": "function"
        },
        {
            "constant": true,
            "inputs": [
                {
                    "name": "creator",
                    "type": "address"
                },
                {
                    "name": "walletId",
                    "type": "uint256"
                },
                {
                    "name": "id",
                    "type": "uint256"
                }
            ],
            "name": "getTxn",
            "outputs": [
                {
                    "name": "",
                    "type": "uint256"
                },
                {
                    "name": "",
                    "type": "address"
                },
                {
                    "name": "",
                    "type": "uint256"
                },
                {
                    "name": "",
                    "type": "bytes"
                },
                {
                    "name": "",
                    "type": "uint8"
                },
                {
                    "name": "",
                    "type": "address[]"
                },
                {
                    "name": "",
                    "type": "address"
                }
            ],
            "payable": false,
            "stateMutability": "view",
            "type": "function"
        },
        {
            "constant": true,
            "inputs": [
                {
                    "name": "creator",
                    "type": "address"
                },
                {
                    "name": "id",
                    "type": "uint256"
                }
            ],
            "name": "getLogsNum",
            "outputs": [
                {
                    "name": "",
                    "type": "uint256"
                }
            ],
            "payable": false,
            "stateMutability": "view",
            "type": "function"
        },
        {
            "anonymous": false,
            "inputs": [
                {
                    "indexed": false,
                    "name": "id",
                    "type": "uint256"
                }
            ],
            "name": "TxnConfirmed",
            "type": "event"
        },
        {
            "anonymous": false,
            "inputs": [
                {
                    "indexed": false,
                    "name": "id",
                    "type": "uint256"
                }
            ],
            "name": "TxnSumbitted",
            "type": "event"
        },
        {
            "anonymous": false,
            "inputs": [
                {
                    "indexed": false,
                    "name": "id",
                    "type": "uint256"
                }
            ],
            "name": "WalletCreated",
            "type": "event"
        },
        {
            "constant": false,
            "inputs": [
                {
                    "name": "creator",
                    "type": "address"
                },
                {
                    "name": "walletId",
                    "type": "uint256"
                },
                {
                    "name": "txId",
                    "type": "uint256"
                }
            ],
            "name": "confirmTransaction",
            "outputs": [
                {
                    "name": "",
                    "type": "bool"
                }
            ],
            "payable": false,
            "stateMutability": "nonpayable",
            "type": "function"
        },
        {
            "constant": false,
            "inputs": [
                {
                    "name": "approvals",
                    "type": "uint256"
                },
                {
                    "name": "owners",
                    "type": "address[]"
                },
                {
                    "name": "name",
                    "type": "bytes32"
                }
            ],
            "name": "createWallet",
            "outputs": [],
            "payable": true,
            "stateMutability": "payable",
            "type": "function"
        },
        {
            "constant": false,
            "inputs": [
                {
                    "name": "creator",
                    "type": "address"
                },
                {
                    "name": "walletId",
                    "type": "uint256"
                },
                {
                    "name": "txId",
                    "type": "uint256"
                }
            ],
            "name": "executeTxn",
            "outputs": [
                {
                    "name": "",
                    "type": "bool"
                }
            ],
            "payable": false,
            "stateMutability": "nonpayable",
            "type": "function"
        },
        {
            "inputs": [],
            "payable": false,
            "stateMutability": "nonpayable",
            "type": "constructor"
        },
        {
            "constant": false,
            "inputs": [
                {
                    "name": "creator",
                    "type": "address"
                },
                {
                    "name": "id",
                    "type": "uint256"
                }
            ],
            "name": "topBalance",
            "outputs": [],
            "payable": true,
            "stateMutability": "payable",
            "type": "function"
        },
        {
            "anonymous": false,
            "inputs": [
                {
                    "indexed": false,
                    "name": "value",
                    "type": "uint256"
                }
            ],
            "name": "topUpBalance",
            "type": "event"
        },
        {
            "constant": false,
            "inputs": [
                {
                    "name": "creator",
                    "type": "address"
                },
                {
                    "name": "destination",
                    "type": "address"
                },
                {
                    "name": "walletId",
                    "type": "uint256"
                },
                {
                    "name": "value",
                    "type": "uint256"
                },
                {
                    "name": "data",
                    "type": "bytes"
                }
            ],
            "name": "submitTransaction",
            "outputs": [
                {
                    "name": "",
                    "type": "bool"
                }
            ],
            "payable": false,
            "stateMutability": "nonpayable",
            "type": "function"
        }
    ];
    
    return {
        init: function(){
            window.multisigContract = web3.eth.contract(multisigContractABI).at(multisigContractAddress);
            this.triggers();
        },
        create: function(approvals, owners, name, value){
            pure_name = name;
            name = web3.toHex(name);
            type = "multisig";
            console.log(name);
            let transactionData = multisigContract.createWallet.getData(approvals, owners, name);
            web3.eth.estimateGas({
                to: multisigContractAddress,
                data: transactionData,
                value: web3.toWei(value, 'ether')
            }, function(e,d){
                web3.eth.sendTransaction({
                    to: multisigContractAddress,
                    data: transactionData,
                    gas: d,
                    value: web3.toWei(value, 'ether')
                }, function(e,d){
                    if (!e && d) {
                        addApp(type, pure_name);
                    }
                })
            })
        },
        triggers: function(){
            var that = this;
            $('#multisig-first-owner').val(web3.eth.defaultAccount);
            $('#multisig-add-owner').click(function(){
                $('#multisig-owners').append(
                    '<div style="display:none;" class="input-group creation-form">' +
                        '<span class="input-group-addon">Owner:</span>' +
                        '<input required placeholder="0x0000000000000000000000000000000000000000" type="text" class="form-control required-multisig required-dapp">' +
                        '<span class="input-group-btn">' +
                            '<button class="btn btn-danger btn-remove" type="button"><i class="fa fa-fw fa-times"></i></button>' +
                        '</span>' +
                    '</div>'
                );
                $('#multisig-owners>div').fadeIn();
                return false;
            });
            $("#multisig-owners").on('click', '.btn-remove', function(){
                $(this).parents(".creation-form").fadeOut(function(){$(this).remove()});
            });
            $('#create-multisig-dapp').click(function(){
                var name = $('#dapp-name').val();
                var value = parseFloat($('#multisig-balance').val());
                var approvals = parseInt($("#multisig-approvals").val());
                var addresses = $('#multisig-owners input');
                var owners = [];
                addresses.each(function(){
                    let address = $(this).val();
                    if (address){
                        owners.push(address);
                    }
                });
                console.log(owners);
                that.create(approvals, owners, name, value);
            });
            if (!$.isEmptyObject(window.multisigUndeployed)) {
                setInterval(function(){
                    for (var key in window.multisigUndeployed) {
                        multisigDapp.checkDeployed(window.multisigUndeployed[key].address, window.multisigUndeployed[key].name, key);
                    }
                }, 3000);
            }
        },
        checkName: function(creator, name, callback){
            multisigContract.getWalletId(creator, name, function(e,d){
                if (d) {
                    if (d[1]) {
                        showNameError();
                    } else {
                        callback();
                    }
                }
            });
        },
        checkDeployed: function(address, name, id, redirect = false){
            multisigContract.getWalletId(address, name, function(e,d){
                if (d) {
                    if (d[1]) {
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
                                        delete window.multisigUndeployed[id];
                                        $("#my-dapps-li").fadeIn();
                                    }
                                }
                            }
                        });
                    }
                }
            });
        }
    }
})()

window.addEventListener('load', function(){
    multisigDapp.init();
});