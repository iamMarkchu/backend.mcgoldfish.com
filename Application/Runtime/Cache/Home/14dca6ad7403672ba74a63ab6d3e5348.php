<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<!-- 

Template Name: Metronic - Responsive Admin Dashboard Template build with Twitter Bootstrap 2.3.1

Version: 1.3

Author: KeenThemes

Website: http://www.keenthemes.com/preview/?theme=metronic

Purchase: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469

-->
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
    <meta charset="utf-8" />
    <title>Metronic | Admin Dashboard Template</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta content="" name="description" />
    <meta content="" name="author" />
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="/Public/media/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="/Public/media/css/bootstrap-responsive.min.css" rel="stylesheet" type="text/css" />
    <link href="/Public/media/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="/Public/media/css/style-metro.css" rel="stylesheet" type="text/css" />
    <link href="/Public/media/css/style.css" rel="stylesheet" type="text/css" />
    <link href="/Public/media/css/style-responsive.css" rel="stylesheet" type="text/css" />
    <link href="/Public/media/css/default.css" rel="stylesheet" type="text/css" id="style_color" />
    <link href="/Public/media/css/uniform.default.css" rel="stylesheet" type="text/css" />
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL STYLES -->
    <link href="/Public/media/css/jquery.gritter.css" rel="stylesheet" type="text/css" />
    <link href="/Public/media/css/daterangepicker.css" rel="stylesheet" type="text/css" />
    <link href="/Public/media/css/fullcalendar.css" rel="stylesheet" type="text/css" />
    <link href="/Public/media/css/jqvmap.css" rel="stylesheet" type="text/css" media="screen" />
    <link href="/Public/media/css/jquery.easy-pie-chart.css" rel="stylesheet" type="text/css"
        media="screen" />
    <link href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL STYLES -->
    <link rel="shortcut icon" href="/Public/media/image/favicon.ico" />
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="page-header-fixed">
    <!-- BEGIN HEADER -->
    <div class="header navbar navbar-inverse navbar-fixed-top">
        <!-- BEGIN TOP NAVIGATION BAR -->
        <div class="navbar-inner">
            <div class="container-fluid">
                <!-- BEGIN LOGO -->
                <a class="brand" href="index.html">
                    <img src="/Public/media/image/logo.png" alt="logo" />
                </a>
                <!-- END LOGO -->
                <!-- BEGIN RESPONSIVE MENU TOGGLER -->
                <a href="javascript:;" class="btn-navbar collapsed" data-toggle="collapse" data-target=".nav-collapse">
                    <img src="/Public/media/image/menu-toggler.png" alt="" />
                </a>
                <!-- END RESPONSIVE MENU TOGGLER -->
                <!-- BEGIN TOP NAVIGATION MENU -->
                <ul class="nav pull-right">
                    <!-- BEGIN NOTIFICATION DROPDOWN -->
                    <li class="dropdown" id="header_notification_bar"><a href="#" class="dropdown-toggle"
                        data-toggle="dropdown"><i class="icon-warning-sign"></i><span class="badge">6</span>
                    </a>
                        <ul class="dropdown-menu extended notification">
                            <li>
                                <p>
                                    You have 14 new notifications</p>
                            </li>
                            <li><a href="#"><span class="label label-success"><i class="icon-plus"></i></span>New
                                user registered. <span class="time">Just now</span> </a></li>
                            <li><a href="#"><span class="label label-important"><i class="icon-bolt"></i></span>
                                Server #12 overloaded. <span class="time">15 mins</span> </a></li>
                            <li><a href="#"><span class="label label-warning"><i class="icon-bell"></i></span>Server
                                #2 not respoding. <span class="time">22 mins</span> </a></li>
                            <li><a href="#"><span class="label label-info"><i class="icon-bullhorn"></i></span>Application
                                error. <span class="time">40 mins</span> </a></li>
                            <li><a href="#"><span class="label label-important"><i class="icon-bolt"></i></span>
                                Database overloaded 68%. <span class="time">2 hrs</span> </a></li>
                            <li><a href="#"><span class="label label-important"><i class="icon-bolt"></i></span>
                                2 user IP blocked. <span class="time">5 hrs</span> </a></li>
                            <li class="external"><a href="#">See all notifications <i class="m-icon-swapright"></i>
                            </a></li>
                        </ul>
                    </li>
                    <!-- END NOTIFICATION DROPDOWN -->
                    <!-- BEGIN INBOX DROPDOWN -->
                    <li class="dropdown" id="header_inbox_bar"><a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="icon-envelope"></i><span class="badge">5</span> </a>
                        <ul class="dropdown-menu extended inbox">
                            <li>
                                <p>
                                    You have 12 new messages</p>
                            </li>
                            <li><a href="inbox.html?a=view"><span class="photo">
                                <img src="/Public/media/image/avatar2.jpg" alt="" /></span> <span class="subject"><span class="from">
                                    Lisa Wong</span> <span class="time">Just Now</span> </span><span class="message">Vivamus
                                        sed auctor nibh congue nibh. auctor nibh auctor nibh... </span></a></li>
                            <li><a href="inbox.html?a=view"><span class="photo">
                                <img src=".//Public/media/image/avatar3.jpg" alt="" /></span> <span class="subject"><span
                                    class="from">Richard Doe</span> <span class="time">16 mins</span> </span><span class="message">
                                        Vivamus sed congue nibh auctor nibh congue nibh. auctor nibh auctor nibh...
                                </span></a></li>
                            <li><a href="inbox.html?a=view"><span class="photo">
                                <img src=".//Public/media/image/avatar1.jpg" alt="" /></span> <span class="subject"><span
                                    class="from">Bob Nilson</span> <span class="time">2 hrs</span> </span><span class="message">
                                        Vivamus sed nibh auctor nibh congue nibh. auctor nibh auctor nibh... </span>
                            </a></li>
                            <li class="external"><a href="inbox.html">See all messages <i class="m-icon-swapright">
                            </i></a></li>
                        </ul>
                    </li>
                    <!-- END INBOX DROPDOWN -->
                    <!-- BEGIN TODO DROPDOWN -->
                    <li class="dropdown" id="header_task_bar"><a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="icon-tasks"></i><span class="badge">5</span> </a>
                        <ul class="dropdown-menu extended tasks">
                            <li>
                                <p>
                                    You have 12 pending tasks</p>
                            </li>
                            <li><a href="#"><span class="task"><span class="desc">New release v1.2</span> <span
                                class="percent">30%</span> </span><span class="progress progress-success "><span
                                    style="width: 30%;" class="bar"></span></span></a></li>
                            <li><a href="#"><span class="task"><span class="desc">Application deployment</span>
                                <span class="percent">65%</span> </span><span class="progress progress-danger progress-striped active">
                                    <span style="width: 65%;" class="bar"></span></span></a></li>
                            <li><a href="#"><span class="task"><span class="desc">Mobile app release</span> <span
                                class="percent">98%</span> </span><span class="progress progress-success"><span style="width: 98%;"
                                    class="bar"></span></span></a></li>
                            <li><a href="#"><span class="task"><span class="desc">Database migration</span> <span
                                class="percent">10%</span> </span><span class="progress progress-warning progress-striped">
                                    <span style="width: 10%;" class="bar"></span></span></a></li>
                            <li><a href="#"><span class="task"><span class="desc">Web server upgrade</span> <span
                                class="percent">58%</span> </span><span class="progress progress-info"><span style="width: 58%;"
                                    class="bar"></span></span></a></li>
                            <li><a href="#"><span class="task"><span class="desc">Mobile development</span> <span
                                class="percent">85%</span> </span><span class="progress progress-success"><span style="width: 85%;"
                                    class="bar"></span></span></a></li>
                            <li class="external"><a href="#">See all tasks <i class="m-icon-swapright"></i></a>
                            </li>
                        </ul>
                    </li>
                    <!-- END TODO DROPDOWN -->
                    <!-- BEGIN USER LOGIN DROPDOWN -->
                    <li class="dropdown user"><a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img alt="" src="/Public/media/image/avatar1_small.jpg" />
                        <span class="username">Bob Nilson</span> <i class="icon-angle-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="extra_profile.html"><i class="icon-user"></i>My Profile</a></li>
                            <li><a href="page_calendar.html"><i class="icon-calendar"></i>My Calendar</a></li>
                            <li><a href="inbox.html"><i class="icon-envelope"></i>My Inbox(3)</a></li>
                            <li><a href="#"><i class="icon-tasks"></i>My Tasks</a></li>
                            <li class="divider"></li>
                            <li><a href="extra_lock.html"><i class="icon-lock"></i>Lock Screen</a></li>
                            <li><a href="login.html"><i class="icon-key"></i>Log Out</a></li>
                        </ul>
                    </li>
                    <!-- END USER LOGIN DROPDOWN -->
                </ul>
                <!-- END TOP NAVIGATION MENU -->
            </div>
        </div>
        <!-- END TOP NAVIGATION BAR -->
    </div>
    <!-- END HEADER -->
    <!-- BEGIN CONTAINER -->
    <div class="page-container">
         <!-- BEGIN SIDEBAR -->
        <div class="page-sidebar nav-collapse collapse">
            <!-- BEGIN SIDEBAR MENU -->
            <ul class="page-sidebar-menu">               
                <li class="active "><a href="index.html"><i class="icon-home"></i><span class="title">
                    Dashboard</span> <span class="selected"></span></a></li>
                <li class="">
                    <a href="javascript:;">
                        <i class="icon-cogs"></i>
                        <span class="title">User</span>
                        <span class="arrow "></span>
                    </a>
                    <ul class="sub-menu">
                        <li>
                            <a href="<?php echo U('User/add');?>">Add User</a>
                        </li>
                        <li>
                            <a href="<?php echo U('User/index');?>">User Management</a>
                        </li>
                    </ul>
                </li>
                <li class="">
                    <a href="javascript:;">
                        <i class="icon-cogs"></i>
                        <span class="title">Article</span>
                        <span class="arrow "></span>
                    </a>
                    <ul class="sub-menu">
                        <li>
                            <a href="<?php echo U('Article/add');?>">Add Article</a>
                        </li>
                        <li>
                            <a href="<?php echo U('Article/index');?>">Article Management</a>
                        </li>
                    </ul>
                </li>
                <li class="">
                    <a href="javascript:;">
                        <i class="icon-cogs"></i>
                        <span class="title">Url</span>
                        <span class="arrow "></span>
                    </a>
                    <ul class="sub-menu">
                        <li>
                            <a href="<?php echo U('Url/add');?>">Add Url</a>
                        </li>
                        <li>
                            <a href="<?php echo U('Url/index');?>">Url Management</a>
                        </li>
                    </ul>
                </li>
                <li class="">
                    <a href="javascript:;">
                        <i class="icon-cogs"></i>
                        <span class="title">Category</span>
                        <span class="arrow "></span>
                    </a>
                    <ul class="sub-menu">
                        <li>
                            <a href="<?php echo U('Category/add');?>">Add Category</a>
                        </li>
                        <li>
                            <a href="<?php echo U('Category/index');?>">Category Management</a>
                        </li>
                    </ul>
                </li>
                <li class="last "><a href="charts.html"><i class="icon-bar-chart"></i><span class="title">
                    Visual Charts</span> </a></li>
            </ul>
            <!-- END SIDEBAR MENU -->
        </div>
        <!-- END SIDEBAR -->
        <!-- BEGIN PAGE -->
        <div class="page-content">
            <!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
            <div id="portlet-config" class="modal hide">
                <div class="modal-header">
                    <button data-dismiss="modal" class="close" type="button">
                    </button>
                    <h3>
                        Widget Settings</h3>
                </div>
                <div class="modal-body">
                    Widget settings form goes here
                </div>
            </div>
            <!-- END SAMPLE PORTLET CONFIGURATION MODAL FORM-->
            <!-- BEGIN PAGE CONTAINER-->
            <div class="container-fluid">
                <!-- BEGIN PAGE HEADER-->
                <div class="row-fluid">
                    <div class="span12">
                        <!-- BEGIN STYLE CUSTOMIZER -->
                        <div class="color-panel hidden-phone">
                            <div class="color-mode-icons icon-color">
                            </div>
                            <div class="color-mode-icons icon-color-close">
                            </div>
                            <div class="color-mode">
                                <p>
                                    THEME COLOR</p>
                                <ul class="inline">
                                    <li class="color-black current color-default" data-style="default"></li>
                                    <li class="color-blue" data-style="blue"></li>
                                    <li class="color-brown" data-style="brown"></li>
                                    <li class="color-purple" data-style="purple"></li>
                                    <li class="color-grey" data-style="grey"></li>
                                    <li class="color-white color-light" data-style="light"></li>
                                </ul>
                                <label>
                                    <span>Layout</span>
                                    <select class="layout-option m-wrap small">
                                        <option value="fluid" selected>Fluid</option>
                                        <option value="boxed">Boxed</option>
                                    </select>
                                </label>
                                <label>
                                    <span>Header</span>
                                    <select class="header-option m-wrap small">
                                        <option value="fixed" selected>Fixed</option>
                                        <option value="default">Default</option>
                                    </select>
                                </label>
                                <label>
                                    <span>Sidebar</span>
                                    <select class="sidebar-option m-wrap small">
                                        <option value="fixed">Fixed</option>
                                        <option value="default" selected>Default</option>
                                    </select>
                                </label>
                                <label>
                                    <span>Footer</span>
                                    <select class="footer-option m-wrap small">
                                        <option value="fixed">Fixed</option>
                                        <option value="default" selected>Default</option>
                                    </select>
                                </label>
                            </div>
                        </div>
                        <!-- END BEGIN STYLE CUSTOMIZER -->
                        <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                        <h3 class="page-title">
                            <?php echo ($blockName); ?>
                        </h3>
                        <ul class="breadcrumb">
                            <li><i class="icon-home"></i><a href="index.html">Home</a> <i class="icon-angle-right">
                            </i></li>
                            <li><a href="#">Dashboard</a></li>
                        </ul>
                        <!-- END PAGE TITLE & BREADCRUMB-->
                    </div>
                </div>
                <!-- END PAGE HEADER-->
                <div id="dashboard">
                    <div class="row-fluid">
    <div class="span12">
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-plus"></i>新增文章
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse"></a>
                </div>
            </div>
            <div class="portlet-body form">
                <div class="row-fluid">
                    <div class="span12">
                        <div class="tab-pane">
                            <!-- BEGIN FORM-->
                            <form action="<?php echo U('/Home/Url/insert');?>" method="POST" class="form-horizontal" enctype="multipart/form-data">
                                 <div class="control-group">
                                        <label class="control-label">
                                            RequestPath</label>
                                    <div class="controls">
                                        <input type="text" placeholder="RequestPath" class="m-wrap medium"  name="RequestPath" />
                                        <span class="help-inline">不能为空</span>
                                    </div>
                                 </div>
                                 <div class="control-group">
                                        <label class="control-label">
                                            OptDataId</label>
                                    <div class="controls">
                                        <input type="text" placeholder="OptDataId" class="m-wrap medium" name="OptDataId" />
                                        <span class="help-inline"></span>
                                    </div>
                                 </div>
                                 <div class="control-group">
                                        <label class="control-label">
                                            ModelType</label>
                                    <div class="controls">
                                        <select name="ModelType">
                                            <option value="文章">文章</option>
                                            <option value="转载">123</option>
                                            <option value="转载">转载</option>
                                            <option value="转载">转载</option>
                                        </select>
                                        <span class="help-inline"></span>
                                    </div>
                                 </div>
                                    <div class="control-group">
                                        <label class="control-label">
                                            JumpType</label>
                                        <div class="controls">
                                            <select name="IsJump">
                                                <option value="301">301</option>
                                                <option value="302">302</option>
                                                <option value="404">404</option>
                                                <option value="NO">NO</option>
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
                                <div class="form-actions">
                                    <button type="submit" class="btn blue">
                                        <i class="icon-ok"></i>保存</button>
                                    <a href="<?php echo U('/Home/Article/index');?>" class="btn">取消</a>
                                </div>
                            </form>
                            <!-- END FORM-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

                </div>
            </div>
            <!-- END PAGE CONTAINER-->
        </div>
        <!-- END PAGE -->
    </div>
    <!-- END CONTAINER -->
    <!-- BEGIN FOOTER -->
    <div class="footer">
        <div class="footer-inner">
            2013 &copy; Metronic by keenthemes.
        </div>
        <div class="footer-tools">
            <span class="go-top"><i class="icon-angle-up"></i></span>
        </div>
    </div>
    <!-- END FOOTER -->
    <!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
    <!-- BEGIN CORE PLUGINS -->
    <script src="/Public/media/js/jquery-1.10.1.min.js" type="text/javascript"></script>
    <script src="/Public/media/js/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
    <!-- IMPORTANT! Load jquery-ui-1.10.1.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
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
    <!-- END CORE PLUGINS -->
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js" type="text/javascript"></script>
    <script src="/Public/media/js/app.js" type="text/javascript"></script>
    <script src="/Public/own-js/base.js" type="text/javascript"></script>
    <?php if(($isEditor == 1)): ?><script charset="utf-8" src="/Public/kindeditor/kindeditor-all-min.js"></script>
    <script charset="utf-8" src="/Public/kindeditor/lang/zh-CN.js"></script>
    <script type="text/javascript">
        KindEditor.ready(function(K) {
                window.editor = K.create('#articleContent');
        });
    </script><?php endif; ?>
    <script src="/Public/own-js/<?php echo (CONTROLLER_NAME); ?>.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL SCRIPTS -->
     <script type="text/javascript">
        $(function () {
            App.init(); // initlayout and core plugins
        });
    </script>
    <!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>