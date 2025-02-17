<script type='text/javascript'>
    function single_insert_booking(wk,section_id,booking_date,item_id){
      $.post('ajax.php', {op: 'single_insert_booking', section_id: section_id, booking_date: booking_date, item_id: item_id, week: wk },
      function(data) {
        $('#submit'+section_id+'_'+wk).html(data).css('color','#000000');
      });
    }
</script>

<{$Bootstrap3EditableCode}>

<div class="row my-3 mx-auto">
    <div class="col-4 text-right text-end"><{if $item.id|default:false}><a href="index.php?item_id=<{$item.id}>&date=<{$item.prev_week_start}>" class="btn btn-success"><i class="fa-solid fa-circle-chevron-left"></i> <{$smarty.const._MD_TADBOOKING_PREVIOUS_WEEK}></a><{/if}></div>
    <div class="col-4"><{include file="$xoops_rootpath/modules/tad_booking/templates/sub_item_menu.tpl" item_id=$smarty.get.item_id|default:0}></div>
    <div class="col-4"><{if $item.id|default:false}><a href="index.php?item_id=<{$item.id}>&date=<{$item.next_week_start}>" class="btn btn-success"><{$smarty.const._MD_TADBOOKING_NEXT_WEEK}> <i class="fa-solid fa-circle-chevron-right"></i></a><{/if}></div>
</div>

<{if $item.id|default:false}>
    <div class="alert alert-info">
        <{if $item.approval}>
            <{$smarty.const._MD_TADBOOKING_ITEM_NEED_APPROVAL_DESC|sprintf:$item.title}>
        <{else}>
            <{$smarty.const._MD_TADBOOKING_ITEM_NO_NEED_APPROVAL_DESC|sprintf:$item.title}>
        <{/if}>
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
            <{assign var='first_booking' value=$item.first_booking.$date.$section_id}>
                <li class="c w13">
                    <div class="vcell num"><{$date}> (<{$item.chinese_week.$w}>) <{$section.title}></div>
                    <div id="submit<{$section_id}>_<{$w}>">
                        <{if $w|in_array:$section.week_arr && $date|strtotime >= $item.today}>
                            <{if $first_booking}>
                                <{if $first_booking.status == 1}><{$first_booking.info.name}><{else}><{$smarty.const._MD_TADBOOKING_APPROVING}><{/if}>
                                <{if $smarty.session.tad_booking_adm|default:false || $first_booking.uid == $smarty.session.now_user.uid}>
                                    <a href="javascript:delete_booking('<{$item.id}>', '<{$date}>', <{$section_id}>, '<{$first_booking.booking_id}>', '<{$first_booking.uid}>');" style='color:#D44950;' ><i class='fa fa-times' ></i></a>
                                <{/if}>

                                <{if $item.booking_arr.$date.$section_id|@count > 1}>
                                    <i class="fa fa-list" data-bs-toggle="tooltip" data-bs-html="true" data-bs-title="<{$smarty.const._MD_TADBOOKING_BOOKING_LIST}><ol>
                                        <{foreach from=$item.booking_arr.$date.$section_id item=booking name=bookings}>
                                        <li><{$booking.info.name}> <{if $booking.status}><i class='fa-solid fa-circle-check text-success'></i><{else}><i class='fa-solid fa-circle-xmark text-danger'></i><{/if}> <{$booking.content}></li>
                                        <{/foreach}>
                                    </ol>"></i>
                                <{/if}>

                                <div style="font-size:0.9rem">
                                    <{if $first_booking.uid == $smarty.session.now_user.uid}>
                                        <a href="#" class="editable" data-name="content" data-type="text" data-pk="<{$first_booking.booking_id}>" data-params="{op: 'tad_booking_update_content'}"><{$first_booking.content|default:'<{$smarty.const._MD_TADBOOKING_UNFILLED}>'}></a>
                                    <{else}>
                                        <{$first_booking.content|default:'<{$smarty.const._MD_TADBOOKING_UNFILLED}>'}>
                                    <{/if}>
                                </div>
                            <{elseif $smarty.session.now_user.uid}>
                                <button type="button" class="btn btn-info"  onclick="single_insert_booking('<{$w}>' , '<{$section.id}>','<{$date}>','<{$item.id}>')"><i class="fa fa-pencil"></i></button>
                            <{/if}>
                        <{else}>
                            <{if $first_booking && $first_booking.status == 1}>
                                <div style="color: gray;"><{$first_booking.info.name}></div>
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
        </ol>
    </div>
<{/if}>