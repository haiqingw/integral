<?php
/**
 * +-------------------------------------------
 * | Description: 网页版接口 公共js管理
 * +-------------------------------------------
 * | Author HaiQing.Wu <398994668@qq.com>
 * +-----------------------------------------------------
 * | Date :  2018年6月19日 下午12:14:33
 * +-----------------------------------------------------
 * | Filename: WebjsmgController.class.php
 * +-----------------------------------------------------
 */
namespace App\Controller;
use Think\Controller;
class WebjsmgController extends BaseController{
	/**
	 * 资讯页面js加载
	 * @date: 2018年6月15日 下午12:08:16
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param:
	 * @return:
	 */
	public function loading_infor_js(){
		$str = "";
		$str .= "<script>";
		$str .= "	var \$lens = 0;";
		$str .= "	var \$limit = 5;";
		$str .= "	var id = $('.active').attr('data-id');";
		$str .= "	var \$page = 1;";
		$str .= "	var items = '';";
		$str .= "	$(function(){";
		$str .= "		$('.classifyTap').delegate('a','click',function(){";
		$str .= " 		   \$page = 1;";
		$str .= "			$(this).addClass('active').siblings('a').removeClass('active');";
		$str .= "			$('a.loadingMoreBtn').addClass('loadingMoreBtn').text('更多加载');";
		$str .= "			$('.loadingMoreBtn').removeAttr('style').removeClass('noDataLoading');";
		$str .= "			items = '';";
		$str .= "			$('newsListMain').empty();";
		$str .= " 			id = $(this).attr('data-id');";
		$str .= "			getdata();";
		//$str .= "			var id=$(this).attr('data-id');";
		$str .= "	   });";
		//分类大于四个的时候，上下结构变为左右结构
		$str .= "	   var len = $('.classifyTap a').length;";
		$str .= "	   if(len>4){";
		$str .= "			$('.classifyTap').removeClass('flex').addClass('verticalTab');";
		$str .= "			$('.classifyTap a').removeClass('line_right').addClass('line_bottom');";
		$str .= "			$('.newsListContainer').addClass('verticalNewsList');";
		$str .= "	   }";
		$str .= "	   $('.classifyTap').find('a.active').trigger('click');";
		//详情页点击返回按钮
		$str .= "	   $('#headerLeftBtn').click(function(){";
		$str .= "	   		$('.msgdetailBody').html();";
		$str .= "			$('.msgdetail').hide();";
		$str .= "	   });";
		//点击加载更多
		$str .= " 	   $('.loadingMoreBtn').click(function () {";
		$str .= "			\$page++;";
		$str .= "			 getdata();";
		$str .= "	   });";
		//请求数据
		$str .= "   });";
		//获取列表数据
		$str .= "  function getdata(){";
		$str .= "		$.ajax({";
		$str .= "			url:'" . BASEURL . "/index.php/App/Weboutputmg/get_list_info',";
		$str .= '			type:"post",';
		$str .= '			dataType:"json",';
		$str .= "			data:{id:id,page:\$page,limit:\$limit},";
		$str .= "			success:function(ret){";
		$str .= " 			   \$lens = ret.count;";
		$str .= "				if(!ret.status && \$page == 1){";
		$str .= "					items += '<div class=\"noDataView\" style=\"display:block;\"><img src=\"" . BASEURL . "/Public/infor/images/noDataIcon.png\" alt=\"暂无数据\"><p>暂无数据</p></div>';";
		$str .= "				}else{";
		$str .= " 					if (!ret.status) {";
		$str .= "						$(\"a.loadingMoreBtn\").addClass('noDataLoading').text('已经没有了').css({ 'background': '#f1f1f1', 'color': '#333', 'margin': '0 auto', 'lineHeight': '16px'});";
		$str .= "					}";
		//列表循环
		$str .= "					for(i in ret.data){";
		$str .= "						items += '<div class=\"newsListItem\">';";
		$str .= "						items += '<a href=\"javascript:;\" id=\"details\" onclick=\"details('+ret.data[i].cid+')\">';";
		$str .= "						items += '<h3>'+ret.data[i].title+'</h3>';";
		$str .= "						items += '<img src=\"'+ret.data[i].imgurl+'\" alt=\"新闻列表\">';";
		$str .= "						items += '<p><time>'+ret.data[i].time+'</time><em><img src=\"" . BASEURL . "/Public/infor/images/eyeIcon.png\" alt=\"阅读次数\">'+ret.data[i].views+'</em></p>';";
		$str .= "						items += '</a>';";
		$str .= "						items += '</div>';";
		$str .= "					}";
		$str .= "					if (ret.status) {";
		$str .= "						if (ret.data.length < 5) {";
		$str .= "							$(\"a.loadingMoreBtn\").addClass('noDataLoading').text('已经没有了').css({ 'background': '#f1f1f1', 'color': '#333', 'margin': '0 auto', 'lineHeight': '16px' });";
		$str .= "						}";
		$str .= "					}";
		$str .= "				}";
		$str .= "				$('#newsListMain').html(items);";
		$str .= "			}";
		$str .= "		});";
		$str .= "   }";
		//加载详情页
		$str .= "	function details(id){";
		$str .= "		$.ajax({";
		$str .= " 			url:'" . BASEURL . "/index.php/App/Webdetailmg/detail',";
		$str .= "			type:'POST',";
		$str .= "			dataType:'JSON',";
		$str .= "			data:{id:id},";
		$str .= "			success:function(ret){";
		$str .= "				if(ret.status){";
		$str .= "					$('.msgdetail').show();";
		$str .= "					$('.msgdetailBody').html(ret.data);";
		$str .= "				}else{";
		$str .= "					$('.msgdetailBody').html('<div class=\"noDataView\" style=\"display:block;\"><img src=\"" . BASEURL . "/Public/infor/images/noDataIcon.png\" alt=\"暂无数据\"><p>暂无数据</p></div>');";
		$str .= "				}";
		$str .= "			}";
		$str .= "		})";
		$str .= "	}";
		$str .= "</script>";
		return $str;
	}
}