//
//  gCalendarButton.m
//  mWebVC
//
//  Created by Alexey Dadanov on 25.01.17.
//  Copyright © 2017 iBuildApp. All rights reserved.
//

#import "gCalendarButton.h"

@interface gCalendarButton() {

    CGRect _initialFrame;
}

@property (nonatomic, strong) UILabel *customTitleLabel;
@property (nonatomic, strong) UIImageView *customImageView;

@end

@implementation gCalendarButton

- (id)initWithFrame:(CGRect)frame {

    self = [super initWithFrame:frame];
    if (self)
    {

        _initialFrame = frame;

        self.backgroundColor = [UIColor clearColor];

        _customImageView = [[UIImageView alloc] init];
        [self addSubview:_customImageView];

        _customTitleLabel = [[UILabel alloc] init];
        [self addSubview:_customTitleLabel];
    }

    return self;

}

-(void) refreshView {

    [self refreshViewWithColor:_titleColor tintColor:_imageTintColor];

}

-(void) refreshViewWithColor:(UIColor *)color tintColor:(UIColor *)tintColor {

    _customTitleLabel.textAlignment = NSTextAlignmentCenter;
    _customTitleLabel.lineBreakMode = NSLineBreakByTruncatingTail;
    _customTitleLabel.numberOfLines = 1;
    _customTitleLabel.backgroundColor = [UIColor clearColor];
    _customTitleLabel.font = _titleFont;
    _customTitleLabel.text = _title;
    _customTitleLabel.textColor = color;

    CGSize titleSize = [_title sizeForFont:_titleFont
                                 limitSize:CGSizeMake(CGFLOAT_MAX, CGFLOAT_MAX)
                           nslineBreakMode:NSLineBreakByTruncatingTail];

    CGFloat imageOriginX = _imageOrigin.x;
    CGFloat imageOriginY = _imageOrigin.y;
    UIImage *templateImage = _image;

    if(SYSTEM_VERSION_GREATER_THAN_OR_EQUAL_TO(@"7.0"))
        templateImage =[_image imageWithRenderingMode:UIImageRenderingModeAlwaysTemplate];

    _customImageView.image = templateImage;
    _customImageView.frame = CGRectMake(imageOriginX, imageOriginY, _image.size.width, _image.size.height);
    _customImageView.tintColor = tintColor;

    CGFloat titleOriginX = _titleOrigin.x;
    CGFloat titleOriginY = _titleOrigin.y;
    _customTitleLabel.frame = CGRectMake(titleOriginX, titleOriginY, titleSize.width, titleSize.height);
}

#pragma mark - Touches

- (void)touchesBegan:(NSSet *)touches withEvent:(UIEvent *)event {

    [super touchesBegan:touches withEvent:event];

    [self refreshViewWithColor:_highlitedColor  tintColor:_highlitedColor];
}

- (void) touchesCancelled:(NSSet *)touches withEvent:(UIEvent *)event {

    [super touchesCancelled:touches withEvent:event];

    [self refreshViewWithColor:_titleColor tintColor:_imageTintColor];
}

- (void) touchesEnded:(NSSet *)touches withEvent:(UIEvent *)event {
    
    [super touchesEnded:touches withEvent:event];
    
    [self refreshViewWithColor:_titleColor tintColor:_imageTintColor];
}

@end
