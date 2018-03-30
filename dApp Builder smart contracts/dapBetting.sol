pragma solidity ^0.4.21;

contract dapBetting {
    
    /* Types */
    
    enum eventStatus{ open, closed, finished }
    
    struct bid{
        uint id;
        bytes32 name;
        address[] whoBet;
        uint amoutReceived;
    }
    
    struct betEvent{
        uint id;
        bytes32 name;
        address creator;
        address arbitrator;
        bytes32 winner;
        uint arbitratorFee;
        bid[] bids;
        bet[] bets;
        eventStatus status;
    }
    
    struct bet{
        address person;
        bytes32 bidName;
        uint amount;
    }
    
    /* Storage */
    
    mapping (address => betEvent[]) public betEvents;
    mapping (address => uint) public pendingWithdrawals;
    
    /* Events */
    
    event EventCreated(uint id, address creator);
    event betMade(uint value, uint id);
    event eventStatusChanged(uint status);
    event eventFinished(bytes32 winner);
    event withdrawalDone(uint amount);
    
    /* Methods */
    
    function createEvent(bytes32 name, bytes32[] names, address arbitrator, uint fee) external{
        
        require(fee < 100);
        
        /* check names for duplicates */
        for (uint8 y=0;i<names.length;i++){
            require(names[y] != names[y+1]);
        }
        
        uint newId = betEvents[msg.sender].length++;
        betEvents[msg.sender][newId].name = name;
        betEvents[msg.sender][newId].arbitrator = arbitrator;
        betEvents[msg.sender][newId].status = eventStatus.open;
        betEvents[msg.sender][newId].creator = msg.sender;
        betEvents[msg.sender][newId].arbitratorFee = fee;
        
        for (uint8 i = 0;i < names.length; i++){
            uint newBidId = betEvents[msg.sender][newId].bids.length++;
            betEvents[msg.sender][newId].bids[newBidId].name = names[i];
        }
        
        emit EventCreated(newId, msg.sender);
    }
    
    function makeBet(address creator, uint eventId, bytes32 bidName) payable external{
        
        /* check whether bid with given name actually exists */
        bool found;
        for (uint8 i=0;i<betEvents[creator][eventId].bids.length;i++){
            if (betEvents[creator][eventId].bids[i].name == bidName){
                bid storage foundBid = betEvents[creator][eventId].bids[i];
                found = true;
            }
        }
        require(found);
        require(betEvents[creator][eventId].status == eventStatus.open);
        foundBid.whoBet.push(msg.sender);
        foundBid.amoutReceived += msg.value;
        uint newBetId = betEvents[creator][eventId].bets.length++;
        betEvents[creator][eventId].bets[newBetId].person = msg.sender;
        betEvents[creator][eventId].bets[newBetId].amount = msg.value;
        betEvents[creator][eventId].bets[newBetId].bidName = bidName;
        
        emit betMade(msg.value, newBetId);
    }
    
    function finishEvent(address creator, uint eventId) external{
        require(msg.sender == betEvents[creator][eventId].arbitrator);
        betEvents[creator][eventId].status = eventStatus.finished;
        emit eventStatusChanged(1);
    }
    
    function determineWinner(address creator, uint eventId, bytes32 bidName) external{
        require(msg.sender == betEvents[creator][eventId].arbitrator);
        bool found;
        for (uint8 i=0;i<betEvents[creator][eventId].bids.length;i++){
            if(betEvents[creator][eventId].bids[i].name == bidName){
                found = true;
            }
        }
        //require(found);
        require(betEvents[creator][eventId].status == eventStatus.finished);
        betEvent storage cEvent = betEvents[creator][eventId];
        //cEvent.status = eventStatus.finished;
        cEvent.winner = bidName;
        uint amountLost;
        uint wonBetsLen;
        for (uint y=0;y<cEvent.bids.length;y++){
            if (cEvent.bids[y].name != cEvent.winner){
                amountLost += cEvent.bids[y].amoutReceived;
            }
        }
        uint feeAmount = (amountLost/100)*cEvent.arbitratorFee;
        amountLost = amountLost - feeAmount;
        pendingWithdrawals[cEvent.arbitrator] += feeAmount;
        
        for (uint x=0;x<cEvent.bets.length;x++){
            if(cEvent.bets[x].bidName == cEvent.winner){
                wonBetsLen++;
                pendingWithdrawals[cEvent.bets[x].person] += cEvent.bets[x].amount;
            }
        }
        for (uint c=0;c<cEvent.bets.length;c++){
            if(cEvent.bets[c].bidName == cEvent.winner){
               uint adamount = amountLost/wonBetsLen;
               pendingWithdrawals[cEvent.bets[c].person] += adamount;
            }
        }
        emit eventFinished(cEvent.winner);
    }
    
    function withdraw(address person) private{
        uint amount = pendingWithdrawals[person];
        pendingWithdrawals[person] = 0;
        person.transfer(amount);
        emit withdrawalDone(amount);
    }
    
    function requestWithdraw() external {
        require(pendingWithdrawals[msg.sender] != 0);
        withdraw(msg.sender);
    }
    
    /* Getters */
    
    function getBidsNum(address creator, uint eventId) external view returns (uint){
        return betEvents[creator][eventId].bids.length;
    }
    
    function getBetsNums(address creator, uint eventId) external view returns (uint){
        return betEvents[creator][eventId].bets.length;
    }
    
    function getBet(address creator, uint eventId, uint betId) external view returns(address, bytes32, uint){
        bet storage foundBet = betEvents[creator][eventId].bets[betId];
        return (foundBet.person, foundBet.bidName, foundBet.amount);
    }
    
    function getEventId(address creator, bytes32 eventName) external view returns (uint, bool){
        for (uint i=0;i<betEvents[creator].length;i++){
            if(betEvents[creator][i].name == eventName){
                return (betEvents[creator][i].id, true);
            }
        }
    }
}