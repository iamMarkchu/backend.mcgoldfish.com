<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
<head>
<meta charset="utf-8" />
<title>后台管理|<?php echo ($conInfo["title"]); ?></title>
<meta content="width=device-width, initial-scale=1.0" name="viewport" />
<meta content="" name="description" />
<meta content="" name="author" />
<link href="/Public/media/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="/Public/media/css/bootstrap-responsive.min.css" rel="stylesheet" type="text/css" />
<link href="/Public/media/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<link href="/Public/media/css/style-metro.css" rel="stylesheet" type="text/css" />
<link href="/Public/media/css/style.css" rel="stylesheet" type="text/css" />
<link href="/Public/media/css/style-responsive.css" rel="stylesheet" type="text/css" />
<link href="/Public/media/css/default.css" rel="stylesheet" type="text/css" id="style_color" />
<link href="/Public/media/css/uniform.default.css" rel="stylesheet" type="text/css" />
<?php if(($isSelect2 == 1)): ?><link href="/Public/media/css/select2_metro.css" rel="stylesheet" type="text/css"
    media="screen" /><?php endif; ?>
<?php if(($isMutiSelect == 1)): ?><link rel="stylesheet" type="text/css" href="/Public/media/css/multi-select-metro.css"><?php endif; ?>
<?php if(($isLogin == 1)): ?><link href="/Public/media/css/login.css" rel="stylesheet" type="text/css"/><?php endif; ?>
<?php if(($isDatePicker == 1)): ?><link href="/Public/media/css/datepicker.css" rel="stylesheet" type="text/css"/><?php endif; ?>
<?php if(($isFileUpload == 1)): ?><link href="/Public/media/css/jquery.fileupload-ui.css" rel="stylesheet" type="text/css"/><?php endif; ?>
<link href="/Public/media/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="/media/image/favicon.ico" />
<style type="text/css">
    tr{text-align: center;}
    .my-breadcrumb{margin-top: 20px;margin-bottom: 0;}
</style>
<script>
var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "//hm.baidu.com/hm.js?e6ea67101700040a01867aa382be912d";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();
</script>
</head>
<body class="page-header-fixed page-sidebar-fixed">
    <div class="header navbar navbar-inverse navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container-fluid">
            <a class="brand" href="/">
                <img src="/Public/image/logo2.png" alt="logo" />
            </a>
            <a href="javascript:;" class="btn-navbar collapsed" data-toggle="collapse" data-target=".nav-collapse">
                <img src="/Public/media/image/menu-toggler.png" alt="" />
            </a>
            <ul class="nav pull-right">
                <li class="dropdown user"><a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <img alt="" src="/Public<?php echo (session('userImage')); ?>" />
                    &nbsp
                    <span class="username"><?php echo (session('loginUserName')); ?></span><i class="icon-angle-down"></i></a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo U('Public/changepwd');?>"><i class="icon-user"></i>修改密码</a></li>
                        <li><a href="<?php echo U('Public/logout');?>"><i class="icon-key"></i>注销</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>
    <div class="page-container">
        <div class="page-sidebar nav-collapse collapse">
    <ul class="page-sidebar-menu">   
        <li>
            <div class="sidebar-toggler hidden-phone"></div>
        </li>
        <li>
            <form class="sidebar-search">
                <div class="input-box">
                    <a href="javascript:;" class="remove"></a>
                    <input type="text" placeholder="Search...">
                    <input type="button" class="submit" value=" ">
                </div>
            </form>
        </li>            
        <li <?php if(($conInfo["name"]) == "Index"): ?>class="active"<?php endif; ?>>
            <a href="<?php echo U('/');?>">
                <i class="icon-home"></i>
                <span class="title">首页</span>
                <span class="selected"></span>
            </a>
        </li>
        <li <?php if(($conInfo["name"]) == "Auth"): ?>class="active"<?php endif; ?>>
            <a href="javascript:;">
                <i class="icon-wrench"></i>
                <span class="title">站点管理</span>
                <span class="arrow "></span>
            </a>
            <ul class="sub-menu">
                <li>
                    <a href="<?php echo U('Auth/index');?>">授权管理</a>
                </li>
                <li>
                    <a href="<?php echo U('Node/index');?>">节点管理</a>
                </li>
                <li>
                    <a href="<?php echo U('SiteConfig/index');?>">站点管理</a>
                </li>
                 <li>
                    <a href="<?php echo U('User/index');?>">用户管理</a>
                </li>
            </ul>
        </li>
        <li <?php if(($conInfo["name"]) == "Article"): ?>class="active"<?php endif; ?>>
            <a href="javascript:;">
                <i class="icon-file-text"></i>
                <span class="title">内容管理</span>
                <span class="arrow "></span>
            </a>
            <ul class="sub-menu">
                <li>
                    <a href="<?php echo U('Article/add');?>">新增文章</a>
                </li>
                <li>
                    <a href="<?php echo U('Article/index');?>">文章管理</a>
                </li>
                <li>
                    <a href="<?php echo U('Tag/index');?>">标签管理</a>
                </li>
                 <li>
                    <a href="<?php echo U('Category/index');?>">类别管理</a>
                </li>
                <li>
                    <a href="<?php echo U('Url/index');?>">链接管理</a>
                </li>
                <li>
                    <a href="<?php echo U('Comment/index');?>">评论管理</a>
                </li>
                <li>
                    <a href="<?php echo U('Album/index');?>">相册管理</a>
                </li>
                <li>
                    <a href="<?php echo U('Images/index');?>">图片管理</a>
                </li>
            </ul>
        </li>
       <!--  <li <?php if(($conInfo["name"]) == "Finance"): ?>class="active"<?php endif; ?>>
            <a href="javascript:;">
                <i class="icon-money"></i>
                <span class="title">财务</span>
                <span class="arrow "></span>
            </a>
            <ul class="sub-menu">
                <li>
                    <a href="<?php echo U('Finance/add');?>">添加一笔财务</a>
                </li>
                <li>
                    <a href="<?php echo U('Finance/index');?>">财务管理</a>
                </li>
            </ul>
        </li> -->
    </ul>
</div>
        <div class="page-content">
            <div class="container-fluid">
                <div class="row-fluid">
                    <div class="span12">
                        <ul class="breadcrumb my-breadcrumb">
                            <li>
                                <i class="icon-home"></i>
                                <a href="/">Home</a>
                                <i class="icon-angle-right">
                                </i>
                            </li>
                            <?php if(is_array($breadCrumb)): $i = 0; $__LIST__ = $breadCrumb;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
                                    <a href=""><?php echo ($vo); ?></a><?php if(($key) != "1"): ?><i class="icon-angle-right"></i><?php endif; ?>
                                </li><?php endforeach; endif; else: echo "" ;endif; ?>
                        </ul>
                    </div>
                </div>
                <div>
                    <div class="row-fluid">
    <div class="span12">
        <div class="portlet box <?php echo (C("PORLET_COLOR")); ?>">
            <div class="portlet-title">
                <div class="caption">
                    <?php echo (C("EDIT_BUTTON")); echo ($conInfo["remark"]); ?>
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse"></a>
                </div>
            </div>
            <div class="portlet-body form">
                <div class="row-fluid">
                    <div class="span12">
                        <!--BEGIN TABS-->
                        <form action="<?php echo U('Article/update');?>" method="POST" class="form-horizontal updateArticleForm" enctype="multipart/form-data">
                        <div class="tabbable tabbable-custom">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#tab_1_1" data-toggle="tab">文章</a></li>
                                <li><a href="#tab_1_2" data-toggle="tab">类别&标签</a></li>
                                <li><a href="#tab_1_3" data-toggle="tab">标题&关键字&描述</a></li>
                            </ul>
                            <div class="tab-content">
                                    <div class="tab-pane active" id="tab_1_1">
                                        <input type="hidden" name="id" value="<?php echo ($result["id"]); ?>">
                                        <div class="control-group">
                                            <label class="control-label">文章标题</label>
                                            <div class="controls">
                                                <input type="text" placeholder="文章标题" class="m-wrap large"  name="title" value="<?php echo ($result["title"]); ?>"/>
                                                <span class="help-inline">不能为空</span>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">文章封面</label>
                                            <div class="controls">
                                                <input type="file" name="imgFile" />
                                                <span class="help-inline"></span>
                                            </div>
                                            <div class="controls">
                                                <img id="imghead" src="/Public<?php echo ($result["image"]); ?>" <?php if(empty($result["image"])): ?>class="hidden"<?php endif; ?>/>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">文章来源</label>
                                            <div class="controls">
                                                <select name="articlesource">
                                                    <option value="原创" <?php if(($$result["articlesource"]) == "原创"): ?>selected="true"<?php endif; ?>>原创</option>
                                                    <option value="转载" <?php if(($$result["articlesource"]) == "转载"): ?>selected="true"<?php endif; ?>>转载</option>
                                                </select>
                                                <span class="help-inline"></span>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">文章链接</label>
                                            <div class="controls">
                                                <input type="text" placeholder="文章链接" class="m-wrap large"  name="requestPath" value="<?php echo ($urlInfo["requestpath"]); ?>"/>
                                                <span class="help-inline">如果不填,url默认为/article/文章id.html</span>
                                            </div>
                                        </div>
                                         <div class="control-group">
                                            <label class="control-label">文章内容</label>
                                            <div class="controls">
                                                <script id="container" name="content" type="text/plain"><?php echo ($result["content"]); ?></script>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">维护等级</label>
                                            <div class="controls">
                                                <input type="text" placeholder="维护等级" class="m-wrap small" name="maintainorder" value="<?php echo ($result["maintainorder"]); ?>"/>
                                                <span class="help-inline">按什么顺序排序</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="tab_1_2">
                                       <div class="control-group">
                                            <label class="control-label">标签选择</label>
                                            <div class="controls">
                                                <select multiple="multiple" id="tag_multi_select2" data-placeholder="标签选择" class="chosen span3 chzn-done" tabindex="-1"  name="tag_multi_select2[]">
                                                    <?php if(is_array($allTagInfo)): $i = 0; $__LIST__ = $allTagInfo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value='<?php echo ($vo["id"]); ?>' <?php if(($vo["selected"]) == "1"): ?>selected="selected"<?php endif; ?>><?php echo ($vo["displayname"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                                </select>
                                                &nbsp<a href="#" id="addOneTag" class="btn red"><?php echo (C("ADD_BUTTON")); ?>一个新标签</a>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">分类选择</label>
                                            <div class="controls">
                                                <select data-placeholder="分类选择" class="chosen span3 chzn-done" tabindex="-1" id="selOY2" name="categoryid" style="display: none;">
                                                    <option></option>
                                                    <?php if(is_array($allCateInfo)): $i = 0; $__LIST__ = $allCateInfo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><optgroup label="<?php echo ($vo["displayname"]); ?>">
                                                            <?php if(is_array($vo["child"])): $i = 0; $__LIST__ = $vo["child"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vovo): $mod = ($i % 2 );++$i;?><option value='<?php echo ($vovo["id"]); ?>' <?php if(($vovo["id"]) == $categoryid): ?>selected="selected"<?php endif; ?>><?php echo ($vovo["displayname"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                                        </optgroup><?php endforeach; endif; else: echo "" ;endif; ?>
                                                </select>
                                                &nbsp<a href="#" id="addOneCategory" class="btn green"><?php echo (C("ADD_BUTTON")); ?>一个新类别</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="tab_1_3">
                                        <div class="control-group">
                                                <label class="control-label">网页H1</label>
                                            <div class="controls">
                                                <input type="text" placeholder="网页H1" class="m-wrap medium" id="manager" name="pageh1" value="<?php echo ($result["pageh1"]); ?>"/>
                                                <span class="help-inline">与seo有关的网页h1</span>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                                <label class="control-label">网页title</label>
                                            <div class="controls">
                                                <input type="text" placeholder="pagetitle" class="m-wrap medium" id="manager" name="pagetitle" value="<?php echo ($pageMetaInfo["pagetitle"]); ?>"/>
                                                <span class="help-inline">与seo有关的title</span>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                                <label class="control-label">网页keyword</label>
                                            <div class="controls">
                                                <input type="text" placeholder="pagekeyword" class="m-wrap medium" name="pagekeyword" value="<?php echo ($pageMetaInfo["pagekeyword"]); ?>"/>
                                                <span class="help-inline">与seo有关的keyword</span>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                                <label class="control-label">网页description</label>
                                            <div class="controls">
                                                <input type="text" placeholder="pagedescription" class="m-wrap medium" name="pagedescription" value="<?php echo ($pageMetaInfo["pagedescription"]); ?>"/>
                                                <span class="help-inline">与seo有关的description</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                    <a class="btn blue updateArticleBtn"><?php echo (C("SAVE_BUTTON")); ?></a>
                                    <a href="<?php echo U('Article/index');?>" class="btn"><?php echo (C("CANCEL_BUTTON")); ?></a>
                            </div>
                        </form>
                        <!--END TABS-->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <div id="addArticleTag" class="modal hide fade in" style="display: none; ">
        <div class="modal-header">
            <h3>新增标签</h3>
        </div>
        <div class="modal-body form-horizontal">
            <div class="hidden">
                <input type="hidden" value="" name="id" />
            </div>
            <div class="control-group">
                <label class="control-label">
                    displayname</label>
                <div class="controls">
                    <input type="text" placeholder="displayname" class="m-wrap medium"  name="displayname" />
                    <span class="help-inline">不能为空</span>
                </div>  
            </div>
            <div class="control-group">
                    <label class="control-label">
                        displayorder</label>
                <div class="controls">
                    <input type="text" placeholder="displayorder" class="m-wrap medium" name="displayorder" />
                    <span class="help-inline"></span>
                </div>
             </div>
        </div>
        <div class="modal-footer">
            <a href="#" class="btn blue" id="addTagForArticle"><?php echo (C("SAVE_BUTTON")); ?></a>
            <a href="#" class="btn" data-dismiss="modal"><?php echo (C("CANCEL_BUTTON")); ?></a>
        </div>
</div>
<div class="container">
    <div id="addArticleCategory" class="modal hide fade in" style="display: none; ">
        <div class="modal-header">
            <h3>新增类别</h3>
        </div>
        <div class="modal-body form-horizontal">
            <div class="control-group">
                <label class="control-label">
                    displayname</label>
                <div class="controls">
                    <input type="text" placeholder="displayname" class="m-wrap medium"  name="displayname" />
                    <span class="help-inline">不能为空</span>
                </div>  
            </div>
            <div class="control-group">
                <label class="control-label">
                    parentcategoryid</label>
                <div class="controls">
                    <input type="text" placeholder="parentcategoryid" class="m-wrap medium" name="parentcategoryid" />
                    <span class="help-inline"></span>
                </div>
            </div>
            <div class="control-group">
                    <label class="control-label">
                        displayorder</label>
                <div class="controls">
                    <input type="text" placeholder="displayorder" class="m-wrap medium" name="displayorder" />
                    <span class="help-inline"></span>
                </div>
             </div>
        </div>
        <div class="modal-footer">
            <a href="#" class="btn blue" id="addCategoryForArticle"><?php echo (C("SAVE_BUTTON")); ?></a>
            <a href="#" class="btn" data-dismiss="modal"><?php echo (C("CANCEL_BUTTON")); ?></a>
        </div>
</div>
<div id="alert-modal" class="modal hide fade in" style="display: none; ">
    <div class="modal-header">
        <h3 class="alert-data-title"></h3>
    </div>
</div>

                </div>
              </div>
            </div>
        </div>
    </div>
    <div class="footer">
    <div class="footer-inner">
        <?php echo date('Y');?> &copy; <?php echo (C("BACKEND_SITE")); ?>. Version:<?php echo (C("SITE_VERSION")); ?>
    </div>
    <div class="footer-tools">
        <span class="go-top"><i class="icon-angle-up"></i></span>
    </div>
</div>

    <script src="/Public/media/js/jquery-1.10.1.min.js" type="text/javascript"></script>
<script src="/Public/media/js/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
<script src="/Public/media/js/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
<script src="/Public/media/js/bootstrap.min.js" type="text/javascript"></script>
<!--[if lt IE 9]>
<script src="/Public/media/js/excanvas.min.js"></script>
<script src="/Public/media/js/respond.min.js"></script>  
<![endif]-->
<script src="/Public/media/js/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="/Public/media/js/jquery.blockui.min.js" type="text/javascript"></script>
<script src="/Public/media/js/jquery.cookie.min.js" type="text/javascript"></script>
<script src="/Public/media/js/jquery.uniform.min.js" type="text/javascript"></script>
<script src="/Public/media/js/app.js" type="text/javascript"></script>
<?php if(($isSelect2 == 1)): ?><script src="/Public/media/js/select2.min.js"></script><?php endif; ?>
<?php if(($isMutiSelect == 1)): ?><script src="/Public/media/js/jquery.multi-select.js"></script><?php endif; ?>
<?php if(($isModal == 1)): ?><script src="/Public/media/js/bootstrap-modal.js"></script>
<script src="/Public/media/js/bootstrap-modalmanager.js"></script><?php endif; ?>
<?php if(($isDatePicker == 1)): ?><script src="/Public/media/js/bootstrap-datepicker.js"></script><?php endif; ?>
<?php if(($isEcharts == 1)): ?><script src="http://echarts.baidu.com/build/dist/echarts.js"></script><?php endif; ?>
<?php if(($isUeditor == 1)): ?><script src="/Public/plugins/ueditor/ueditor.config.js?12312=1231"></script>
<script src="/Public/plugins/ueditor/ueditor.all.js"></script><?php endif; ?>
<script src="/Public/media/js/jquery.1.10.12.dataTables.min.js" type="text/javascript"></script>
<script src="/Public/own-js/base.js" type="text/javascript"></script>
<script src="/Public/own-js/<?php echo (CONTROLLER_NAME); ?>/<?php echo (strtolower(ACTION_NAME)); ?>.js?ver=<?php echo C('VER');?>" type="text/javascript"></script>
 <script type="text/javascript">
    $(function () {
        App.init();
    });
</script>
</body>
</html>