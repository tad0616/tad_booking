
<div class="text-center">
    <{if $smarty.session.tad_booking_adm|default:false}>
        <a href="javascript:tad_booking_destroy_func(<{$id}>);" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="<{$smarty.const._TAD_DEL}>"><i class="fa fa-times" aria-hidden="true"></i></a>
        <a href="<{$xoops_url}>/modules/tad_booking/index.php?op=tad_booking_edit&id=<{$id}>" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="<{$smarty.const._TAD_EDIT}>"><i class="fa fa-pencil" aria-hidden="true"></i> <{$smarty.const._TAD_EDIT}></a>
        <a href="<{$xoops_url}>/modules/tad_booking/index.php?op=tad_booking_create" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="<{$smarty.const._TAD_ADD}>"><i class="fa fa-plus" aria-hidden="true"></i> <{$smarty.const._TAD_ADD}></a>
    <{/if}>
    <a href="<{$xoops_url}>/modules/tad_booking/" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="<{$smarty.const._TAD_HOME}>"><i class="fa fa-home" aria-hidden="true"></i> <{$smarty.const._TAD_HOME}></a>
</div>
<div class="alert alert-warning d-inline-block text-center py-1 px-5 my-3">
    <i class="fa fa-user"></i> <{$uid_name}>
    <i class="fa fa-calendar"></i> <{$booking_time}></div>
<{$info}>
<div class="vtb mt-3">
<!--預約理由-->
<ul>
    <li class="w2 vtitle"><{$smarty.const._MD_TADBOOKING_CONTENT}></li>
    <li class="w8"><{$content}></li>
</ul>

<!--開始日期-->
<ul>
    <li class="w2 vtitle"><{$smarty.const._MD_TADBOOKING_START_DATE}></li>
    <li class="w8"><{$start_date}></li>
</ul>

<!--結束日期-->
<ul>
    <li class="w2 vtitle"><{$smarty.const._MD_TADBOOKING_END_DATE}></li>
    <li class="w8"><{$end_date}></li>
</ul>
</div>
