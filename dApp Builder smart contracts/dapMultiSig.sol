pragma solidity ^0.4.19;
contract ibaMultisig {

    /*
    * Types
    */
    struct Transaction {
        uint id;
        address destination;
        uint value;
        bytes data;
        bool executed;
        address[] confirmed;
        address creator;
    }

    struct Wallet {
        address creator;
        uint id;
        uint balance;
        address[] owners;
        Transaction[] transactions;
        uint appovalsreq;
        bool initialized;
    }

    /*
    * Events
    */
    event WalletCreated(uint id);
    event TxnSumbitted(uint id);

    /*
    * Storage
    */
    mapping (address => Wallet[]) public wallets;

    /*
    * Constructor
    */
    function ibaMultisig() public{

    }

    /*
    * Methods
    */
    function createWallet(uint approvals, address[] owners) external returns (bool){

        /*check if approvals num equals or greater than given owners num*/
        assert(approvals <= owners.length);

        /*instantiate new wallet*/
        uint currentLen = wallets[msg.sender].length++;
        wallets[msg.sender][currentLen].creator = msg.sender;
        wallets[msg.sender][currentLen].id = currentLen;
        wallets[msg.sender][currentLen].owners = owners;
        wallets[msg.sender][currentLen].appovalsreq = approvals;
        wallets[msg.sender][currentLen].initialized = true;
        WalletCreated(currentLen);
        return true;
    }

    function getWalletsNum(address creator) public view returns (uint num){
        return wallets[creator].length;
    }

    function getOwners(address creator, uint id) public view returns (address[]){
        return wallets[creator][id].owners;
    }

    function submitTransaction(address creator, address destination, uint walletId, uint value, bytes data) external returns (bool){
        if (wallets[creator][walletId].initialized == true){
            uint newTxId = wallets[creator][walletId].transactions.length++;
            wallets[creator][walletId].transactions[newTxId].destination = destination;
            wallets[creator][walletId].transactions[newTxId].value = value;
            wallets[creator][walletId].transactions[newTxId].data = data;
            TxnSumbitted(newTxId);
            return true;
        }
    }

    function confirmTransaction(address creator, uint walletId, uint txId) external returns (bool){
        bool found;
        for (uint i = 0;i<wallets[creator][walletId].owners.length;i++){
            if (wallets[creator][walletId].owners[i] == msg.sender){
                found = true;
            }
        }
        if (found){
            Wallet storage wallet = wallets[creator][walletId];
            Transaction storage txn = wallet.transactions[txId];

            txn.confirmed.push(msg.sender);

            /* check whether transaction has reached required amount of confirmations */
            if (txn.confirmed.length == wallet.appovalsreq){

                /* check whether wallet has sufficient balance to send this transaction */
                if (wallet.balance >= txn.value){

                    /* change transaction's status to executed */
                    txn.executed == true;

                    /* change wallet's balance */
                    wallet.balance = wallet.balance - txn.value;

                    /* send transaction */
                    address dest = txn.destination;
                    uint val = txn.value;
                    bytes memory dat = txn.data;
                    assert(this.call(dest,val,dat));
                    return true;
                }
            }
            return true;
        } else {
            return false;
        }
    }

}