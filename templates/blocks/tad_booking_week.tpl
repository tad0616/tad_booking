<style>
    .section-txt {
        font-size: <{$block.font_size|default:0.8}>rem;
        white-space: nowrap;
    }
    .status1{
        color:blue;
    }
    .status0{
        color:#8ba2ad;
    }
</style>
<div id="bTab-<{$block.randStr}>">
    <ul class="resp-tabs-list item_tab_parent" >
        <{foreach from=$block.cate_arr item=cate}>
            <li><{$cate.title}></li>
        <{/foreach}>
    </ul>
    <div class="resp-tabs-container item_tab_parent">
        <{foreach from=$block.cate_arr key=cate_id item=cate}>
            <div>
                <{include file="$xoops_rootpath/modules/tad_booking/templates/blocks/sub_bookingItemTab.tpl"}>
            </div>
        <{/foreach}>
    </div>
</div>

<div class="text-end text-right mt-2">
    <a href="<{$xoops_url}>/modules/tad_booking/index.php" class="btn btn-sm btn-info"><{$smarty.const._MB_TADBOOKING_MORE}></a>
</div>