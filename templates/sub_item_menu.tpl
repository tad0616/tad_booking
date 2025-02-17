<select name="item_id" id="item_id" class="form-control form-select" onchange="location.href='<{$smarty.server.PHP_SELF}>?<{if $and_date|default:0}>start_date='+document.getElementById('startDate').value+'&end_date='+document.getElementById('endDate').value+'&<{/if}>item_id='+this.value;">
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