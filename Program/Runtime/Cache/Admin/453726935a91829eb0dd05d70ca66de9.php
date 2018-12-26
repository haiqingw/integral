<?php if (!defined('THINK_PATH')) exit();?><script src="/Public/dwzUI/js/jquery-1.8.3.min.js" type="text/javascript"></script>
<script type="text/javascript">
    $.post("<?php echo U('Admin/Login/redirect');?>",'',function(data) {
        window.location = "<?php echo U('Admin/Login/index');?>";
    });
</script>