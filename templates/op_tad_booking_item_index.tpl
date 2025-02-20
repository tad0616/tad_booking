<{if $all_tad_booking_item|default:false}>
    <{if $smarty.session.tad_booking_adm|default:false}>
        <script type="text/javascript">
            $(document).ready(function(){
                $("#tad_booking_item_sort").sortable({ opacity: 0.6, cursor: "move", update: function() {
                    var order = $(this).sortable("serialize");
                    $.post("<{$xoops_url}>/modules/tad_booking/admin/tad_booking_item_save_sort.php", order + "&op=update_tad_booking_item_sort", function(msg){
                        $("#tad_booking_item_save_msg").html(msg);
                    });
                }
                });
            });
        </script>
    <{/if}>

    <div id="tad_booking_item_save_msg"></div>

    <div class="vtb mt-4">
        <ul id="tad_booking_item_sort" class="vhead">

                <!--類別編號-->
                <li class="w10"><{$smarty.const._MD_TADBOOKING_ITEM_CATE_ID}></li>
                <!--名稱-->
                <li class="w10"><{$smarty.const._MD_TADBOOKING_ITEM_TITLE}></li>
                <!--啟用日期-->
                <li class="w10"><{$smarty.const._MD_TADBOOKING_ITEM_START}></li>
                <!--停用日期-->
                <li class="w10"><{$smarty.const._MD_TADBOOKING_ITEM_END}></li>
                <!--是否可借-->
                <li class="w10"><{$smarty.const._MD_TADBOOKING_ITEM_ENABLE}></li>
                <!--審核人員-->
                <li class="w10"><{$smarty.const._MD_TADBOOKING_ITEM_APPROVAL}></li>
                <!--相關資訊-->
                <li class="w10"><{$smarty.const._MD_TADBOOKING_ITEM_INFO}></li>
            <{if $smarty.session.tad_booking_adm|default:false}>
                <li class="w10"><{$smarty.const._TAD_FUNCTION}></li>
            <{/if}>
        </ul>
        <{foreach from=$all_tad_booking_item key=k item=data name=all_tad_booking_item}>
            <ul id="tr_<{$data.id}>">

                <!--類別編號-->
                <li class="c ">
                    <div class="vcell"><{$smarty.const._MD_TADBOOKING_ITEM_CATE_ID}></div>
                    <a href="<{$xoops_url}>/modules/tad_booking/manager.php?cate_id=<{$data.cate_id}>">
                <{$data.cate_id_title}>
                </a>
                </li>

                <!--名稱-->
                <li class="c mobile-title">
                    <div class="vcell"><{$smarty.const._MD_TADBOOKING_ITEM_TITLE}></div>
                    <a href="<{$xoops_url}>/modules/tad_booking/manager.php?id=<{$data.id}>"><{$data.title}></a><{$data.files}>
                </li>

                <!--啟用日期-->
                <li class="c ">
                    <div class="vcell"><{$smarty.const._MD_TADBOOKING_ITEM_START}></div>
                    <{$data.start}>
                </li>

                <!--停用日期-->
                <li class="c ">
                    <div class="vcell"><{$smarty.const._MD_TADBOOKING_ITEM_END}></div>
                    <{$data.end}>
                </li>

                <!--是否可借-->
                <li class="c ">
                    <div class="vcell"><{$smarty.const._MD_TADBOOKING_ITEM_ENABLE}></div>
                    <{if $data.enable == '1'}>
                        <img src="<{$xoops_url}>/modules/tad_booking/images/yes.gif" alt="<{$smarty.const._TAD_ENABLE}>" title="<{$smarty.const._TAD_ENABLE}>">
                    <{else}>
                        <img src="<{$xoops_url}>/modules/tad_booking/images/no.gif" alt="<{$smarty.const._TAD_UNABLE}>" title="<{$smarty.const._TAD_UNABLE}>">
                    <{/if}>

                </li>

                <!--審核人員-->
                <li class="c ">
                    <div class="vcell"><{$smarty.const._MD_TADBOOKING_ITEM_APPROVAL}></div>
                    <{$data.approval|replace:';':$smarty.const._TAD_AND}>
                </li>

                <!--相關資訊-->
                <li class="c ">
                    <div class="vcell"><{$smarty.const._MD_TADBOOKING_ITEM_INFO}></div>
                    <{$data.info}>
                </li>

                <{if $smarty.session.tad_booking_adm|default:false}>
                    <li class="vm c w10">
                        <a href="javascript:tad_booking_item_destroy_func(<{$data.id}>);" class="btn btn-sm btn-xs btn-danger <{if $data.enable}>disabled<{/if}>" title="<{$smarty.const._MD_TADBOOKING_DEL|sprintf:$data.title}>"><i class="fa fa-trash"></i></a>
                        <a href="<{$xoops_url}>/modules/tad_booking/manager.php?op=tad_booking_item_show&id=<{$data.id}>" class="btn btn-sm btn-xs btn-warning" title="<{$smarty.const._MD_TADBOOKING_EDIT|sprintf:$data.title}>"><i class="fa fa-pencil"></i></a>
                        <i class="fa fa-sort" aria-hidden="true" title="$tad_sortable"></i>
                    </li>
                <{/if}>
            </ul>
        <{/foreach}>
    </div>

    <{if $smarty.session.tad_booking_adm|default:false}>
        <div class="text-right text-end my-3">
            <a href="<{$xoops_url}>/modules/tad_booking/manager.php?op=tad_booking_item_create&cate_id=<{$cate_id}>" class="btn btn-info">
                <i class="fa fa-plus"></i> <{$smarty.const._TAD_ADD}>
            </a>
        </div>
    <{/if}>

    <div class="bar"><{$bar|default:''}></div>

<{else}>
    <div class="alert alert-warning text-center">
        <{if $smarty.session.tad_booking_adm|default:false}>
            <a href="<{$xoops_url}>/modules/tad_booking/manager.php?op=tad_booking_item_create&cate_id=<{$cate_id}>" class="btn btn-info">
                <i class="fa fa-plus"></i> <{$smarty.const._TAD_ADD}>
            </a>
        <{else}>
            <h3><{$smarty.const._TAD_EMPTY}></h3>
        <{/if}>
    </div>
<{/if}>
