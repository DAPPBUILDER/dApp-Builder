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
        bytes32 name;
        address creator;
        uint id;
        uint allowance;
        address[] owners;
        Transaction[] transactions;
        uint appovalsreq;
    }
    
    /*
    * Modifiers
    */
    modifier onlyOwner ( address creator, uint walletId ) {
        bool found;
        for (uint i = 0;i<wallets[creator][walletId].owners.length;i++){
            if (wallets[creator][walletId].owners[i] == msg.sender){
                found = true;
            }
        }
        if (found){
            _;
        }
    }
    
    /*
    * Events
    */
    event WalletCreated(uint id);
    event TxnSumbitted(uint id);
    event TxnConfirmed(uint id);

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
    * Getters
    */
    function getWalletId(address creator, bytes32 name) external view returns (uint, bool){
        for (uint i = 0;i<wallets[creator].length;i++){
            if (wallets[creator][i].name == name){
                return (i, true);
            }
        }
    }

    function getOwners(address creator, uint id) external view returns (address[]){
        return wallets[creator][id].owners;
    }
    
    function getTxnNum(address creator, uint id) external view returns (uint){
        require(wallets[creator][id].owners.length > 0);
        return wallets[creator][id].transactions.length;
    }
    
    function getTxn(address creator, uint walletId, uint id) external view returns (uint, address, uint, bytes, bool, address[], address){
        Transaction storage txn = wallets[creator][walletId].transactions[id];
        return (txn.id, txn.destination, txn.value, txn.data, txn.executed, txn.confirmed, txn.creator);
    }
    
    /*
    * Methods
    */
    function createWallet(uint approvals, address[] owners, bytes32 name) external payable{

        /* check if name was actually given */
        require(name.length != 0);
        
        /*check if approvals num equals or greater than given owners num*/
        require(approvals <= owners.length);
        
        /* check if wallets with given name already exists */
        bool found;
        for (uint i = 0; i<wallets[msg.sender].length;i++){
            if (wallets[msg.sender][i].name == name){
                found = true;
            }
        }
        require (found == false);
        
        /*instantiate new wallet*/
        uint currentLen = wallets[msg.sender].length++;
        wallets[msg.sender][currentLen].name = name;
        wallets[msg.sender][currentLen].creator = msg.sender;
        wallets[msg.sender][currentLen].id = currentLen;
        wallets[msg.sender][currentLen].allowance = msg.value;
        wallets[msg.sender][currentLen].owners = owners;
        wallets[msg.sender][currentLen].appovalsreq = approvals;
        WalletCreated(currentLen);
    }

    function submitTransaction(address creator, address destination, uint walletId, uint value, bytes data) onlyOwner (creator,walletId) external returns (bool) {
        uint newTxId = wallets[creator][walletId].transactions.length++;
        wallets[creator][walletId].transactions[newTxId].id = newTxId;
        wallets[creator][walletId].transactions[newTxId].destination = destination;
        wallets[creator][walletId].transactions[newTxId].value = value;
        wallets[creator][walletId].transactions[newTxId].data = data;
        wallets[creator][walletId].transactions[newTxId].creator = msg.sender;
        TxnSumbitted(newTxId);
        return true;
    }

    function confirmTransaction(address creator, uint walletId, uint txId) onlyOwner(creator, walletId) external returns (bool){
        Wallet storage wallet = wallets[creator][walletId];
        Transaction storage txn = wallet.transactions[txId];

        //check whether this owner has already confirmed this txn
        bool f;
        for (uint8 i = 0; i<txn.confirmed.length;i++){
            if (txn.confirmed[i] == msg.sender){
                f = true;
            }
        }
        //push sender address into confirmed array if haven't found
        require(!f);
        txn.confirmed.push(msg.sender);
        
        //fire event
        TxnConfirmed(txId);
        
        return true;
    }
    
    function executeTxn(address creator, uint walletId, uint txId) onlyOwner(creator, walletId) external returns (bool){
        Wallet storage wallet = wallets[creator][walletId];
        
        Transaction storage txn = wallet.transactions[txId];
        
        /* check whether transaction has reached required amount of confirmations */
        require(txn.confirmed.length >= wallet.appovalsreq);
        
        /* check whether wallet has sufficient balance to send this transaction */
        require(wallet.allowance >= txn.value);
        
        /* send transaction */
        address dest = txn.destination;
        uint val = txn.value;
        bytes memory dat = txn.data;
        assert(dest.call.value(val)(dat));
            
        /* change transaction's status to executed */
        txn.executed == true;

        /* change wallet's balance */
        wallet.allowance = wallet.allowance - txn.value;

        return true;
        
    }
}