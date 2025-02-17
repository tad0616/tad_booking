<h1 class="my text-center"><{$title}></h1>

<div class="text-center">
    <{if $smarty.session.tad_booking_adm|default:false}>
        <a href="javascript:tad_booking_section_destroy_func(<{$id}>);" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="<{$smarty.const._TAD_DEL}>"><i class="fa fa-times" aria-hidden="true"></i></a>
        <a href="<{$xoops_url}>/modules/tad_booking/manager.php?op=tad_booking_section_edit&id=<{$id}>" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="<{$smarty.const._TAD_EDIT}>"><i class="fa fa-pencil" aria-hidden="true"></i> <{$smarty.const._TAD_EDIT}></a>
        <a href="<{$xoops_url}>/modules/tad_booking/manager.php?op=tad_booking_section_create" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="<{$smarty.const._TAD_ADD}>"><i class="fa fa-plus" aria-hidden="true"></i> <{$smarty.const._TAD_ADD}></a>
    <{/if}>
    <a href="<{$xoops_url}>/modules/tad_booking/" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="<{$smarty.const._TAD_HOME}>"><i class="fa fa-home" aria-hidden="true"></i> <{$smarty.const._TAD_HOME}></a>
</div>


<div class="vtb mt-3">
<!--場地編號-->
<ul>
    <li class="w2 vtitle"><{$smarty.const._MD_TADBOOKING_SECTION_ITEM_ID}></li>
    <li class="w8"><{$item_id}></li>
</ul>

<!--開放星期-->
<ul>
    <li class="w2 vtitle"><{$smarty.const._MD_TADBOOKING_SECTION_WEEK}></li>
    <li class="w8"><{$week}></li>
</ul>
</div>
