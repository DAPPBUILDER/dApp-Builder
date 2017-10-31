# dApp-Builder

This is a dApp-browser and Ethereum wallet, stored on a mobile device, and allows you to make transactions when working with Ethereum dApps on your iOS or Android device.

The principle is to add additional html and js to the web page of dApp frontend: the library [Web3.js](https://github.com/ethereum/web3.js), the script that creates the Web3 object that interacts with dApp Builder Ethereum node, and additional scripts to work with the user's wallet and signing transactions. A special script allows user to intercept sending of the transaction into blockchain through Web3. It gives the user an opportunity to sign the transaction with one of his Ethereum accounts and to set values of Gas Price and Gas Limit. The private key for ETH-account is stored at the user’s device by using [Go Ethereum Account management](https://github.com/ethereum/go-ethereum/wiki/Mobile:-Account-management).

This solution is made as a widget for iBuildApp mobile app creation platform. You can try how it works on Android [here](http://ibuildapp.com/dapps.php).

![dApp Widget Architecture](https://dapps.ibuildapp.com/images/architecture.png)