//
//  gCalendarButton.h
//  mWebVC
//
//  Created by Alexey Dadanov on 25.01.17.
//  Copyright © 2017 iBuildApp. All rights reserved.
//

#import <Foundation/Foundation.h>
#import <UIKit/UIKit.h>

@interface gCalendarButton : UIButton

- (id)initWithFrame:(CGRect)frame;

-(void) refreshView;

@property (nonatomic, strong) NSString *title;

@property (nonatomic, strong) UIImage *image;

@property (nonatomic, strong) UIColor *highlitedColor;

@property (nonatomic, strong) UIColor *titleColor;
@property (nonatomic, strong) UIColor *imageTintColor;

@property (nonatomic, strong) UIFont *titleFont;

@property (nonatomic, assign) CGPoint titleOrigin;
@property (nonatomic, assign) CGPoint imageOrigin;

@end
