<?php
return [
    /** mysql setting */
    'DB_TYPE' => 'mysql',
    'DB_HOST'               =>  'localhost', 
    'DB_NAME'               =>  'chukui_blog',
    'DB_USER'               =>  'root',      
    'DB_PWD'                =>  'chukui',
    /** memcache setting */
    // 'MEMCACHE_SETTING'      => [
    //     'type'  =>  'memcached',
    //     'host'  =>  'localhost',
    //     'port'  =>  '11211',
    //     'prefix'  =>'chukui_',
    //     'expire'  =>60,
    // ],

    /** qiniu */
    'QINIU' => [
        'driver'           => 'Qiniu',
        'driverConfig'     => [
            'secretKey' => 'JG9ISnGdPPu-XV9eEdmwyyeQvtTNac12BWXKMjHY',
            'accessKey' => 'NerkCxrLuV4BvK_0LI7Q3bubApLZbxwjeDTXv_bK',
            'domain'    => 'omu372tgu.bkt.clouddn.com',
            'bucket'    => 'chukui',
        ]
    ],

    /* rbac setiing*/

    'USER_AUTH_ON'=>true,
    'USER_AUTH_TYPE'=>2,        // 默认认证类型 1 登录认证 2 实时认证
    'USER_AUTH_KEY'=>'authId',  // 用户认证SESSION标记
    'ADMIN_AUTH_KEY'=>'administrator',
    'USER_AUTH_MODEL'=>'user',  // 默认验证数据表模型
    'AUTH_PWD_ENCODER'=>'md5',  // 用户认证密码加密方式
    'USER_AUTH_GATEWAY'=>'/public/login.html',   // 默认认证网关
    'NOT_AUTH_MODULE'=>'Public,Baidu,Merchant',        // 默认无需认证模块
    'REQUIRE_AUTH_MODULE'=>'',      // 默认需要认证模块
    'NOT_AUTH_ACTION'=>'',      // 默认无需认证操作
    'REQUIRE_AUTH_ACTION'=>'',      // 默认需要认证操作
    'GUEST_AUTH_ON'=>false,    // 是否开启游客授权访问
    'GUEST_AUTH_ID'=>0,     // 游客的用户ID
    'DB_LIKE_FIELDS'=>'title|remark',
    'RBAC_ROLE_TABLE'=>'role',
    'RBAC_USER_TABLE'=>'role_user',
    'RBAC_ACCESS_TABLE'=>'access',
    'RBAC_NODE_TABLE'=>'node',
    /** url mode */
    'URL_MODEL' => '2',
    'URL_HTML_SUFFIX'       =>  'html',
    /*图片路径*/
    'IMG_SAVE_PATH' => '/app/site/backend.mcgoldfish.com/Public',
    /*按钮文字*/
    'SAVE_BUTTON'  => '保存',
    'CANCEL_BUTTON'  => '&nbsp取消',
    'SEARCH_BUTTON'  => '<i class="icon-search"></i>&nbsp搜索',
    'SEARCH_AERA'  =>  '<i class="icon-search"></i>搜索栏',
    'ADD_BUTTON'  =>  '<i class="icon-plus"></i>&nbsp新增',
    'EDIT_BUTTON' =>  '<i class="icon-edit"></i>&nbsp修改',
    'DELETE_BUTTON' =>  '<i class="icon-trash"></i>&nbsp删除',
    'COMFIRM_BUTTON' => '确定',
    'LISTNAME' => '列表',
    'ADDNAME' => 'add',
    'EDITNAME' => 'edit',
    'DELETENAME' => 'delete',
    /*提示文字*/
    'NEW_ARTICLE_MESSAGE' =>'新文章待审核!',
    /*网站常量*/
    'BACKEND_SITE' => 'backend.mcgoldfish.com',
    'SITE_VERSION' => '1.0.0.01',
    'PORLET_COLOR' => 'blue',
    'SEARCH_BUTTON_COLOR' =>'blue',
    /*百度地图api*/
    'SUGGESTION_AK' => 'OkSOPAGevGNwQKlGeM6Fx7AgAaedBYao',
    /*图片域名*/
    'IMG_URL' => 'http://img.mcgoldfish.com',

    'VER'  => '2016121601',
    'TMPL_CACHE_ON'         =>  false,        // 是否开启模板编译缓存,设为false则每次都会重新编译
];
