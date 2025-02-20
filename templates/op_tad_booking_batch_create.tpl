<form action="batch.php" method="post" id="myForm" class="form-horizontal">
    <div class="row g-1 my-3">
        <div class="col-lg-auto">
            <div class="input-group">
            <input type="text" id="startDate" name="start_date" class="form-control validate[required]" style="width: 8rem;" onclick="WdatePicker({minDate:'<{$minDate}>'<{if !$smarty.session.tad_booking_adm}>, maxDate:'<{$maxDate}>'<{/if}>}})" required value="<{$start_date}>">
                <div class="input-group-prepend input-group-addon">
                    <span class="input-group-text">起</span>
                </div>
            </div>
        </div>
        <div class="col-lg-auto">
            <div class="input-group">
                <input type="text" id="endDate" name="end_date" class="form-control validate[required]" style="width: 8rem;" onclick="WdatePicker({minDate:'#F{$dp.$D(\'startDate\')}'<{if !$smarty.session.tad_booking_adm}>, maxDate:'<{$maxDate}>'<{/if}>})" required value="<{$end_date}>" onchange="location.href='<{$smarty.server.PHP_SELF}>?start_date='+document.getElementById('startDate').value+'&end_date='+document.getElementById('endDate').value+'&item_id='+document.getElementById('item_id').value;">
                <div class="input-group-prepend input-group-addon">
                    <span class="input-group-text">止</span>
                </div>
            </div>

        </div>
        <div class="col-lg-3">
            <{include file="$xoops_rootpath/modules/tad_booking/templates/sub_item_menu.tpl" item_id=$item.id|default:0 and_date=1}>
        </div>
        <div class="col-lg-3">
            <input type="text" name="content" id="content" class="form-control validate[required]" value="<{$content|default:''}>" placeholder="<{$smarty.const._MD_TADBOOKING_CONTENT}>">
        </div>
        <div class="col-lg-auto">
            <{if $act=="preview"}>
                <input type="hidden" name="approval" value="<{$item.approval}>">
                <button type="submit" class="btn btn-success" name="op" value="tad_booking_batch_store"><{$smarty.const._MD_TADBOOKING_SUBMIT}></button>
            <{else}>
                <button type="submit" class="btn btn-primary" name="act" value="preview"><{$smarty.const._TAD_SUBMIT}></button>
            <{/if}>
        </div>
    </div>

    <{if $item.id|default:false}>
        <div class="alert alert-info">
            <ol class="m-0">
                <{if $item.approval}>
                    <{$smarty.const._MD_TADBOOKING_ITEM_NEED_APPROVAL_DESC|sprintf:$item.title}>
                <{else}>
                    <{$smarty.const._MD_TADBOOKING_ITEM_NO_NEED_APPROVAL_DESC|sprintf:$item.title}>
                <{/if}>
                <li><{$smarty.const._MD_TADBOOKING_BATCH_BOOKING_DESC|sprintf:$max_booking_week}></li>
            </ol>
        </div>
        <div class="vtb mt-4">
            <ul id="tad_booking_sort" class="vhead">
                <li class="w9"><{$smarty.const._MD_TADBOOKING_SECTION}></li>
                <{section name=loop start=0 loop=7 step=1}>
                    <{assign var="w" value="_MD_TADBOOKING_W`$smarty.section.loop.index`"}>
                    <li class="w13 num"><{$smarty.const.$w}></li>
                <{/section}>
            </ul>
        <{foreach from=$item.sections key=section_id item=section name=sections}>
            <ul id="tr_<{$section_id}>">
                <li class="c w9">
                    <div class="vcell"><{$smarty.const._MD_TADBOOKING_SECTION}></div>
                    <{$section.title}>
                </li>
                <{section name=loop start=0 loop=7 step=1}>
                    <{assign var="w" value=$smarty.section.loop.index}>
                    <li class="c w13">
                        <div class="vcell num"><{$smarty.const._MD_TADBOOKING_W}><{$item.chinese_week.$w}> <{$section.title}></div>
                        <div id="submit<{$section_id}>_<{$w}>">
                            <{if $w|in_array:$section.week_arr}>
                                <{if $act=="preview"}>
                                    <{foreach from=$my_batch_booking.$w item=booking_date}>
                                        <{if $week_section_id.$w.$section_id}>
                                            <{if $item.week_sections.$section_id.$w.$booking_date}>
                                                <div style="font-size:0.9rem" data-bs-toggle="tooltip"  data-bs-html="true" title="<{foreach from=$item.week_sections.$section_id.$w.$booking_date key=waiting item=booking}><div><{$smarty.const._MD_TADBOOKING_DATA_WAITING}><{$waiting}>：<{$booking.name}></div><{/foreach}>">
                                                    <{$booking_date|substr:5:10}> <input type="checkbox" name="booking_week_section[<{$w}>][<{$section_id}>][]" value="<{$booking_date}>" id="<{$booking_date}>_<{$w}>_<{$section_id}>"> <label for="<{$booking_date}>_<{$w}>_<{$section_id}>"><{$smarty.const._MD_TADBOOKING_DATA_WAITING}><{$waiting+1}></label>
                                                </div>
                                            <{else}>
                                                <div style="font-size:0.9rem">
                                                    <{$booking_date|substr:5:10}> <input type="checkbox" name="booking_week_section[<{$w}>][<{$section_id}>][]" value="<{$booking_date}>" checked id="<{$booking_date}>_<{$w}>_<{$section_id}>"> <label for="<{$booking_date}>_<{$w}>_<{$section_id}>"><{$smarty.const._MD_TADBOOKING_DATA_WAITING}>1</label>
                                                </div>
                                            <{/if}>
                                        <{/if}>
                                    <{/foreach}>
                                <{else}>
                                    <input type="checkbox" name="week_section_id[<{$w}>][<{$section_id}>]" value="<{$section_id}>">
                                <{/if}>
                            <{else}>
                                <span class="text-danger"><i class="fa fa-times" aria-hidden="true"></i></span>
                            <{/if}>
                        </div>
                    </li>
                    <{/section}>
            </ul>
        <{/foreach}>
        </div>
    <{else}>
        <div class="alert alert-info mt-4">
            <ol class="m-0">
                <{$smarty.const._MD_TADBOOKING_ITEM_OPTION_DESC}>
                <{$smarty.const._MD_TADBOOKING_BOOKING_DESC|sprintf:$max_booking_week}>
            </ol>
        </div>
    <{/if}>

</form>

<script>
    function validateForm() {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        if (!startDate || !endDate) {
            alert('<{$smarty.const._MD_TADBOOKING_NEED_START_END}>');
            return false;
        }

        // 將日期字符串轉換為Date對象進行比較
        const start = new Date(startDate);
        const end = new Date(endDate);

        if (start > end) {
            alert('<{$smarty.const._MD_TADBOOKING_START_BIGGER_END}>');
            return false;
        }

        // 這裡可以添加您的表單提交邏輯
        // alert('表單驗證通過！\n開始日期：' + startDate + '\n結束日期：' + endDate);
        return false; // 防止表單實際提交
    }
</script>