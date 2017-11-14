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
#import <UIKit/UIKit.h>
#import <QuartzCore/QuartzCore.h>
#import <MessageUI/MessageUI.h>
#import "iphColorskinModel.h"
#import <JavaScriptCore/JavaScriptCore.h>

/**
 *  Overloaded UIWebView for customizing content align when rotating device
 */
@interface TWebView : UIWebView

/**
 *  Align center content of the page when rotating device
 */
@property (nonatomic, assign) BOOL    centerOnPage;

@end

@class mWebVCBehaviour;


/**
 *  Main module class for widget HTML/Web. Module entry point. Represents HTML, Web, Google Calendar and Facebook widgets.
 */
@interface mWebVCViewController : UIViewController <UIWebViewDelegate, MFMailComposeViewControllerDelegate>
{
  BOOL TBarHidden;
  int loadsCount;
  BOOL isReloading;
  
  @protected
  UIButton *tbButton;
}

/**
 *  Customized webView
 */
@property (nonatomic, strong) TWebView *webView;

/**
 *  Application Name
 */
@property (nonatomic, copy  ) NSString *appName;

/**
 *  String representation of URL for webView initialization
 */
@property (nonatomic, copy  ) NSString *URL;

/**
 *  HTML content for webView initialization
 */
@property (nonatomic, copy  ) NSString *content;

/**
 *  JavaScripts for webView initialization
 */
@property (nonatomic, copy  ) NSString *plugins;

/**
 *  Code for google calendar
 */
@property (nonatomic, copy  ) NSString *code;

/**
 *  Custom request for webView initialization
 */
@property (nonatomic, copy  ) NSURLRequest *customRequest;

/**
 *  BaseURL for NSURLRequest
 */
@property (nonatomic, copy  ) NSURL    *baseURL;

/**
 *  Widget type
 */
@property (nonatomic, copy  ) NSString *widgetType;

/**
 *  Align center content of the page when rotating device
 */
@property (nonatomic, assign) BOOL    centerOnPage;

/**
 *  Load content once on viewWillAppear (for fixing video stream)
 */
@property (nonatomic, assign) BOOL    reloadOnceWhenAppear;

/**
 *  Set value YES for webView.scalesPageToFit on next step
 */
@property (nonatomic, assign) BOOL    scalesPageToFitOnNextStep;

@property (nonatomic, strong) mWebVCBehaviour *behaviour;

/**
 *  Add link to ibuildapp.com to sharing messages
 */
@property BOOL showLink;

/**
 *  Don't show navigation toolbar
 */
@property BOOL withoutTBar;

/**
 *  Show navigation toolbar on next step (on pushed viewcontrollers)
 */
@property BOOL showTBarOnNextStep;

/**
 *  Allow scalable for webView
 */
@property BOOL scalable;

/**
 *  Previous value for property 'scalable'
 */
@property BOOL prevScalable;

/**
 *  Allow change scale on webView
 */
@property BOOL allowChangeScale;

/**
 * Lets mWebVC reload its content at viewWillAppear
 */
@property(nonatomic, assign) BOOL     bNeedsReloadWhenAppear;

@property (nonatomic, strong) iphColorskinModel *colorSkin;

/**
 *  BackButton on Toolbar clicked
 *
 *  @param sender UIButton
 */
- (void)bButtonClicked:(id)sender;

/**
 *  ForvardButton on Toolbar clicked
 *
 *  @param sender UIButton
 */
- (void)fButtonClicked:(id)sender;

/**
 *  MiddleButton on Toolbar clicked
 *
 *  @param sender UIButton
 */
- (void)srButtonClicked:(id)sender;

/**
 *  Set widget parameters
 *
 *  @param inputParams dictionary with parameters
 */
- (void)setParams:(NSMutableDictionary *)inputParams;

/**
 *  Show button that opens navigation toolbar
 */
- (void)showTBButton;

/**
 *  Hide button that opens navigation toolbar
 */
- (void)hideTBButton;

/**
 *  Show navigation toolbar
 */
- (void)showTBar;

/**
 *  Hide navigation toolbar
 */
- (void)hideTBar;

/**
 *  Hide NetworkActivityIndicator and change state for middle button on navigation toolbar
 */
- (void)stopLoadingIndication;
- (void)setInputTitle:(NSString *)inputTitle; // not a setter of property!

/**
 *  Init baseURL property with string representation strBaseURL
 *
 *  @param strBaseURL string with baseURL
 */
- (void)setBaseURLfromString:(NSString *) strBaseURL;

/**
 *  Reload webView content
 */
- (void)reload;

/**
 *  Get widget title for statistics
 *
 *  @return widget title
 */
- (NSString*)getWidgetTitle;

@end
