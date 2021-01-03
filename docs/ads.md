## HOME SECTIONS (ADMOB):

```json
"sections": [
      {
        "title": "section 1",
        "seeMore": {
          "name": "section 1",
          "url": "/wp-json/wl/v1/posts?categories=13,28,11,17&offset=0&sort=latest"
        },
        "url": "/wp-json/wl/v1/posts?categories=13,28,11,17&offset=0&sort=latest&count=9",
        "postLayout": "PostLayout.endThumbPost",
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
        "postLayout": "PostLayout.adMob",
        "adSize": "banner"
      },
      {
        "postLayout": "PostLayout.htmlAd",
        "htmlAd": {
          "content": "<h1>Hello World</h1>"
        }
      },
      {
        "postLayout": "PostLayout.imageAd",
        "imageAd": {
          "type": "url",
          "value": "https://cdn.pixabay.com/photo/2015/04/23/22/00/tree-736885__340.jpg",
          "img": "https://cdn.pixabay.com/photo/2015/04/23/22/00/tree-736885__340.jpg"
        }
      }
    ]
```


- - -


## AFTER POST (ADMOB)

```json
 "afterPost": {
          "type": "main",
          "value": "settings",
          "img": "https://cdn.pixabay.com/photo/2015/04/23/22/00/tree-736885__340.jpg"
        },
```


- - -


## ADMOB OPTIONS:

```json
"adMob": {
    "html": {
      "positions": {
        "bottom": {
          "content": "<h1>Hello World</h1>"
        },
        "top": {
          "content": "<h1>Hello World</h1>"
        },
        "afterPost": {
          "content": "<h1>Hello World</h1>"
        },
        "beforeComments": {
          "content": "<h1>Hello World</h1>"
        }
      }
    },
    "image": {
      "positions": {
        "bottom": {
          "type": "url",
          "value": "https://cdn.pixabay.com/photo/2015/04/23/22/00/tree-736885__340.jpg",
          "img": "https://cdn.pixabay.com/photo/2015/04/23/22/00/tree-736885__340.jpg"
        },
        "top": {
          "type": "url",
          "value": "https://cdn.pixabay.com/photo/2015/04/23/22/00/tree-736885__340.jpg",
          "img": "https://cdn.pixabay.com/photo/2015/04/23/22/00/tree-736885__340.jpg"
        },
        "afterPost": {
          "type": "main",
          "value": "settings",
          "img": "https://cdn.pixabay.com/photo/2015/04/23/22/00/tree-736885__340.jpg"
        },
        "beforeComments": {
          "type": "url",
          "value": "https://cdn.pixabay.com/photo/2015/04/23/22/00/tree-736885__340.jpg",
          "img": "https://cdn.pixabay.com/photo/2015/04/23/22/00/tree-736885__340.jpg"
        }
      }
    },
    "banner": {
      "androidBannerId": "ca-app-pub-3940256099942544/6300978111",
      "iosBannerId": "ca-app-pub-3940256099942544/6300978111",
      "positions": {
        "bottom": "true",
        "top": "true",
        "afterPost": "true",
        "beforeComments": "true"
      }
    },
    "interstatial": {
      "count": "5",
      "androidInterstatialId": "ca-app-pub-3940256099942544/1033173712",
      "iosInterstatialId": "ca-app-pub-3940256099942544/1033173712",
      "positions": {
        "beforePost": "true"
      }
    }
  }, 
```
