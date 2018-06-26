/**
 * Created by Fivehundreth on 04.04.2018.
 */

var bettingDapp = (function(){
    if (web3.version.network == "1") {
        var bettingContractAddress = bettingMainAddress;
        var network = 'main';
    } else if (web3.version.network == "4") {
        var bettingContractAddress = bettingRinkebyAddress;
        var network = 'rinkeby';
    } else {
        return;
    }

    return {
        init: function(){
            window.bettingContract = web3.eth.contract(bettingABI).at(bettingContractAddress);
            this.triggers();
        },
        create: function(arbitrator, fee, name, bids){
            pure_name = name;
            name = web3.toHex(name);
            type = "betting";
            //console.log(name);
            let transactionData = bettingContract.createEvent.getData(name, bids, arbitrator, fee);
            web3.eth.estimateGas({
                to: bettingContractAddress,
                data: transactionData,
            }, function(e,d){
                web3.eth.sendTransaction({
                    to: bettingContractAddress,
                    data: transactionData,
                    gas: d,
                }, function(e,d){
                    console.log(e,d);
                    if (!e && d) {
                        console.log('Adding dapp...');
                        addApp(type, pure_name, network);
                    }
                })
            })
        },
        triggers: function(){
            var that = this;
            $('#betting-add-bid').click(function(){
                $('#betting-bids').append(
                    '<div style="display:none;" class="input-group creation-form">' +
                    '<span class="input-group-addon">Bid\'s name:</span>' +
                    '<input placeholder="Name..." required type="text" class="form-control required-betting required-dapp">' +
                    '<span class="input-group-btn">' +
                    '<button class="btn btn-danger btn-remove" type="button"><i class="fa fa-fw fa-times"></i></button>' +
                    '</span>' +
                    '</div>'
                );
                $('#betting-bids>div').fadeIn();
                return false;
            });
            $("#betting-bids").on('click', '.btn-remove', function(){
                $(this).parents(".creation-form").fadeOut(function(){$(this).remove()});
            });
            $('#create-betting-dapp').click(function(){
                var arbitrator = $('#betting-arbitrator').val();
                var fee = $('#betting-fee').val();
                var name = $('#dapp-name').val();
                var names = $('#betting-bids input');
                var bids = [];
                names.each(function(){
                    let name = $(this).val();
                    if (name){
                        bids.push(web3.toHex(name));
                    }
                });
                that.create(arbitrator, fee, name, bids);
            });

            if (network == "main") {
                window.bettingUndeployed = window.bettingMainUndeployed;
            } else if (network == "rinkeby") {
                window.bettingUndeployed = window.bettingRinkebyUndeployed;
            }

            if (!$.isEmptyObject(window.bettingUndeployed)) {
                setInterval(function(){
                    for (var key in window.bettingUndeployed) {
                        bettingDapp.checkDeployed(window.bettingUndeployed[key].address, window.bettingUndeployed[key].name, key);
                    }
                }, 3000);
            }
        },
        checkName: function(creator, name, callback){
            bettingContract.getEventId(creator, name, function(e,d){
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
            console.log('Checking deployed dapp...');
            bettingContract.getEventId(address, name, function(e,d){
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
                                        if (network == "main") {
                                            location.href = '/builder/my-dapps.php';
                                        } else if (network == "rinkeby") {
                                            location.href = '/builder/my-dapps.php?network=rinkeby';
                                        }
                                    } else {
                                        delete window.bettingUndeployed[id];
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
        }
    }
})()

window.addEventListener('load', function(){
    bettingDapp.init();
});