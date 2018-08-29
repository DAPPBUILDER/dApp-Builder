pragma solidity 0.4.24;
contract dapMultisig {

    /*
    * Types
    */
    struct Transaction {
        uint id;
        address destination;
        uint value;
        bytes data;
        TxnStatus status;
        address[] confirmed;
        address creator;
    }
    
    struct Log {
        uint amount;
        address sender;
    }
    
    enum TxnStatus { Unconfirmed, Pending, Executed }
    
    /*
    * Modifiers
    */
    modifier onlyOwner () {
        bool found;
        for (uint i = 0;i<owners.length;i++){
            if (owners[i] == msg.sender){
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
    event WalletCreated(address creator, address[] owners);
    event TxnSumbitted(uint id);
    event TxnConfirmed(uint id);
    event topUpBalance(uint value);

    /*
    * Storage
    */
    bytes32 name;
    address creator;
    uint allowance;
    address[] owners;
    Log[] logs;
    Transaction[] transactions;
    uint appovalsreq;
    
    /*
    * Constructor
    */
    constructor (uint _approvals, address[] _owners, bytes32 _name) public payable{
        /* check if name was actually given */
        require(_name.length != 0);
        
        /*check if approvals num equals or greater than given owners num*/
        require(_approvals <= _owners.length);
        
        name = _name;
        creator = msg.sender;
        allowance = msg.value;
        owners = _owners;
        appovalsreq = _approvals;
        emit WalletCreated(msg.sender, _owners);
    }

    //fallback to accept funds without method signature
    function () external payable {
        allowance += msg.value;
    }
    
    /*
    * Getters
    */

    function getOwners() external view returns (address[]){
        return owners;
    }
    
    function getTxnNum() external view returns (uint){
        return transactions.length;
    }
    
    function getTxn(uint _id) external view returns (uint, address, uint, bytes, TxnStatus, address[], address){
        Transaction storage txn = transactions[_id];
        return (txn.id, txn.destination, txn.value, txn.data, txn.status, txn.confirmed, txn.creator);
    }
    
    function getLogsNum() external view returns (uint){
        return logs.length;
    }
    
    function getLog(uint logId) external view returns (address, uint){
        return(logs[logId].sender, logs[logId].amount);
    }
    
    /*
    * Methods
    */

    function topBalance() external payable {
        require (msg.value > 0 wei);
        allowance += msg.value;
        
        /* create new log entry */
        uint loglen = logs.length++;
        logs[loglen].amount = msg.value;
        logs[loglen].sender = msg.sender;
        emit topUpBalance(msg.value);
    }
    
    function submitTransaction(address _destination, uint _value, bytes _data) onlyOwner () external returns (bool) {
        uint newTxId = transactions.length++;
        transactions[newTxId].id = newTxId;
        transactions[newTxId].destination = _destination;
        transactions[newTxId].value = _value;
        transactions[newTxId].data = _data;
        transactions[newTxId].creator = msg.sender;
        emit TxnSumbitted(newTxId);
        return true;
    }

    function confirmTransaction(uint txId) onlyOwner() external returns (bool){
        Transaction storage txn = transactions[txId];

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
        
        if (txn.confirmed.length == appovalsreq){
            txn.status = TxnStatus.Pending;
        }
        
        //fire event
        emit TxnConfirmed(txId);
        
        return true;
    }
    
    function executeTxn(uint txId) onlyOwner() external returns (bool){
        
        Transaction storage txn = transactions[txId];
        
        /* check txn status */
        require(txn.status == TxnStatus.Pending);
        
        /* check whether wallet has sufficient balance to send this transaction */
        require(allowance >= txn.value);
        
        /* send transaction */
        address dest = txn.destination;
        uint val = txn.value;
        bytes memory dat = txn.data;
        assert(dest.call.value(val)(dat));
            
        /* change transaction's status to executed */
        txn.status = TxnStatus.Executed;

        /* change wallet's balance */
        allowance = allowance - txn.value;

        return true;
        
    }
}