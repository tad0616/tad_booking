<h1 class="text-center">
    <a href="manager.php#cateTab<{$cate.sort}>"><i class="fa-solid fa-caret-left"></i> <{$cate.title}></a> /

    <{if $enable}>
        <i class='fa-solid fa-circle-check text-info'></i>
    <{else}>
        <i class='fa-solid fa-circle-pause text-secondary'></i>
    <{/if}><{$title}>
</h1>


<div class="alert alert-warning text-center py-1 px-5 my-3 mx-auto">
    <{if $smarty.session.tad_booking_adm|default:false}>
        <a href="javascript:tad_booking_item_destroy_func(<{$id}>);" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="<{$smarty.const._TAD_DEL}><{$id}>"><i class="fa fa-times" aria-hidden="true"></i></a>
    <{/if}>
    <{if $smarty.session.tad_booking_adm|default:false}>
        <a href="<{$xoops_url}>/modules/tad_booking/manager.php?op=tad_booking_item_edit&item_id=<{$id}>" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="<{$smarty.const._TAD_EDIT}>"><i class="fa fa-pencil" aria-hidden="true"></i> <{$smarty.const._TAD_EDIT}><{$title}></a>
        <a href="<{$xoops_url}>/modules/tad_booking/manager.php?cate_id=<{$cate_id}>&op=tad_booking_item_create" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="<{$smarty.const._TAD_ADD}>"><i class="fa fa-plus" aria-hidden="true"></i> <{$smarty.const._ADD}><{$cate.title}></a>
    <{/if}>
    <i class="fa-regular fa-calendar-days"></i> <{$smarty.const._MD_TADBOOKING_ITEM_START}><{$smarty.const._TAD_FOR}><{$start}>
    <{if $end!='0000-00-00'}>
    <i class="fa-solid fa-calendar"></i> <{$smarty.const._MD_TADBOOKING_ITEM_END}><{$smarty.const._TAD_FOR}><{$end}>
    <{/if}>
    <{if $approval}>
    <span data-bs-toggle="tooltip" data-bs-html="true" title="<{$smarty.const._MD_TADBOOKING_ITEM_APPROVAL}><{$smarty.const._TAD_FOR}><br><{'<br>'|implode:$approval_name_arr}>">
    <i class="fa-solid fa-users-gear"></i> <{$approval_name_arr|@count}>
    </span>
    <{/if}>
</div>

<{if $desc|default:false}>
    <div class="my-border">
        <{$desc}>
    </div>
<{/if}>



<{include file="$xoops_rootpath/modules/tad_booking/templates/op_tad_booking_section_create.tpl"}>