<script type='text/javascript'>
    function single_insert_booking(wk,section_id,booking_date,item_id){
      $.post('ajax.php', {op: 'single_insert_booking', section_id: section_id, booking_date: booking_date, item_id: item_id, week: wk ,end_date_ts: <{$end_date_ts}>},
      function(data) {
        $('#submit'+section_id+'_'+wk).html(data).css('color','#000000');
      });
    }
</script>

<{$Bootstrap3EditableCode}>

<div class="row my-3 mx-auto" id="booking_bar">
    <div class="col-4 text-right text-end"><{if $item.id|default:false}><a href="index.php?item_id=<{$item.id}>&date=<{$item.prev_week_start}>#xoops_contents" class="btn btn-success"><i class="fa-solid fa-circle-chevron-left"></i> <{$smarty.const._MD_TADBOOKING_PREVIOUS_WEEK}></a><{/if}></div>
    <div class="col-4"><{include file="$xoops_rootpath/modules/tad_booking/templates/sub_item_menu.tpl" item_id=$smarty.get.item_id|default:0}></div>
    <div class="col-4"><{if $item.id|default:false}><a href="index.php?item_id=<{$item.id}>&date=<{$item.next_week_start}>#xoops_contents" class="btn btn-success <{if $end_date_ts < $item.next_week_start_ts && !$smarty.session.tad_booking_adm}>disabled<{/if}>"><{$smarty.const._MD_TADBOOKING_NEXT_WEEK}> <i class="fa-solid fa-circle-chevron-right"></i></a><{/if}></div>
</div>

<{if $item.id|default:false}>
    <div class="alert alert-info">
        <ol class="m-0">
            <{if $item.approval}>
                <{$smarty.const._MD_TADBOOKING_ITEM_NEED_APPROVAL_DESC|sprintf:$item.title}>
            <{else}>
                <{$smarty.const._MD_TADBOOKING_ITEM_NO_NEED_APPROVAL_DESC|sprintf:$item.title}>
            <{/if}>
            <{if $smarty.session.tad_booking_adm}>
                <{$smarty.const._MD_TADBOOKING_BOOKING_ADM_DESC|sprintf:$max_booking_week}>
            <{else}>
                <{$smarty.const._MD_TADBOOKING_BOOKING_DESC|sprintf:$max_booking_week}>
            <{/if}>
        </ol>
    </div>
    <div class="vtb mt-4">
        <ul id="tad_booking_sort" class="vhead">
            <li class="w9"><{$smarty.const._MD_TADBOOKING_SECTION}></li>
            <{foreach from=$item.week_dates key=w item=date}>
            <li class="w13 num"><{$date}> (<{$item.chinese_week.$w}>)</li>
            <{/foreach}>
        </ul>
    <{foreach from=$item.sections key=section_id item=section name=sections}>
        <ul id="tr_<{$section_id}>">
            <li class="c w9">
                <div class="vcell"><{$smarty.const._MD_TADBOOKING_SECTION}></div>
                <{$section.title}>
            </li>
            <{foreach from=$item.week_dates key=w item=date}>
                <{assign var='ok_booking' value=$item.ok_booking.$date.$section_id}>
                <{if $item.booking_arr.$date.$section_id}>
                    <{assign var="uid_arr" value=$item.booking_arr.$date.$section_id|array_keys}>
                <{else}>
                    <{assign var="uid_arr" value=[]}>
                <{/if}>
                <li class="c w13">
                    <div class="vcell num"><{$date}> (<{$item.chinese_week.$w}>) <{$section.title}></div>
                    <div id="submit<{$section_id}>_<{$w}>">
                        <{if $w|in_array:$section.week_arr && $date|strtotime >= $item.today}>
                            <{if $ok_booking}>
                                <{if $xoops_isuser}><{$ok_booking.info.name}><{else}><{$smarty.const._MD_TADBOOKING_BOOKED}><{/if}>

                                <{if $smarty.session.tad_booking_adm|default:false || $ok_booking.uid == $smarty.session.now_user.uid}>
                                    <a href="javascript:delete_booking('<{$item.id}>', '<{$date}>', <{$section_id}>, '<{$ok_booking.booking_id}>', '<{$ok_booking.uid}>');" style='color:#D44950;' ><i class='fa fa-times' ></i></a>
                                <{/if}>

                                <{if $item.booking_arr.$date.$section_id|@count > 1}>
                                    <i class="fa fa-list" data-bs-toggle="tooltip" data-bs-html="true" data-bs-title="<{$smarty.const._MD_TADBOOKING_BOOKING_LIST}><ol>
                                        <{foreach from=$item.booking_arr.$date.$section_id item=booking name=bookings}>
                                            <li><{$booking.info.name}> <{if $booking.status}><i class='fa-solid fa-circle-check text-success'></i><{else}><i class='fa-solid fa-circle-xmark text-danger'></i><{/if}> <{$booking.content}></li>
                                        <{/foreach}>
                                    </ol>"></i>
                                <{/if}>

                                <div style="font-size:0.9rem">
                                    <{if $ok_booking.uid == $smarty.session.now_user.uid}>
                                        <a href="#" class="editable" data-name="content" data-type="text" data-pk="<{$ok_booking.booking_id}>" data-params="{op: 'tad_booking_update_content'}"><{$ok_booking.content|default:<{$smarty.const._MD_TADBOOKING_UNFILLED}>}></a>
                                    <{else}>
                                        <{$ok_booking.content|default:<{$smarty.const._MD_TADBOOKING_UNFILLED}>}>
                                    <{/if}>
                                </div>
                            <{elseif $smarty.session.now_user.uid}>
                                <{assign var='my_uid' value=$smarty.session.now_user.uid}>
                                <{assign var='my_booking' value=$item.booking_arr.$date.$section_id.$my_uid}>
                                <{if $uid_arr && $smarty.session.now_user.uid|in_array:$uid_arr}>
                                    <span class="approving"><{$smarty.const._MD_TADBOOKING_APPROVING}></span>
                                    <a href="javascript:delete_booking('<{$item.id}>', '<{$date}>', <{$section_id}>, '<{$my_booking.booking_id}>', '<{$my_uid}>');" style='color:#D44950;' ><i class='fa fa-times' ></i></a>

                                    <{if $item.booking_arr.$date.$section_id|@count > 1}>
                                        <i class="fa fa-list" data-bs-toggle="tooltip" data-bs-html="true" data-bs-title="<{$smarty.const._MD_TADBOOKING_BOOKING_LIST}><ol>
                                            <{foreach from=$item.booking_arr.$date.$section_id item=booking name=bookings}>
                                                <li><{$booking.info.name}> <{if $booking.status}><i class='fa-solid fa-circle-check text-success'></i><{else}><i class='fa-solid fa-circle-xmark text-danger'></i><{/if}> <{$booking.content}></li>
                                            <{/foreach}>
                                        </ol>"></i>
                                    <{/if}>

                                    <div style="font-size:0.9rem">
                                        <a href="#" class="editable" data-name="content" data-type="text" data-pk="<{$my_booking.booking_id}>" data-params="{op: 'tad_booking_update_content'}"><{$my_booking.content|default:<{$smarty.const._MD_TADBOOKING_UNFILLED}>}></a>
                                    </div>
                                <{else}>
                                    <button type="button" class="btn btn-info <{if $end_date_ts < $date|strtotime && !$smarty.session.tad_booking_adm}>disabled<{/if}>"  onclick="single_insert_booking('<{$w}>' , '<{$section.id}>','<{$date}>','<{$item.id}>')"><i class="fa fa-pencil"></i></button>
                                    <{foreach from=$item.booking_arr.$date.$section_id key=uid item=booking}>
                                        <div class="status-0"><{$booking.waiting}>-<{$booking.info.name}> (<{if $booking.status}><{$smarty.const._MD_TADBOOKING_PASS}><{else}><{$smarty.const._MD_TADBOOKING_APPROVING}><{/if}>)</div>
                                    <{/foreach}>
                                <{/if}>
                            <{/if}>
                        <{else}>
                            <{if $ok_booking && $ok_booking.status == 1}>
                                <div style="color: gray;"><{if $xoops_isuser}><{$ok_booking.info.name}><{else}><{$smarty.const._MD_TADBOOKING_BOOKED}><{/if}></div>
                            <{/if}>
                        <{/if}>
                    </div>
                </li>
            <{/foreach}>
        </ul>
    <{/foreach}>
    </div>
<{else}>
    <div class="alert alert-info mt-4">
        <ol class="m-0">
            <{$smarty.const._MD_TADBOOKING_ITEM_OPTION_DESC}>
            <{if $smarty.session.tad_booking_adm}>
                <{$smarty.const._MD_TADBOOKING_BOOKING_ADM_DESC|sprintf:$max_booking_week}>
            <{else}>
                <{$smarty.const._MD_TADBOOKING_BOOKING_DESC|sprintf:$max_booking_week}>
            <{/if}>
        </ol>
    </div>
<{/if}>


<{if $item.desc|default:false}>
    <div class="my-border">
        <{$item.desc}>
    </div>
<{/if}>


<{$item.files|default:false}>
