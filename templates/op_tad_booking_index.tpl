<{if $all_tad_booking|default:false}>
    <{if $smarty.session.tad_booking_adm|default:false}>

    <{/if}>

    <div id="tad_booking_save_msg"></div>

    <div class="vtb mt-4">
        <ul id="tad_booking_sort" class="vhead">

                <!--預約者-->
                <li class="w10"><{$smarty.const._MD_TADBOOKING_UID}></li>
                <!--預約時間-->
                <li class="w10"><{$smarty.const._MD_TADBOOKING_BOOKING_TIME}></li>
                <!--預約理由-->
                <li class="w10"><{$smarty.const._MD_TADBOOKING_CONTENT}></li>
                <!--開始日期-->
                <li class="w10"><{$smarty.const._MD_TADBOOKING_START_DATE}></li>
                <!--結束日期-->
                <li class="w10"><{$smarty.const._MD_TADBOOKING_END_DATE}></li>
                <!--相關資訊-->
                <li class="w10"><{$smarty.const._MD_TADBOOKING_INFO}></li>
            <{if $smarty.session.tad_booking_adm|default:false}>
                <li class="w10"><{$smarty.const._TAD_FUNCTION}></li>
            <{/if}>
        </ul>
        <{foreach from=$all_tad_booking key=k item=data name=all_tad_booking}>
            <ul id="tr_<{$data.id}>">

                <!--預約者-->
                <li class="c ">
                    <div class="vcell"><{$smarty.const._MD_TADBOOKING_UID}></div>
                    <a href="<{$xoops_user}>/user.php?uid=<{$data.uid}>"><{$data.uid_name}></a>
                </li>

                <!--預約時間-->
                <li class="c ">
                    <div class="vcell"><{$smarty.const._MD_TADBOOKING_BOOKING_TIME}></div>
                    <{$data.booking_time}>
                </li>

                <!--預約理由-->
                <li class="c ">
                    <div class="vcell"><{$smarty.const._MD_TADBOOKING_CONTENT}></div>
                    <{$data.content}>
                </li>

                <!--開始日期-->
                <li class="c ">
                    <div class="vcell"><{$smarty.const._MD_TADBOOKING_START_DATE}></div>
                    <{$data.start_date}>
                </li>

                <!--結束日期-->
                <li class="c ">
                    <div class="vcell"><{$smarty.const._MD_TADBOOKING_END_DATE}></div>
                    <{$data.end_date}>
                </li>

                <!--相關資訊-->
                <li class="c ">
                    <div class="vcell"><{$smarty.const._MD_TADBOOKING_INFO}></div>
                    <{$data.info}>
                </li>

                <{if $smarty.session.tad_booking_adm|default:false}>
                    <li class="vm c w10">
                        <a href="javascript:tad_booking_destroy_func(<{$data.id}>);" class="btn btn-sm btn-xs btn-danger" title="<{$smarty.const._TAD_DEL}>"><i class="fa fa-trash"></i></a>
                        <a href="<{$xoops_url}>/modules/tad_booking/index.php?op=tad_booking_edit&id=<{$data.id}>" class="btn btn-sm btn-xs btn-warning" title="<{$smarty.const._TAD_EDIT}>"><i class="fa fa-pencil"></i></a>

                    </li>
                <{/if}>
            </ul>
        <{/foreach}>
    </div>

    <{if $smarty.session.tad_booking_adm|default:false}>
        <div class="text-right text-end my-3">
            <a href="<{$xoops_url}>/modules/tad_booking/index.php?op=tad_booking_create" class="btn btn-info">
                <i class="fa fa-plus"></i> <{$smarty.const._TAD_ADD}>
            </a>
        </div>
    <{/if}>

    <div class="bar"><{$bar|default:''}></div>

<{else}>
    <div class="alert alert-warning text-center">
        <{if $smarty.session.tad_booking_adm|default:false}>
            <a href="<{$xoops_url}>/modules/tad_booking/index.php?op=tad_booking_create" class="btn btn-info">
                <i class="fa fa-plus"></i> <{$smarty.const._TAD_ADD}>
            </a>
        <{else}>
            <h3><{$smarty.const._TAD_EMPTY}></h3>
        <{/if}>
    </div>
<{/if}>
