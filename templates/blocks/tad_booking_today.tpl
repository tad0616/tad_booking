<style>
    .today-section-txt {
        font-size: <{$block.font_size|default:0.8}>rem;
        white-space: nowrap;
    }
</style>
<div id="todayBookingTab<{$block.randStr}>" class="row">
    <ul class="resp-tabs-list vert" >
        <{foreach from=$block.cate_arr item=cate}>
            <li><{$cate.title}></li>
        <{/foreach}>
    </ul>
    <div class="resp-tabs-container vert">
        <{foreach from=$block.cate_arr item=cate}>
            <div class="table-responsive">
                <{foreach from=$cate.items item=item}>
                    <{assign var="w" value="_MB_TADBOOKING_W`$block.w`"}>
                    <a href="<{$xoops_url}>/modules/tad_booking/index.php?op=tad_booking_item_show&item_id=<{$item.id}>" target="_blank"><{$block.today}> (<{$smarty.const.$w}>) <{$item.title}></a>
                    <table class="table table-sm table-hover table-bordered">
                    <thead>
                        <tr>
                            <{foreach from=$item.sections item=section}>
                            <td class="bg-light text-center today-section-txt"><{$section.title|replace:" ":""}></td>
                            <{/foreach}>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <{foreach from=$item.sections key=section_id item=section}>
                                <td class="text-center today-section-txt">
                                    <{if $block.w|in_array:$section.week_arr}>
                                        <{if $block.booking_data_arr.$section_id}>
                                            <{foreach from=$block.booking_data_arr.$section_id key=waiting item=booking_data}>
                                                <div><{$booking_data.who.info.name}></div>
                                            <{/foreach}>
                                        <{else}>
                                        <span style="color: #8ba2ad;"><{$smarty.const._MB_TADBOOKING_NO}></span>
                                        <{/if}>
                                    <{else}>
                                        <span class="text-danger">
                                            <i class="fa fa-times" aria-hidden="true"></i>
                                        </span>
                                    <{/if}>
                                </td>
                            <{/foreach}>
                        </tr>
                    </tbody>
                    </table>
                <{/foreach}>
            </div>
        <{/foreach}>
    </div>
</div>

<div class="text-end text-right mt-2">
    <a href="<{$xoops_url}>/modules/tad_booking/index.php" class="btn btn-sm btn-info"><{$smarty.const._MB_TADBOOKING_MORE}></a>
</div>