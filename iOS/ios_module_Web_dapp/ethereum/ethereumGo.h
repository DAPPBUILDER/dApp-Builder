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

#import <Foundation/Foundation.h>

@interface EthereumGo: NSObject

-(NSString *)goCreateSignTransaction:(NSString *)pass
                      accountAddress:(NSString *)accAddress
                           addressTO:(NSString *)addrT
                            withData:(NSString *)dataT
                            gasLimit:(long long)gasLimit
                            gasPrice:(long long)gasPrice
                               nonce:(long)nonce
                               value:(long)value;

-(NSString *)goCreateAccountWithPass:(NSString *)passCreate;

-(NSMutableArray *)goGetAccounts;

-(NSString *)goGetTransactionsByAddress:(NSString *)accAddress;

-(BOOL)checkPassword:(NSString *)pass accountAddress:(NSString *)accAddress;

@end
