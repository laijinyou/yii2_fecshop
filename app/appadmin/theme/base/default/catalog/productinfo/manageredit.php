<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */

use fec\helpers\CRequest;
use fec\helpers\CUrl;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
?>
<style>
.checker{float:left;}
.dialog .pageContent {background:none;}
.dialog .pageContent .pageFormContent{background:none;}
.edit_p{display:block;height:35px;}
.edit_p label{float:left;line-height: 20px;min-width:200px;}
.edit_p input{width:700px;}
.tabsContent .tabsContent .edit_p label{min-width:194px;}
.edit_p .tier_price input{
	width:100px;
}

.tier_price table thead tr th{
	 background: #ddd none repeat scroll 0 0;
    border: 1px solid #ccc;
    padding: 4px 10px;
    width: 100px;
}

.tier_price table tbody tr td{
	background: #fff;
    border-right: 1px solid #ccc;
	border-bottom: 1px solid #ccc;
    padding:3px;
    width: 100px;
}

.custom_option_list table thead tr th{
	 background: #ddd none repeat scroll 0 0;
    border: 1px solid #ccc;
    padding: 4px 10px;
    width: 100px;
}

.custom_option_list table tbody tr td{
	background: #fff;
    border-right: 1px solid #ccc;
	border-bottom: 1px solid #ccc;
    padding:3px;
    width: 100px;
}



.edit_p .tier_price input.tier_qty{width:30px;}
.custom_option{padding:10px 5px;}
.custom_option span{margin:0 2px 0 10px;}

.custom_option .nps{float:left;margin:0 0 10px 0}
.custom_option_img_list img {cursor:pointer;}
</style>

<script>

$(document).ready(function(){
	$(document).off("change").on("change",".attr_group",function(){
		//alert(2222);
		options = {};
		val = $(this).val();
		pm = "?attr_group="+val;
		currentPrimayInfo = $(".primary_info").val();
		currentPrimayInfo = currentPrimayInfo ? '&'+currentPrimayInfo : '';
		url = '<?= CUrl::getUrl("catalog/productinfo/manageredit"); ?>'+pm+currentPrimayInfo;
		$.pdialog.reload(url,options);
	});
});


function getCategoryData(product_id,i){
	$.ajax({
		url:'<?= CUrl::getUrl("catalog/productinfo/getproductcategory",['product_id'=>$product_id]); ?>',
		async:false,
		timeout: 80000,
		dataType: 'json',
		type:'get',
		data:{
			'product_id':product_id,
		},
		success:function(data, textStatus){
			if(data.return_status == "success"){
				jQuery(".category_tree").html(data.menu);
				// $.fn.zTree.init($(".category_tree"), subMenuSetting, json);
				if(i){
					$("ul.tree", ".dialog").jTree();
				}
			}
		},
		error:function(){
			alert("<?=  Yii::$service->page->translate->__('load category info error') ?>");
		}
	});
}

function thissubmit(thiss){
	// product image
	main_image_image 		=  $('.productimg input[type=radio]:checked').val();
	main_image_label 		    =  $('.productimg input[type=radio]:checked').parent().parent().find(".image_label").val();
	main_image_sort_order 	=  $('.productimg input[type=radio]:checked').parent().parent().find(".sort_order").val();
	main_image_is_thumbnails    =  $('.productimg input[type=radio]:checked').parent().parent().find(".is_thumbnails").val();
    main_image_is_detail 	    =  $('.productimg input[type=radio]:checked').parent().parent().find(".is_detail").val();
    //alert(main_image_image+main_image_label+main_image_sort_order);
	if(main_image_image){
		image_main = main_image_image+'#####'+main_image_label+'#####'+main_image_sort_order  +'#####'+main_image_is_thumbnails  +'#####'+main_image_is_detail;
		$(".tabsContent .image_main").val(image_main);
	}else{
		alert('<?=  Yii::$service->page->translate->__('You upload and select at least one main image') ?>');
		//DWZ.ajaxDone;
		return false;
	}
	image_gallery = '';
	$('.productimg input[type=radio]').each(function(){
		if(!$(this).is(':checked')){
			gallery_image_image 		= $(this).val();
			gallery_image_label 		= $(this).parent().parent().find(".image_label").val();
			gallery_image_sort_order 	= $(this).parent().parent().find(".sort_order").val();
            gallery_image_is_thumbnails = $(this).parent().parent().find(".is_thumbnails").val();
            gallery_image_is_detail 	= $(this).parent().parent().find(".is_detail").val();
			//alert(gallery_image_image+gallery_image_label+gallery_image_sort_order);
			image_gallery += gallery_image_image+'#####'+gallery_image_label+'#####'+gallery_image_sort_order +'#####'+gallery_image_is_thumbnails  +'#####'+gallery_image_is_detail+'|||||';
		}
	});

	$(".tabsContent .image_gallery").val(image_gallery);
	//custom_option
	//i = 0;
	//custom_option = new Object();
	//jQuery(".custom_option_list tbody tr").each(function(){
	//	option_header = new Object();
	//	$(this).find("td").each(function(){
	//		rel = $(this).attr("rel");
	//
	//		if(rel != 'image'){
	//			if(rel){
	//				option_header[rel] = $(this).attr('val');
	//			}
	//		}else{
	//			rel = $(this).find("img").attr("rel");
	//			option_header['image'] = rel;
	//		}
	//
	//	});
	//	custom_option[i] = option_header;
	//	i++;
	//});
	//
	//custom_option = JSON.stringify(custom_option);
	//alert(custom_option);
	//jQuery(".custom_option_value").val(custom_option);

	cate_str = "";
	jQuery(".category_tree div.ckbox.checked").each(function(){
		cate_id = jQuery(this).find("input").val();
		cate_str += cate_id+",";
	});



	jQuery(".category_tree div.ckbox.indeterminate").each(function(){
		cate_id = jQuery(this).find("input").val();
		cate_str += cate_id+",";
	});

	jQuery(".inputcategory").val(cate_str);

	tier_price_str = "";
	$(".tier_price table tbody tr").each(function(){
		tier_qty = $(this).find(".tier_qty").val();
		tier_price = $(this).find(".tier_price").val();
		if(tier_qty && tier_price){
			tier_price_str += tier_qty+'##'+tier_price+"||";
		}
	});
	//alert(tier_price_str);
	jQuery(".tier_price_input").val(tier_price_str);
	//alert($(".tier_price_input").val());
	return validateCallback(thiss, dialogAjaxDoneCloseAndReflush);
}
</script>

<div class="pageContent">
	<form  method="post" action="<?= $saveUrl ?>" class="pageForm required-validate" onsubmit="return thissubmit(this, dialogAjaxDoneCloseAndReflush);">
		<?php echo CRequest::getCsrfInputHtml();  ?>
		<input type="hidden" name="operate"  value="<?=  $operate ?>" />
		<input type="hidden" class="primary_info"  value="<?= $primaryInfo ?>" />
		<div class="tabs" >
			<div class="tabsHeader">
				<div class="tabsHeaderContent">
					<ul>
						<li><a href="javascript:;"><span><?=  Yii::$service->page->translate->__('Basic Info') ?></span></a></li>
						<li><a href="javascript:;"><span><?=  Yii::$service->page->translate->__('Price Info') ?></span></a></li>
						<li><a href="javascript:;"><span><?=  Yii::$service->page->translate->__('Meta Info') ?></span></a></li>
						<li><a href="javascript:;"><span><?=  Yii::$service->page->translate->__('Description Info') ?></span></a></li>
						<li><a href="javascript:;"><span><?=  Yii::$service->page->translate->__('Image Info') ?></span></a></li>
						<li><a href="javascript:;"><span><?=  Yii::$service->page->translate->__('Category Info') ?></span></a></li>
						<li><a href="javascript:;"><span><?=  Yii::$service->page->translate->__('Attr Group') ?></span></a></li>
						<!--<li><a href="javascript:;"><span><?=  Yii::$service->page->translate->__('Custom Option') ?></span></a></li>
						-->
                        <li><a href="javascript:;"><span><?=  Yii::$service->page->translate->__('Relate Product') ?></span></a></li>
                        <li><a href="javascript:;"><span><?=  Yii::$service->page->translate->__('Third Collection') ?></span></a></li>

                    </ul>
				</div>
			</div>
			<div class="productPage tabsContent" style="height:550px;overflow:auto;">
				<div>
					<input type="hidden"  value="<?=  $product_id; ?>" size="30" name="product_id" class="textInput ">

					<fieldset id="fieldset_table_qbe">
						<legend style=""><?=  Yii::$service->page->translate->__('Product attribute group switching: Please switch the product attribute group before editing') ?></legend>
						<div>
							<p class="edit_p" style="padding: 5px 0 0 10px;  height: 20px;">
								<?= $attrGroup ?>
							</p>
						</div>
					</fieldset>
					<?= $baseInfo ?>
				</div>
				<div>
					<?= $priceInfo ?>
					<div class="edit_p">
						<label><?=  Yii::$service->page->translate->__('Tier Price') ?>???</label>
						<input type="hidden" name="editFormData[tier_price]" class="tier_price_input"  />
						<div class="tier_price" style="float:left;width:700px;">
							<table style="">
								<thead>
									<tr>
										<th><?=  Yii::$service->page->translate->__('Qty') ?></th>
										<th><?=  Yii::$service->page->translate->__('Price') ?></th>
										<th><?=  Yii::$service->page->translate->__('Action') ?></th>
									</tr>
								</thead>
								<tbody>
									<?php if(is_array($tier_price) && !empty($tier_price)){  ?>
										<?php foreach($tier_price as $one){ ?>
										<tr>
											<td>
												<input class="tier_qty" type="text" value="<?= $one['qty'] ?>"> <?=  Yii::$service->page->translate->__('And Above') ?>
											</td>
											<td>
												<input class="tier_price" type="text" value="<?= $one['price'] ?>">
											</td>
											<td>
                                                <i class="fa fa-trash-o"></i>
											</td>
										</tr>
										<?php } ?>
									<?php } ?>
								</tbody>
								<tfoot style="text-align:right;">
									<tr>
										<td colspan="100" style="text-align:right;">
											<a rel="2" style="text-align:right;" href="javascript:void(0)" class="addProductTierPrice button">
												<span><?=  Yii::$service->page->translate->__('Add Tier Price') ?></span>
											</a>
										</td>
									</tr>
								</tfoot>
							</table>
							<script>
								$(document).ready(function(){
									$(".addProductTierPrice").click(function(){
										str = "<tr>";
										str +="<td><input class=\"tier_qty textInput \" type=\"text\"   /> <?=  Yii::$service->page->translate->__('And Above') ?> </td>";
										str +="<td><input class=\"tier_price textInput\" type=\"text\"   /></td>";
										str +="<td><i class='fa fa-trash-o'></i></td>";
										str +="</tr>";
										$(".tier_price table tbody").append(str);
									});
									$(".dialog").off("click").on("click",".tier_price table tbody tr td .fa-trash-o",function(){
                                        $(this).parent().parent().remove();
                                    });

								});
							</script>
						</div>
					</div>
				</div>
				<div>
					<?= $metaInfo ?>
				</div>
				<div >
					<?= $descriptionInfo ?>
				</div>
				<div >
					<input type="hidden" name="image_main" class="image_main"  />
					<input type="hidden" name="image_gallery" class="image_gallery"  />
					<?=  $img_html ?>
					<div id="addpicContainer" style="padding-bottom:20px;">
						<!-- ??????multiple="multiple"?????????????????????????????? -->
						<!-- position: absolute;left: 10px;top: 5px;?????????????????????input?????????????????????-->
						<!-- height:0;width:0;z-index: -1;???????????????input?????????Chrome???????????????display:none??????????????????????????? -->
						<!-- onclick="getElementById('inputfile').click()" ?????????????????????????????????????????? -->
						<button style="" onclick="getElementById('inputfile').click()" class="scalable upload-image" type="button" title="Duplicate" id=""><span><span><span><?=  Yii::$service->page->translate->__('Browse Files') ?></span></span></span></button>

						<input type="file" multiple="multiple" id="inputfile" style="margin:10px;height:0;width:0;z-index: -1; position: absolute;left: 10px;top: 5px;"/>
						<span class="loading"></span>
					</div>
					<script>
						jQuery(document).ready(function(){
							jQuery("body").on('click',".delete_img",function(){
								jQuery(this).parent().parent().remove();
							});
							//jQuery(".delete_img").click(function(){
							//	jQuery
							//});

							//??????????????????????????????
							$("#inputfile").change(function(){
								//??????FormData??????
								var thisindex = 0;
								jQuery(".productimg tbody tr").each(function(){
									rel = parseInt(jQuery(this).attr("rel"));
									//alert(rel);
									if(rel > thisindex){
										thisindex = rel;
									}
								});
								//alert(thisindex);
								var data = new FormData();
								data.append('thisindex', thisindex);

								//???FormData??????????????????
								$.each($('#inputfile')[0].files, function(i, file) {
									data.append('upload_file'+i, file);
								});
								//$(".loading").show();	//??????????????????
								//????????????
								data.append("<?= CRequest::getCsrfName() ?>", "<?= CRequest::getCsrfValue() ?>");
								$.ajax({
									url:'<?= CUrl::getUrl('catalog/productinfo/imageupload')  ?>',
									type:'POST',
									data:data,
									async:false,
									dataType: 'json',
									timeout: 80000,
									cache: false,
									contentType: false,		//???????????????
									processData: false,		//???????????????
									success:function(data, textStatus){
										//data = $(data).html();
										//?????????feedback????????????append???????????????before???1?????? .eq(0).before() ?????????????????????
										//data.replace(/&lt;/g,'<').replace(/&gt;/g,'>') ??????html????????????????????????????????????
										//if($("#feedback").children('img').length == 0) $("#feedback").append(data.replace(/&lt;/g,'<').replace(/&gt;/g,'>'));
										//else $("#feedback").children('img').eq(0).before(data.replace(/&lt;/g,'<').replace(/&gt;/g,'>'));
									//	alert(data.return_status);
										if(data.return_status == "success"){
										//	alert("success");
											jQuery(".productimg tbody ").append(data.img_str);
											//alert(data.img_str);
										}
										//$(".loading").hide();	//??????????????????????????????
									},
									error:function(){
										alert('<?=  Yii::$service->page->translate->__('Upload Error') ?>');
										//$(".loading").hide();	//??????????????????????????????
									}
								});
							});
						});
					</script>
				</div>
				<div>
					<script>

                        $(document).ready(function(){
                            id = '<?= $product_id; ?>' ;

                            getCategoryData(id,0);
                        });
                    </script>
                    <input type="hidden" value="" name="category"  class="inputcategory"/>
                    <ul class="category_tree tree treeFolder treeCheck expand" >
                    </ul>
				</div>
				<div >
					<?= $groupAttr ?>
				</div>
				<div class="relation_list" style="margin:20px 2px;">
						<?= $relation ?>
				</div>
                <div class="third_info_list" style="margin:20px 2px;">
						<?= $thirdInfo ?>
				</div>
			</div>
			<div class="tabsFooter">
				<div class="tabsFooterContent"></div>
			</div>
		</div>
		<div class="formBar">
			<ul>
				<!--<li><a class="buttonActive" href="javascript:;"><span>??????</span></a></li>-->
				<li><div class="buttonActive"><div class="buttonContent"><button onclick=""  value="accept" name="accept" type="submit"><?=  Yii::$service->page->translate->__('Save') ?></button></div></div></li>
				<li>
					<div class="button"><div class="buttonContent"><button type="button" class="close"><?=  Yii::$service->page->translate->__('Cancel') ?></button></div></div>
				</li>
			</ul>
		</div>
	</form>
</div>

<script>
    var div = document.getElementById("container");
    var w = div.offsetWidth;    // ????????????????????????
    var h = div.offsetHeight;    // ????????????????????????
    var vsss = h*0.95 - 150;
    $(".pageForm > .tabs >.tabsContent").css("height", vsss+'px');
</script>
