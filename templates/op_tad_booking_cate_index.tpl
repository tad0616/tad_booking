<{if $all_tad_booking_cate|default:false}>
    <{if $smarty.session.tad_booking_adm|default:false}>
        <script type="text/javascript">
            $(document).ready(function(){
                $(".tad_booking_item_sort").sortable({ opacity: 0.6, cursor: "move", update: function() {
                    var order = $(this).sortable("serialize");
                    $.post("<{$xoops_url}>/modules/tad_booking/manager.php", order + "&op=tad_booking_item_update_sort", function(msg){
                        $("#tad_booking_item_save_msg").html(msg);
                    });
                }
                });
            });
        </script>
    <{/if}>

    <div id="tad_booking_item_save_msg"></div>

    <div id="cateTab">
        <ul class="resp-tabs-list vert">
            <{foreach from=$all_tad_booking_cate key=k item=data}>
                <li style="font-size: 1.1rem;">
                    <{if $data.enable}>
                        <i class='fa-solid fa-circle-check text-success'></i>
                    <{else}>
                        <i class='fa-solid fa-circle-xmark text-warning'></i>
                    <{/if}>
                    <{$data.title}>
                    <{if isset($data.item_arr)}>(<{$data.item_arr|@count}>)<{/if}>
                </li>
            <{/foreach}>
        </ul>

        <div class="resp-tabs-container vert">
            <{foreach from=$all_tad_booking_cate key=cate_id item=data}>
                <div class="tad_booking_item_sort">
                    <{foreach from=$data.item_arr key=item_id item=item_section}>
                        <div id="tr_<{$item_id}>" class="my-border my-2 d-inline-block" style="width: auto;">
                            <{if $item_section.item.enable}>
                                <i class='fa-solid fa-circle-check text-info'></i>
                            <{else}>
                                <i class='fa-solid fa-circle-pause text-secondary'></i>
                            <{/if}>
                            <a href="<{$xoops_url}>/modules/tad_booking/manager.php?op=tad_booking_item_show&item_id=<{$item_id}>"><{$item_section.item.title}></a>
                            (
                                <{if $item_section.item.approval}>
                                    <span class="text-danger" data-bs-toggle="tooltip" data-bs-html="true" title="<{$smarty.const._MD_TADBOOKING_ITEM_NEED_APPROVAL}>">
                                        <i class="fa-solid fa-person-circle-check"></i>
                                    </span>
                                <{/if}>

                                <{if $item_section.count > 0}>
                                    <{$item_section.count}><{$smarty.const._MD_TADBOOKING_SECTION}>
                                <{else}>
                                    <{$smarty.const._MD_TADBOOKING_NO_SECTION}>
                                <{/if}>
                            )
                        </div>
                    <{/foreach}>

                    <div class="my-border my-2 d-inline-block" style="width: auto;">
                        <a href="<{$xoops_url}>/modules/tad_booking/manager.php?cate_id=<{$data.id}>&op=tad_booking_item_create"><i class="fa-solid fa-circle-plus fa-xl"></i></a>
                    </div>

                    <div class="mt-3">
                        <{if $smarty.session.tad_booking_adm|default:false}>

                            <a href="javascript:tad_booking_cate_destroy_func(<{$data.id}>);" class="btn btn-sm btn-danger <{if $data.item_arr}>disabled<{/if}>" title="<{if $data.item_arr}><{$smarty.const._MD_TADBOOKING_CANT_DELETE}><{else}><{$smarty.const._MD_TADBOOKING_DEL_CATE|sprintf:$data.title}><{/if}>"><i class="fa fa-trash"></i> <{$smarty.const._MD_TADBOOKING_DEL_CATE|sprintf:$data.title}></a>

                            <a href="<{$xoops_url}>/modules/tad_booking/manager.php?op=tad_booking_cate_edit&id=<{$data.id}>" class="btn btn-sm btn-warning" title="<{$smarty.const._TAD_EDIT}>"><i class="fa fa-pencil"></i> <{$smarty.const._MD_TADBOOKING_EDIT_CATE|sprintf:$data.title}></a>
                        <{/if}>
                    </div>
                </div>
            <{/foreach}>
        </div>
    </div>

    <{if $smarty.session.tad_booking_adm|default:false}>
        <div class="text-right text-end my-3">
            <a href="<{$xoops_url}>/modules/tad_booking/manager.php?op=tad_booking_cate_create" class="btn btn-info">
                <i class="fa fa-plus"></i> <{$smarty.const._MD_TADBOOKING_ADD_CATE}>
            </a>
        </div>
    <{/if}>


<{else}>
    <div class="alert alert-warning text-center">
        <{if $smarty.session.tad_booking_adm|default:false}>
            <a href="<{$xoops_url}>/modules/tad_booking/manager.php?op=tad_booking_cate_create" class="btn btn-info">
                <i class="fa fa-plus"></i> <{$smarty.const._MD_TADBOOKING_ADD_CATE}>
            </a>
        <{else}>
            <h3><{$smarty.const._TAD_EMPTY}></h3>
        <{/if}>
    </div>
<{/if}>
