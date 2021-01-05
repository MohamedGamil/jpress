## Options Structure (JSON):

```json
{
  "rtl": "false",
  "themeMode": "ThemeMode.dark",
  "onboardModels": [
    {
      "title": "Home",
      "image": "http://Array/2020/07/svg_1.png"
    },
    {
      "title": "Welcome",
      "image": "http://Array/2020/07/logo-demo-6-black.png"
    }
  ],
  "logo": {
    "light": "http://appstage.tielabs.com/wp-content/uploads/2020/08/logo-demo-7.png",
    "dark": "http://appstage.tielabs.com/wp-content/uploads/2020/08/logo-demo-4-e1603273606416.png"
  },
  "appBar": {
    "layout": "AppBarLayout.header2",
    "position": "LogoPosition.center",
    "searchIcon": "0xe820"
  },
  "bottomBar": {
    "navigators": [
      {
        "type": "NavigationType.main",
        "bottom_bar_icon_enable": "true",
        "icon": "0xe800",
        "main": "MainPage.home",
        "title_enable": "true",
        "title": "Home"
      },
      {
        "type": "NavigationType.main",
        "bottom_bar_icon_enable": "true",
        "icon": "0xe801",
        "main": "MainPage.sections",
        "title_enable": "true",
        "title": "Categories"
      },
      {
        "type": "NavigationType.main",
        "bottom_bar_icon_enable": "true",
        "icon": "0xe803",
        "main": "MainPage.favourites",
        "title_enable": "true",
        "title": "Favorites"
      },
      {
        "type": "NavigationType.main",
        "bottom_bar_icon_enable": "true",
        "icon": "0xe935",
        "main": "MainPage.settings",
        "title_enable": "true",
        "title": "Settings"
      }
    ]
  },
  "tabs": {
    "tabsLayout": "TabsLayout.tab1",
    "homeTab": "Top news",
    "tabs": [
      {
        "url": "/wp-json/wl/v1/posts?categories=32",
        "title": "Football"
      },
      {
        "url": "/wp-json/wl/v1/posts?categories=33",
        "title": "Racing"
      },
      {
        "url": "/wp-json/wl/v1/posts?categories=28",
        "title": "Sports"
      },
      {
        "url": "/wp-json/wl/v1/posts?categories=2",
        "title": "World"
      },
      {
        "url": "/wp-json/wl/v1/posts?categories=26",
        "title": "Life Style"
      },
      {
        "url": "/wp-json/wl/v1/posts?categories=11",
        "title": "Travel"
      },
      {
        "url": "/wp-json/wl/v1/posts?categories=2",
        "title": "World"
      },
      {
        "url": "/wp-json/wl/v1/posts?"
      }
    ],
    "firstFeatured": "PostLayout.cardPost",
    "postLayout": "PostLayout.gridPost",
    "options": {
      "category": "true",
      "readTime": "true",
      "date": "true",
      "share": "true",
      "save": "true"
    }
  },
  "homePage": {
    "sections": [
      {
        "title": "section 1",
        "seeMore": {
          "name": "section 1",
          "url": "/wp-json/wl/v1/posts?categories=13,28,11,17&offset=0&sort=latest"
        },
        "url": "/wp-json/wl/v1/posts?categories=13,28,11,17&offset=0&sort=latest&count=9",
        "postLayout": "PostLayout.gridPost",
        "firstFeatured": "PostLayout.featuredPost",
        "options": {
          "category": "true",
          "readTime": "true",
          "date": "true",
          "share": "true",
          "save": "true"
        }
      },
      {
        "postLayout": "PostLayout.htmlAd",
        "content": "<h1>Hello</h1>"
      },
      {
        "postLayout": "PostLayout.adMob",
        "adSize": "banner"
      },
      {
        "postLayout": "PostLayout.imageAd",
        "img": "https://cdn.pixabay.com/photo/2015/04/23/22/00/tree-736885__340.jpg",
        "action": "main",
        "target": "settings"
      }
    ],
    "sections_url": "/wp-json/wl/v1/posts?sections[]=categories%3D13%2C28%2C11%2C17%26offset%3D0%26sort%3Dlatest%26count%3D9sections[]=%26tags%3D34%2C20%2C35%2C36%2C37%2C38%2C39%2C40%2C41%2C42%2C43%2C25%2C8%2C44%2C4%2C15%2C14%2C45%2C46%2C47%2C48%2C49%2C50%2C51%2C7%2C52%2C53%2C54%2C55%2C18%2C56%2C57%2C58%2C59%2C60%2C61%2C62%2C6%2C63%2C64%2C65%2C66%2C67%2C68%2C69%2C70%2C71%2C72%2C73%2C74%2C75%2C76%2C77%2C78%2C79%2C80%2C81%2C5%2C3%2C10%2C82%2C12%2C83%2C84%2C85%2C86%2C87%2C88%2C89%2C90%2C9%2C91%26offset%3D0%26sort%3Dlatest%26count%3D10"
  },
  "archives": {
    "categories": {
      "layout": "CategoriesLayout.cat3",
      "url": "/wp-json/wl/v1/categories"
    },
    "single": {
      "textToSpeech": "true",
      "category": "true",
      "author": "true",
      "tags": "true",
      "readTime": "true",
      "save": "true",
      "share": "true",
      "ads": {
        "interstitial": {
          "afterCount": "5"
        },
        "beforeComments": {
          "type": "adMob",
          "adSize": "banner"
        },
        "afterPost": {
          "type": "html",
          "content": "<h1>Hello</h1>"
        }
      }
    },
    "category": {
      "ads": {
        "afterCount": "5",
        "type": "adMob",
        "adSize": "banner"
      },
      "postLayout": "PostLayout.minimalPost",
      "options": {
        "count": "10",
        "readTime": "true",
        "date": "true",
        "save": "true",
        "share": "true"
      }
    },
    "search": {
      "postLayout": "PostLayout.minimalPost",
      "options": {
        "count": "10",
        "category": "true",
        "readTime": "true",
        "date": "true",
        "save": "true",
        "share": "true"
      }
    },
    "favorites": {
      "postLayout": "PostLayout.minimalPost",
      "url": "/wp-json/wl/v1/posts?&ids=",
      "options": {
        "count": "10",
        "category": "true",
        "readTime": "true",
        "date": "true",
        "save": "true",
        "share": "true"
      }
    }
  },
  "styling": {
    "ThemeMode.light": {
      "bottomBarBackgroundColor": "#BCBCBC",
      "scaffoldBackgroundColor": "#FFFFFF",
      "primary": "#0088ff",
      "secondary": "#333739",
      "secondaryVariant": "#8A8A89",
      "appBarBackgroundColor": "#FFFFFF",
      "appBarColor": "#333739",
      "background": "#FFFFFF",
      "sidemenutextcolor": "#333739",
      "bottomBarInActiveColor": "#8A8A8A",
      "bottomBarActiveColor": "#0088ff",
      "tabBarBackgroundColor": "#FFFFFF",
      "tabBarTextColor": "#7F7F7F",
      "tabBarActiveTextColor": "#333739",
      "tabBarIndicatorColor": "#0088FF",
      "shadowColor": "rgba(0,0,0,0.15)",
      "dividerColor": "rgba(0,0,0,0.05)",
      "inputsbackgroundcolor": "rgba(0,0,0,0.04)",
      "buttonsbackgroudcolor": "#0088FF",
      "buttonTextColor": "#FFFFFF",
      "errorColor": "#FF0000",
      "successColor": "#006900"
    },
    "ThemeMode.dark": {
      "bottomBarBackgroundColor": "#838483",
      "scaffoldBackgroundColor": "#333739",
      "primary": "#0088ff",
      "secondary": "#FFFFFF",
      "secondaryVariant": "#8A8A89",
      "appBarBackgroundColor": "#333739",
      "appBarColor": "#FFFFFF",
      "background": "#333739",
      "sidemenutextcolor": "#FFFFFF",
      "bottomBarInActiveColor": "#C3C3C3",
      "bottomBarActiveColor": "#0088ff",
      "tabBarBackgroundColor": "#333739",
      "tabBarTextColor": "#8A8A89",
      "tabBarActiveTextColor": "#FFFFFF",
      "tabBarIndicatorColor": "#0088FF",
      "shadowColor": "rgba(0,0,0,0.15)",
      "dividerColor": "rgba(255,255,255,0.13)",
      "inputsbackgroundcolor": "rgba(255,255,255,0.07)",
      "buttonsbackgroudcolor": "#0088FF",
      "buttonTextColor": "#FFFFFF",
      "errorColor": "#FF0000",
      "successColor": "#006900"
    }
  },
  "settingsPage": {
    "textSize": "true",
    "rateApp": "true",
    "privacyPolicy": "/wp-json/wl/v1/page?id=3",
    "termsAndConditions": "/wp-json/wl/v1/page?id=3",
    "contactUs": "/wp-json/wl/v1/contact-us",
    "aboutApp": {
      "aboutLogoLight": "http://appstage.tielabs.com/wp-content/plugins/appbear_plugin/img/jannah-logo-light.png",
      "aboutLogoDark": "http://appstage.tielabs.com/wp-content/plugins/appbear_plugin/img/jannah-logo-dark.png",
      "title": "My WordPress Website",
      "content": "Just another WordPress sitern"
    },
    "shortCodes": "true",
    "devMode": {
      "time": "6000",
      "count": "3",
      "addUrl": "/?edd_action=save_development_token",
      "removeUrl": "/?edd_action=remove_development_token"
    },
    "demos": "true"
  },
  "basicUrls": {
    "devMode": "wp-json/wl/v1/dev-mode",
    "getPost": "/wp-json/wl/v1/post",
    "submitComment": "/wp-json/wl/v1/add-comment",
    "removeUrl": "/?edd_action=remove_development_token",
    "saveToken": "/?edd_action=save_token",
    "translations": "/wp-json/wl/v1/translations",
    "getPostWPJSON": "/wp-json/wl/v1/post",
    "getTags": "/wp-json/wl/v1/posts?tags=",
    "getTagsPosts": "/wp-json/wl/v1/posts?tags=",
    "login": "/wp-json/wl/v1/login",
    "selectDemo": "/wp-json/wl/v1/selectDemo",
    "demos": "/wp-json/wl/v1/demos"
  },
  "adMob": {
    "bannerAndroidId": "ca-app-pub-3940256099942544/6300978111",
    "bannerIosId": "",
    "interstitialAndroidId": "ca-app-pub-3940256099942544/1033173712",
    "interstitialIosId": ""
  },
  "baseUrl": "http://appstage.tielabs.com/",
  "defaultLayout": "Layout.standard",
  "searchApi": "/wp-json/wl/v1/posts?s=",
  "commentsApi": "/wp-json/wl/v1/comments?id=",
  "commentAdd": "/wp-json/wl/v1/add-comment",
  "relatedPostsApi": "/wp-json/wl/v1/posts?related_id=",
  "lang": "en",
  "validConfig": "true",
  "ttsLanguage": "en-US",
  "copyrights": "http://appstage.tielabs.com"
}
```
