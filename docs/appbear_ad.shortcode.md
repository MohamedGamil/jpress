## Docs for using the JPress Ads Shortcode:

### Example using the AdMob settings:

**Possible key values for adMob ads:** `adMob`, `admob`, `ad`, or *empty value*

```html
[jpress_ad type='admob' size='banner'/]
```

*or*

```html
[jpress_ad type='ad' size='smart_banner'/]
```

*or*

```html
[jpress_ad size='smart_banner'/]
```


- - -


### Example using the HTML Ad settings:

**Possible key values for HTML ads:** `htmlAd`, `html`

```html
[jpress_ad type='html']<strong>HTML Ad Content!</strong>[/jpress_ad]
```

*or*

```html
[jpress_ad type='html' content='<strong>HTML Ad Content!</strong>'/]
```


- - -


### Example using the Image Ad settings:

**Possible key values for Image ads:** `image`, `img`, `imageAd`

```html
[jpress_ad type='img' target='home' action='main' image='http://placeimg.com/640/360/any' /]
```

*or*

```html
[jpress_ad type='image' target='google.com' action='url' image='http://placeimg.com/640/360/any' /]
```
