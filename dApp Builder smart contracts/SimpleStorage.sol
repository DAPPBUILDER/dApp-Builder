pragma solidity ^0.5.9; 
// Imagine a big integer that the whole world could share
contract SimpleStorage {
    uint storedData;

    function set(uint x) public {
        storedData = x;
    }

    function get() view public returns (uint) {
        return storedData;
    }
    
    function increment (uint n) public {
        storedData = storedData + n;
        return;
    }
    
    function decrement (uint n) public {
        storedData = storedData - n;
        return;
    }
    
}

 


