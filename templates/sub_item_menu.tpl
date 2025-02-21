<{if $cates|default:false}>
    <select name="item_id" id="item_id" class="form-control form-select" onchange="location.href='<{$smarty.server.PHP_SELF}>?<{if $and_date|default:0}>start_date='+document.getElementById('startDate').value+'&end_date='+document.getElementById('endDate').value+'&content='+document.getElementById('content').value+'&<{/if}>item_id='+this.value+'#xoops_contents';">
        <option value=""><{$smarty.const._MD_TADBOOKING_SELECT_OPTION}></option>
        <{foreach from=$cates key=cate_id item=cate}>
            <{if $cate.items|@count > 0}>
                <optgroup label="<{$cate.title}> :" style="background-color: rgb(255, 248, 188); font-style: normal;">
                    <{foreach from=$cate.items item=cate_item}>
                        <option value="<{$cate_item.id}>" <{if $item_id == $cate_item.id}>selected<{/if}>>
                            <{$cate_item.title}>
                            <{if $cate_item.approval}> (<{$smarty.const._MD_TADBOOKING_ITEM_NEED_APPROVAL}>)<{/if}>
                        </option>
                    <{/foreach}>
                </optgroup>
            <{/if}>
        <{/foreach}>
    </select>
<{else}>
    <div class="alert alert-danger">
        尚無預約項目可選擇，待管理員新增可預約項目後始能預約
    </div>
<{/if}>