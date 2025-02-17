<{if $all_tad_booking_data|default:false}>
    <{if $smarty.session.tad_booking_adm|default:false}>

    <{/if}>

    <div id="tad_booking_data_save_msg"></div>

    <div class="vtb mt-4">
        <ul id="tad_booking_data_sort" class="vhead">

                <!--順位-->
                <li class="w10"><{$smarty.const._MD_TADBOOKING_DATA_WAITING}></li>
                <!--是否核准-->
                <li class="w10"><{$smarty.const._MD_TADBOOKING_DATA_STATUS}></li>
                <!--審核者-->
                <li class="w10"><{$smarty.const._MD_TADBOOKING_DATA_APPROVER}></li>
                <!--通過日期-->
                <li class="w10"><{$smarty.const._MD_TADBOOKING_DATA_PASS_DATE}></li>
            <{if $smarty.session.tad_booking_adm|default:false}>
                <li class="w10"><{$smarty.const._TAD_FUNCTION}></li>
            <{/if}>
        </ul>
        <{foreach from=$all_tad_booking_data key=k item=data name=all_tad_booking_data}>
            <ul id="tr_<{$data.booking_date}>-<{$data.booking_id}>-<{$data.section_id}>">

                <!--順位-->
                <li class="c ">
                    <div class="vcell"><{$smarty.const._MD_TADBOOKING_DATA_WAITING}></div>
                    <{$data.waiting}>
                </li>

                <!--是否核准-->
                <li class="c ">
                    <div class="vcell"><{$smarty.const._MD_TADBOOKING_DATA_STATUS}></div>
                    <{if $data.status == '1'}>
                        <img src="<{$xoops_url}>/modules/tad_booking/images/yes.gif" alt="<{$smarty.const._TAD_ENABLE}>" title="<{$smarty.const._TAD_ENABLE}>">
                    <{else}>
                        <img src="<{$xoops_url}>/modules/tad_booking/images/no.gif" alt="<{$smarty.const._TAD_UNABLE}>" title="<{$smarty.const._TAD_UNABLE}>">
                    <{/if}>

                </li>

                <!--審核者-->
                <li class="c ">
                    <div class="vcell"><{$smarty.const._MD_TADBOOKING_DATA_APPROVER}></div>
                    <a href="<{$xoops_user}>/user.php?uid=<{$data.approver}>"><{$data.approver_name}></a>
                </li>

                <!--通過日期-->
                <li class="c ">
                    <div class="vcell"><{$smarty.const._MD_TADBOOKING_DATA_PASS_DATE}></div>
                    <{$data.pass_date}>
                </li>

                <{if $smarty.session.tad_booking_adm|default:false}>
                    <li class="vm c w10">
                        <a href="javascript:tad_booking_data_destroy_func('<{$data.booking_date}>', '<{$data.booking_id}>', '<{$data.section_id}>');" class="btn btn-sm btn-xs btn-danger" title="<{$smarty.const._TAD_DEL}>"><i class="fa fa-trash"></i></a>
                        <a href="<{$xoops_url}>/modules/tad_booking/index.php?op=tad_booking_data_edit&booking_date=<{$data.booking_date}>&booking_id=<{$data.booking_id}>&section_id=<{$data.section_id}>" class="btn btn-sm btn-xs btn-warning" title="<{$smarty.const._TAD_EDIT}>"><i class="fa fa-pencil"></i></a>

                    </li>
                <{/if}>
            </ul>
        <{/foreach}>
    </div>

    <{if $smarty.session.tad_booking_adm|default:false}>
        <div class="text-right text-end my-3">
            <a href="<{$xoops_url}>/modules/tad_booking/index.php?op=tad_booking_data_create" class="btn btn-info">
                <i class="fa fa-plus"></i> <{$smarty.const._TAD_ADD}>
            </a>
        </div>
    <{/if}>

    <div class="bar"><{$bar|default:''}></div>

<{else}>
    <div class="alert alert-warning text-center">
        <{if $smarty.session.tad_booking_adm|default:false}>
            <a href="<{$xoops_url}>/modules/tad_booking/index.php?op=tad_booking_data_create" class="btn btn-info">
                <i class="fa fa-plus"></i> <{$smarty.const._TAD_ADD}>
            </a>
        <{else}>
            <h3><{$smarty.const._TAD_EMPTY}></h3>
        <{/if}>
    </div>
<{/if}>
