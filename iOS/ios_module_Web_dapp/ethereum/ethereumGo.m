/****************************************************************************
 *                                                                           *
 *  Copyright (C) 2014-2017 iBuildApp, Inc. ( http://ibuildapp.com )     *
 *                                                                           *
 *  This file is part of iBuildApp.                                          *
 *                                                                           *
 *  This Source Code Form is subject to the terms of the iBuildApp License.  *
 *  You can obtain one at http://ibuildapp.com/license/                 *
 *                                                                           *
 ****************************************************************************/

#import "ethereumGo.h"
#import <Geth/Geth/Geth.h>

#define etherChainID 4

@implementation EthereumGo{
    NSData *jsonData;
}

// export the list of purses
-(NSMutableArray *)goGetAccounts
{
    NSMutableArray *addresses = [NSMutableArray new];
    GethKeyStore *keyStore = [self createEncryptedAccountManager];
    GethAccounts *accounts = [keyStore getAccounts];
    for (long i = 0; i <= accounts.size-1; i++){
        NSError *error;
        GethAccount *acc = [accounts get:i error:&error];
        [addresses addObject: acc.getAddress.getHex];
        NSLog(@"Adress: %@", error ? error : acc.getAddress.getHex);
    }
    return addresses;
}

// export the list of transactions for the address
-(NSString *)goGetTransactionsByAddress:(NSString *)accAddress
{
    NSError *error;
    NSArray *transactions = [NSArray new];
    NSMutableArray *unique = [NSMutableArray new];
    NSArray *paths = NSSearchPathForDirectoriesInDomains( NSCachesDirectory, NSUserDomainMask, YES);
    NSString *fullPath = [[paths objectAtIndex:0] stringByAppendingPathComponent:[NSString stringWithFormat:@"/transactionsLogs/%@.log", accAddress]];
    
    NSFileHandle *file = [NSFileHandle fileHandleForReadingAtPath:fullPath];
    if (file != nil){
        NSString *getfileContents = [NSString stringWithContentsOfFile:fullPath encoding:NSUTF8StringEncoding error:&error];
        transactions = [getfileContents componentsSeparatedByString:@"\n"];
    }
    [file closeFile];
    NSLog(@"Transactions: %@", error ? error : transactions);
    
    for(NSString *arrEl in transactions){
        if(![unique containsObject:arrEl] && ![arrEl isEqualToString:@""])
            [unique addObject:arrEl];
    }
    NSLog(@"TransactionsUnique: %@", unique);
    
    NSString *transactionsString = @"";
    if(unique.count > 0){
        for(int i = 0; i <= unique.count-1; i++){
            transactionsString = [transactionsString stringByAppendingString:[NSString stringWithFormat:@"\"%@\"",unique[i]]];
            if(i != unique.count-1)
                transactionsString = [transactionsString stringByAppendingString:@", "];
        }
    }
    return transactionsString;
}

// Get an account at his address
-(GethAccount *)getAccountByAddress:(NSString *)accAddress
{
    GethKeyStore *keyStore = [self createEncryptedAccountManager];
    GethAccounts *accounts = [keyStore getAccounts];
    GethAccount *acc;
    for (long i = 0; i <= accounts.size-1; i++){
        NSError *error;
        acc = [accounts get:i error:&error];
        NSLog(@"Adress: %@", error ? error : acc.getAddress.getHex);
        if([acc.getAddress.getHex isEqualToString:accAddress])
            break;
    }
    return acc;
}

// create an account and return its address
-(NSString *)goCreateAccountWithPass:(NSString *)passCreate
{
    NSString *address;
    GethKeyStore *keyStore = [self createEncryptedAccountManager];
    GethAccount *newAcc = [self createAccount:passCreate withKeyStore:keyStore];
    if(newAcc){
        address = newAcc.getAddress.getHex;
    }
    return address;
}

// check your account password
-(BOOL)checkPassword:(NSString *)pass accountAddress:(NSString *)accAddress
{
    GethKeyStore *keyStore = [self createEncryptedAccountManager];
    GethAccount *acc = [self getAccountByAddress:accAddress];
    BOOL goodPass = NO;
    NSError *error;
    goodPass = [keyStore unlock:acc passphrase:pass error:&error];
    NSLog(@"CheckingRassword: %@, error: %@", goodPass ? @"YES" : @"NO", error);
    if(goodPass)
        [keyStore lock:[acc getAddress] error:&error];
    return goodPass;
}

// create a transaction with the received address and return it to json
-(NSString *)goCreateSignTransaction:(NSString *)pass
                      accountAddress:(NSString *)accAddress
                           addressTO:(NSString *)addrT
                            withData:(NSString *)dataT
                            gasLimit:(long long)gasLimit
                            gasPrice:(long long)gasPrice
                               nonce:(long)nonce
                               value:(long)value
{
    NSError *error;
    NSArray *paths = NSSearchPathForDirectoriesInDomains( NSCachesDirectory, NSUserDomainMask, YES);
    NSString *fullPath = [[paths objectAtIndex:0] stringByAppendingPathComponent:[NSString stringWithFormat:@"/transactionsLogs/%@.log", accAddress]];

    GethKeyStore *keyStore = [self createEncryptedAccountManager];
    GethAccount *acc = [self getAccountByAddress:accAddress];
    GethTransaction *transaction = [self createTransaction:pass address:addrT withData:dataT gasLimit:gasLimit gasPrice:gasPrice nonce:nonce value:value];
    NSLog(@"Transaction: %@", transaction.string);
    GethTransaction *transactionSigned = [self signTransaction:pass acount:acc ofKeyStore:keyStore withTransaction:transaction];
    NSLog(@"signTransaction: %@", transactionSigned.string);

    NSString *hexString = [NSString stringWithFormat:@"0x%@",[self hexStringFromData:[transactionSigned encodeRLP:&error]]];
    NSLog(@"signTransactionHex: %@", hexString);
    NSFileHandle *file = [NSFileHandle fileHandleForUpdatingAtPath:fullPath];
    if (file != nil){
        [file seekToEndOfFile];
        NSString *newSTR = [NSString stringWithFormat:@"%@\n", transactionSigned.getHash.getHex];
        [file writeData:[newSTR dataUsingEncoding:NSUTF8StringEncoding]];
    }
    [file closeFile];
    return hexString;
}

- (NSString *)stringFromHexString:(NSString *)hexString {
    
    // The hex codes should all be two characters.
    if (([hexString length] % 2) != 0)
        return nil;
    
    NSMutableString *string = [NSMutableString string];
    
    for (NSInteger i = 0; i < [hexString length]; i += 2) {
        
        NSString *hex = [hexString substringWithRange:NSMakeRange(i, 2)];
        NSInteger decimalValue = 0;
        sscanf([hex UTF8String], "%lx", &decimalValue);
        [string appendFormat:@"%ld", (long)decimalValue];
    }
    
    return string;
}

-(NSString*)hexFromStr:(NSString*)str
{
    NSData* nsData = [str dataUsingEncoding:NSUTF8StringEncoding];
    const char* data = [nsData bytes];
    NSUInteger len = nsData.length;
    NSMutableString* hex = [NSMutableString string];
    for(int i = 0; i < len; ++i)[hex appendString:[NSString stringWithFormat:@"%0.2hhx", data[i]]];
    return hex;
}

// translation of NSData into a NSString
- (NSString*)hexStringFromData:(NSData *)data
{
    unichar* hexChars = (unichar*)malloc(sizeof(unichar) * (data.length*2));
    unsigned char* bytes = (unsigned char*)data.bytes;
    for (NSUInteger i = 0; i < data.length; i++) {
        unichar c = bytes[i] / 16;
        if (c < 10) c += '0';
        else c += 'a' - 10;
        hexChars[i*2] = c;
        c = bytes[i] % 16;
        if (c < 10) c += '0';
        else c += 'a' - 10;
        hexChars[i*2+1] = c;
    }
    NSString* retVal = [[NSString alloc] initWithCharactersNoCopy:hexChars
                                                           length:data.length*2
                                                     freeWhenDone:YES];
    return retVal;
}

// create an encrypted account manager
-(GethKeyStore *)createEncryptedAccountManager
{
    NSArray *paths = NSSearchPathForDirectoriesInDomains( NSCachesDirectory, NSUserDomainMask, YES);
    NSString *folderPath = [[paths objectAtIndex:0] stringByAppendingPathComponent:@"/keystore"];
    NSError *error;

    if ( ![[NSFileManager defaultManager] fileExistsAtPath:folderPath] )
        [[NSFileManager defaultManager] createDirectoryAtPath:folderPath
                                  withIntermediateDirectories:NO
                                                   attributes:nil
                                                        error:&error];

    NSString *folderLogs = [[paths objectAtIndex:0] stringByAppendingPathComponent:@"/transactionsLogs"];

    if ( ![[NSFileManager defaultManager] fileExistsAtPath:folderLogs] )
        [[NSFileManager defaultManager] createDirectoryAtPath:folderLogs
                                  withIntermediateDirectories:NO
                                                   attributes:nil
                                                        error:&error];
    
    GethKeyStore *keyStore = GethNewKeyStore(folderPath, GethLightScryptN, GethLightScryptP);
    return keyStore;
}

// Create a new account with the specified encryption passphrase
-(GethAccount *)createAccount:(NSString *)pass
                 withKeyStore:(GethKeyStore *)keyStore
{
    NSError *error;
    GethAccount *acc = [keyStore newAccount:pass error:&error];
    NSLog(@"createNewAccount: %@", error ? error : acc.getAddress.getHex);
    NSArray *paths = NSSearchPathForDirectoriesInDomains( NSCachesDirectory, NSUserDomainMask, YES);
    NSString *folderPath = [[paths objectAtIndex:0] stringByAppendingPathComponent:@"/transactionsLogs"];
    [[NSFileManager defaultManager] createFileAtPath:[NSString stringWithFormat:@"%@/%@.log", folderPath, acc.getAddress.getHex] contents:nil attributes:nil];
    
    return acc;
}

// export account. The returned data is a coded, encrypted JSON key file.
-(NSData *)exportAccount:(NSString *)pass
                 newPass:(NSString *)passNew
              forAccount:(GethAccount *)acc
            withKeyStore:(GethKeyStore *)keyStore
{
    NSError *error;
    NSData *json = [keyStore exportKey:acc passphrase:pass newPassphrase:passNew error:&error];
    NSLog(@"exportAccount: %@", error ? error : json);
    return json;
}

// Update the passphrase in the account created above inside the local keystore.
-(BOOL)updatePassphrase:(NSString *)pass
                newPass:(NSString *)passNew
             forAccount:(GethAccount *)acc
           withKeyStore:(GethKeyStore *)keyStore
{
    NSError *error;
    BOOL upd = [keyStore updateAccount:acc passphrase:pass newPassphrase:passNew error:&error];
    NSLog(@"updatePassphrase error: %@", error);
    return upd;
}

// Delete the account updated above from the local keystore.
-(BOOL)deleteAccount:(NSString *)pass
          forAccount:(GethAccount *)acc
        withKeyStore:(GethKeyStore *)keyStore
{
    NSError *error;
    BOOL del = [keyStore deleteAccount:acc passphrase:pass error:&error];
    NSLog(@"deleteAccount error: %@", error);
    return del;
}

// We import an account from json with the ability to change the passphrase.
-(void)importAccount:(NSString *)pass
             newPass:(NSString *)passNew
            jsonData:(NSData *)jsonData
        withKeyStore:(GethKeyStore *)keyStore
{
    NSError *error;
    GethAccount *acc = [keyStore importKey:jsonData passphrase:pass newPassphrase:passNew error:&error];
    NSLog(@"importAccount: %@", error ? error : acc);
}

// Create a transaction, the data is transferred from the line to the desired format without changing their appearance
-(GethTransaction *)createTransaction:(NSString *)pass
                              address:(NSString *)addr
                             withData:(NSString *)dataT
                             gasLimit:(long long)gasLimit
                             gasPrice:(long long)gasPrice
                                nonce:(long)nonce
                                value:(long)value
{
    NSError *error;
    GethAddress *to = GethNewAddressFromHex(addr, &error);
    dataT = [dataT substringFromIndex:2];
    NSMutableData *commandToSend= [[NSMutableData alloc] init];
    unsigned char whole_byte;
    char byte_chars[3] = {'\0','\0','\0'};
    int i;
    for (i=0; i < [dataT length]/2; i++) {
        byte_chars[0] = [dataT characterAtIndex:i*2];
        byte_chars[1] = [dataT characterAtIndex:i*2+1];
        whole_byte = strtol(byte_chars, NULL, 16);
        [commandToSend appendBytes:&whole_byte length:1];
    }
    GethTransaction *transaction = GethNewTransaction(nonce, to, GethNewBigInt(value), GethNewBigInt(gasLimit), GethNewBigInt(gasPrice), commandToSend);
    NSLog(@"New transaction: %@", error ? error : transaction);
    return transaction;
}

// We sign a transaction with one authorization
-(GethTransaction *)signTransaction:(NSString *)pass
                             acount:(GethAccount *)acc
                         ofKeyStore:(GethKeyStore *)keyStore
                    withTransaction:(GethTransaction *)transaction
{
    NSError *error;
    [keyStore unlock:acc passphrase:pass error:&error];
    GethTransaction *transactionSigned = [keyStore signTxPassphrase:acc passphrase:pass tx:transaction chainID:GethNewBigInt(etherChainID) error:&error];
    [keyStore lock:[acc getAddress] error:&error];
    NSLog(@"signTransaction: %@", error ? error : transactionSigned);
    return transactionSigned;
}

// We sign a transaction with several manually canceled authorizations
-(void)signTransactionManually:(NSString *)pass
                        acount:(GethAccount *)acc
                    ofKeyStore:(GethKeyStore *)keyStore
               withTransaction:(GethTransaction *)transaction
{
    NSError *error;
    [keyStore unlock:acc passphrase:pass error:&error];
    GethTransaction *transactionSigned = [keyStore signTx:acc tx:transaction chainID:GethNewBigInt(etherChainID) error:&error];
    [keyStore lock:[acc getAddress] error:&error];
    NSLog(@"signTransactionManually: %@", error ? error : transactionSigned);
}

// We sign a transaction with several automatically canceled authorizations, a temporary unlock
-(void)signTransactionAutomatically:(NSString *)pass
                             acount:(GethAccount *)acc
                         ofKeyStore:(GethKeyStore *)keyStore
                    withTransaction:(GethTransaction *)transaction
{
    NSError *error;
    [keyStore timedUnlock:acc passphrase:pass timeout:1000000000 error:&error];
    GethTransaction *transactionSigned = [keyStore signTx:acc tx:transaction chainID:GethNewBigInt(etherChainID) error:&error];
    NSLog(@"signTransactionAutomaticallyCancelled: %@", error ? error : transactionSigned);
}

@end
