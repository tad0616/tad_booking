<div class="row g-1 my-3">
    <div class="col-lg-2">
        <div class="input-group">
        <input type="text" id="startDate" name="startDate" class="Wdate form-control" onclick="WdatePicker({
            onpicked: function(dp) {
                calculateEndDate(dp.cal.getNewDateStr());
            }})" required value="<{$start_date}>">
            <div class="input-group-prepend input-group-addon">
                <span class="input-group-text">起</span>
            </div>
        </div>
    </div>
    <div class="col-lg-2">
        <div class="input-group">
            <input type="text" id="endDate" name="endDate" class="Wdate form-control" onclick="WdatePicker({minDate:'#F{$dp.$D(\'startDate\')}'})" required value="<{$end_date}>" onchange="location.href='<{$smarty.server.PHP_SELF}>?start_date='+document.getElementById('startDate').value+'&end_date='+document.getElementById('endDate').value+'&item_id='+document.getElementById('item_id').value;">
            <div class="input-group-prepend input-group-addon">
                <span class="input-group-text">止</span>
            </div>
        </div>

    </div>
    <div class="col-lg-auto">
        <{include file="$xoops_rootpath/modules/tad_booking/templates/sub_item_menu.tpl" and_date=1 item_id=$smarty.get.item_id|default:0}>
    </div>
    <{if $item.id|default:false}>
        <div class="col-lg-auto">
            <div class="mt-1">
                <a href="index.php?item_id=<{$item.id}>"><i class="fa-solid fa-pencil"></i>
                <{$smarty.const._MD_TADBOOKING_ITEM|sprintf:$item.title}>
                </a>
            </div>
        </div>
    <{/if}>
</div>

<{if $booking_data_arr}>
    <table class="table table-striped table-hover table-bordered table-condensed table-sm">
        <tr class="text-center"><th>類別</th><th>項目</th><th>預約者</th><th>預約日期</th><th><{$smarty.const._MD_TADBOOKING_DATA_WAITING}></th><th>預約理由</th><th>是否核准</th><th><{$smarty.const._TAD_FUNCTION}></th></tr>
        <{foreach from=$booking_data_arr item=data}>
            <{assign var="booking_id" value=$data.booking_id}>
            <{assign var="section_id" value=$data.section_id}>
            <{if !$item_id|default:0}>
                <{assign var="item_id" value=$section_arr.$section_id.item_id}>
                <{assign var="cate_id" value=$item_arr.$item_id.cate_id}>
            <{/if}>
            <{if $booking_arr.$booking_id.uid}>
                <tr class="text-center">
                    <{if $item_id|default:0 && $item|default:false}>
                        <td><{$item.cate.title}></td>
                        <td><a href="index.php?item_id=<{$item_id}>" target="_blank"><{$item.title}></a></td>
                    <{else}>
                        <td><{$cate_arr.$cate_id.title}></td>
                        <td><a href="index.php?item_id=<{$item_id}>" target="_blank"><{$item_arr.$item_id.title}></a></td>
                    <{/if}>
                    <td><{$booking_arr.$booking_id.info.name}></td>
                    <td><{$data.booking_date}> <{$section_arr.$section_id.title}></td>
                    <td><{$data.waiting}></td>
                    <td><{$booking_arr.$booking_id.content}></td>
                    <td>
                        <{if $data.status}>
                            <img src="images/yes.gif" alt="<{$smarty.const._MD_TADBOOKING_APPROVE}><{$smarty.const._MD_TADBOOKING_PASS}>">
                        <{elseif !$data.approver && $data.pass_date=="0000-00-00"}>
                            <{$smarty.const._MD_TADBOOKING_APPROVING}>
                        <{else}>
                            <img src="images/no.gif" alt="<{$smarty.const._MD_TADBOOKING_APPROVE}><{$smarty.const._MD_TADBOOKING_DENY}>">
                        <{/if}>

                    </td>
                    <td>
                        <{if $data.booking_date|strtotime > $smarty.now}>
                            <a href="javascript:delete_booking('<{$item_id}>', '<{$data.booking_date}>', <{$section_id}>, '<{$booking_id}>', '<{$booking_arr.$booking_id.uid}>');" class="btn btn-sm btn-danger"><i class='fa fa-times' ></i> <{$smarty.const._TAD_DEL}></a>
                        <{/if}>
                    </td>
                </tr>
            <{/if}>
        <{/foreach}>
    </table>
<{else}>
    <div class="alert alert-info">
        無預約資料
    </div>
<{/if}>

<script>
    function calculateEndDate(startDateStr) {
        if (!startDateStr) return;

        // 將開始日期轉換為Date物件
        const startDate = new Date(startDateStr);

        // 計算7天後的日期
        const endDate = new Date(startDate);
        endDate.setDate(endDate.getDate() + 7);

        // 格式化日期為 yyyy-MM-dd
        const endDateStr = formatDate(endDate);

        // 設定結束日期
        document.getElementById('endDate').value = endDateStr;

        location.href='<{$smarty.server.PHP_SELF}>?start_date='+document.getElementById('startDate').value+'&end_date='+document.getElementById('endDate').value+'&item_id='+document.getElementById('item_id').value;
    }

    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function validateForm() {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        if (!startDate || !endDate) {
            alert('請選擇開始和結束日期');
            return false;
        }

        // 將日期字符串轉換為Date對象進行比較
        const start = new Date(startDate);
        const end = new Date(endDate);

        if (start > end) {
            alert('結束日期必須大於開始日期');
            return false;
        }

        // 這裡可以添加您的表單提交邏輯
        alert('表單驗證通過！\n開始日期：' + startDate + '\n結束日期：' + endDate);
        return false; // 防止表單實際提交
    }
</script>