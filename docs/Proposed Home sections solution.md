## Proposed Home sections solution in one query (Draft #1):

+ common endpoint:
    `/wp-json/wl/v1/posts?`

+ required params:
    `categories=1,32&`
    `offset=0&`
    `sort=latest&`
    `count=6&`
    `page=1`

+ example final url:
    ```js
    /wp-json/wl/v1/posts?
        sections[]=categories%3D1%2C32%2C26%2C29%2C30%26offset%3D0%26sort%3Dlatest%26count%3D3%26page%3D1
        sections[]=categories%3D1%2C32%26offset%3D0%26sort%3Dlatest%26count%3D100%26page%3D1
    ```

+ example response:
    ```php
    {
        "sections": [
            {
                "status": true,
                "count": 6,
                "count_total": 14,
                "pages": 3,
                "posts": [...]
            },
            {
                "status": false,
                "count": 0,
                "count_total": 0,
                "pages": 0
            },
        ]
    }
    ```
