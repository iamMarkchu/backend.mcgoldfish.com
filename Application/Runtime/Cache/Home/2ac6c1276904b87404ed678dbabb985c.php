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
                    <!-- BEGIN SEARCHBAR-->
<div class="row-fluid">
    <div class="span12">
        <div class="portlet box <?php echo (C("PORLET_COLOR")); ?>">
            <div class="portlet-title">
                <div class="caption">
                    <?php echo (C("SEARCH_AERA")); ?>
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse"></a>
                </div>
            </div>
            <div class="portlet-body form" >
                <div class="row-fluid">
                    <!--BEGIN SEARCH-->
                    <input type="hidden" id="noAjax" value="<?php echo ($noAjax); ?>"/>
                    <div class="span2">
                        <div class="controls">
                            <input type="text" id="search_id_or_title" name="search_id_or_title" class="m-wrap small" placeholder="id或者title" value="<?php echo ($searchArray["where"]["titleOrId"]); ?>" />
                        </div>
                    </div>
                    <div class="span2">
                        <div class="controls">
                              <select id="selectarticlesource" class="m-wrap small" placeholder="文章来源">
                                  <option value="-1">文章来源</option>
                                  <option value="原创" <?php if(($searchArray["where"]["articlesource"]) == "原创"): ?>selected="true"<?php endif; ?>>原创</option>
                                  <option value="转载" <?php if(($searchArray["where"]["articlesource"]) == "转载"): ?>selected="true"<?php endif; ?>>转载</option>
                              </select>
                        </div>
                    </div>
                    <div class="span2">
                        <div class="controls">
                              <select id="selectstatus" class="m-wrap small" placeholder="文章状态">
                                  <option value="-1">文章状态</option>
                                  <option value="active" <?php if(($searchArray["where"]["status"]) == "active"): ?>selected="selected"<?php endif; ?>>active</option>
                                  <option value="deleted" <?php if(($searchArray["where"]["status"]) == "deleted"): ?>selected="selected"<?php endif; ?>>deleted</option>
                                  <option value="republish" <?php if(($searchArray["where"]["status"]) == "republish"): ?>selected="selected"<?php endif; ?>>republish</option>
                              </select>
                        </div>
                    </div>
                    <div class="span2">
                        <div class="controls">
                              <select id="selectorderby" class="m-wrap small" placeholder="文章排序">
                                  <option value="-1">文章排序</option>
                                  <option value="id"  <?php if(($searchArray["order"]) == "id"): ?>selected="selected"<?php endif; ?>>by id asc</option>
                                  <option value="id desc" <?php if(($searchArray["order"]) == "id desc"): ?>selected="selected"<?php endif; ?>>by id desc</option>
                                  <option value="maintainorder asc" <?php if(($searchArray["order"]) == "maintainorder asc"): ?>selected="selected"<?php endif; ?>>by maintainorder asc</option>
                                  <option value="maintainorder desc" <?php if(($searchArray["order"]) == "maintainorder desc"): ?>selected="selected"<?php endif; ?>>by maintainorder desc</option>
                              </select>
                        </div>
                    </div>
                    <div class="span1">
                        <div class="btn-group">
                            <button class="btn <?php echo (C("SEARCH_BUTTON_COLOR")); ?>" id="searchbutton"><?php echo (C("SEARCH_BUTTON")); ?></button>
                        </div>
                    </div>
                </div>
                    <!--END SEARCH-->
            </div>
        </div>
    </div>
</div>
<!-- END SEARCHBAR-->
<!-- BEGIN CONTENT-->
<div class="row-fluid">
    <div class="span12">
        <div class="portlet box <?php echo (C("PORLET_COLOR")); ?>">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-list"></i><?php echo ($conInfo["remark"]); echo (C("LISTNAME")); ?>
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse"></a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row-fluid">
                    <div class="span12">
                         <div class="btn-group">
                            <button class="btn green" id="<?php echo (C("ADDNAME")); echo ($conInfo["name"]); ?>"><?php echo (C("ADD_BUTTON")); echo ($conInfo["remark"]); ?></button>
                         </div>
                         <div class="btn-group">
                            <button class="btn" id="<?php echo (C("EDITNAME")); echo ($conInfo["name"]); ?>"><?php echo (C("EDIT_BUTTON")); echo ($conInfo["remark"]); ?></button>
                         </div>
                         <div class="btn-group">
                            <button class="btn" id="<?php echo (C("DELETENAME")); echo ($conInfo["name"]); ?>"><?php echo (C("DELETE_BUTTON")); echo ($conInfo["remark"]); ?></button>
                         </div>
                    </div>
                </div>
                <table class="display" id="tbartilceList">
                    <thead>
                        <tr>
                            <th>
                                编号
                            </th>
                            <th>
                                标题
                            </th>
                            <th>
                                状态
                            </th>
                            <th>
                                点击次数
                            </th>
                            <th>
                                文章来源
                            </th>
                            <th>
                                维护等级
                            </th>
                            <th>
                                编辑
                            </th>
                            <th>
                                所属类别
                                <br>
                                所属标签
                            </th>
                            <th>
                                添加时间
                                <br>
                                最后更新时间
                            </th>
                            <th>
                                操作
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- END CONTENT-->
<div class="container">
    <div id="editArticleUrl" class="modal hide fade in" style="display: none; ">
        <div class="modal-header">
            <h3>编辑文章链接</h3>
        </div>
        <div class="modal-body form-horizontal">
            <div class="hidden">
                <input type="hidden" value="" name="id" />
            </div>
            <div class="control-group">
                <label class="control-label">
                    requestpath</label>
                <div class="controls">
                    <input type="text" placeholder="RequestPath" class="m-wrap medium"  name="requestpath" value="" />
                    <span class="help-inline">不能为空</span>
                </div>
             </div>
             <div class="control-group">
                <label class="control-label">
                    modeltype</label>
                <div class="controls">
                    <select name="modeltype">
                        <option value="article">文章</option>
                        <option value="category">类别</option>
                        <option value="tag">标签</option>
                    </select>
                    <span class="help-inline"></span>
                </div>
             </div>
            <div class="control-group">
                <label class="control-label">
                    jumptype</label>
                <div class="controls">
                    <select name="isjump">
                        <option value="NO">NO</option>
                        <option value="301">301</option>
                        <option value="302">302</option>
                        <option value="404">404</option>
                    </select>
                    <span class="help-inline"></span>
                </div>
            </div>  
            <div class="control-group">
                <label class="control-label">
                    Status</label>
                <div class="controls">
                    <select name="status">
                        <option value="yes">yes</option>
                        <option value="no">no</option>
                    </select>
                    <span class="help-inline"></span>
                </div>
            </div> 
        </div>
        <div class="modal-footer">
            <a href="#" class="btn blue" id="updateUrlForArticle"><?php echo (C("SAVE_BUTTON")); ?></a>
            <a href="#" class="btn" data-dismiss="modal"><?php echo (C("CANCEL_BUTTON")); ?></a>
        </div>
</div>
<div id="delete-modal" class="modal hide fade in" style="display: none; ">
    <div class="modal-header">
        <h3>确认删除?</h3>
        <input type="hidden" name="<?php echo (strtolower($conInfo["name"])); ?>id" value="" />
    </div>
    <div class="modal-footer">
        <a href="#" class="btn blue" id="deleteFor<?php echo ($conInfo["name"]); ?>"><?php echo (C("COMFIRM_BUTTON")); ?></a>
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