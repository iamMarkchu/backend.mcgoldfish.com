<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SB Admin 2 - Bootstrap Admin Theme</title>

    <!-- Bootstrap Core CSS -->
    <link href="/Public/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="/Public/dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="/Public/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-inverse navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.html"><img src="/Public/image/logo2.png" alt=""></a>
            </div>
            <!-- /.navbar-header -->

            <ul class="nav navbar-top-links navbar-right">
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="#"><i class="fa fa-user fa-fw"></i> User Profile</a>
                        </li>
                        <li><a href="#"><i class="fa fa-gear fa-fw"></i> Settings</a>
                        </li>
                        <li class="divider"></li>
                        <li><a href="login.html"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                        </li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links -->

            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li>
                            <a href="<?php echo U('/index/index');?>"> 首页</a>
                        </li>
                        <li>
                            <a href="#">站点管理<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="<?php echo U('user/index');?>">用户管理</a>
                                </li>
                                <li>
                                    <a href="<?php echo U('role/index');?>">组别管理</a>
                                </li>
                                <li>
                                    <a href="<?php echo U('Node/index');?>">节点管理</a>
                                </li>
                                <li>
                                    <a href="<?php echo U('SiteConfig/index');?>">站点管理</a>
                                </li>
                                 
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                        <li>
                            <a href="#">站点管理<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="<?php echo U('user/index');?>">用户管理</a>
                                </li>
                                <li>
                                    <a href="<?php echo U('role/index');?>">组别管理</a>
                                </li>
                                <li>
                                    <a href="<?php echo U('Node/index');?>">节点管理</a>
                                </li>
                                <li>
                                    <a href="<?php echo U('SiteConfig/index');?>">站点管理</a>
                                </li>
                                 
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>

        <!-- Page Content -->
        <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        
<h3 class="page-title">用户管理</h3>
<div class="panel panel-default">
    <div class="panel-heading">用户列表</div>
    <div class="panel-body">
        <a href="<?php echo U('/user/add');?>" class="btn btn-success"><?php echo (C("ADD_BUTTON")); ?>用户</a>
    </div>
    <div class="panel-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>
                        ID
                    </th>
                    <th>
                        account
                    </th>
                    <th>
                        nickname
                    </th>
                    <th>
                        last_login_time
                        <br/>
                        last_login_ip
                    </th>
                     <th>
                        login_count
                    </th>
                    <th>
                        email
                    </th>
                    <th>
                        remark
                    </th>
                    <th>
                        status
                    </th>
                    <th>
                        operation
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$user): $mod = ($i % 2 );++$i;?><tr>
                        <td><?php echo ($user['id']); ?></td>
                        <td><?php echo ($user['account']); ?></td>
                        <td><?php echo ($user['nickname']); ?></td>
                        <td>
                            <?php echo ($user["['last_login_time']"]); ?>
                            <br/>
                            <?php echo ($user['last_login_ip']); ?>
                        </td>
                        <td><?php echo ($user['login_count']); ?></td>
                        <td><?php echo ($user['email']); ?></td>
                        <td><?php echo ($user['remark']); ?></td>
                        <td><?php echo ($user['status']); ?></td>
                        <td>
                            <a href="<?php echo U('user/edit', ['id' => $user['id']]);?>">编辑</a>
                            <a href="<?php echo U('user/delete', ['id' => $user['id']]);?>">删除</a>
                        </td>
                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody>
        </table>
        <?php echo ($show); ?>
    </div>
</div>

                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->
    <script src="/Public/vendor/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="/Public/vendor/bootstrap/js/bootstrap.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="/Public/dist/js/sb-admin-2.js"></script>

</body>

</html>