<div id="iTab<{$cate_id}>">
    <ul class="resp-tabs-list item_tab_child" >
        <{foreach from=$cate.items item=item}>
            <li><{$item.title}></li>
        <{/foreach}>
    </ul>
    <div class="resp-tabs-container item_tab_child">
        <{foreach from=$cate.items item=item}>
            <div class="table-responsive">
                <table class="table table-sm table-striped table-hover table-bordered">
                    <thead>
                        <tr>
                            <td class="text-center bg-light section-txt">
                                <div><{$smarty.now|date_format:'%Y'}></div>
                                <div><{$smarty.const._MB_TADBOOKING_SECTION}></div>
                            </td>
                            <{foreach from=$block.week_data.week_dates key=w item=date}>
                                <{assign var="w" value="_MB_TADBOOKING_W`$w`"}>
                                <td class="text-center bg-light section-txt">
                                    <div><{$date|substr:5:10}></div>
                                    <div><{$smarty.const.$w}></div>
                                </td>
                            <{/foreach}>
                        </tr>
                    </thead>
                    <tbody>
                        <{foreach from=$item.sections key=section_id item=section}>
                            <tr>
                                <td class="text-center section-txt"><{$section.title|replace:" ":""}></td>
                                <{foreach from=$block.week_data.week_dates key=w item=date}>
                                    <td class="section-txt text-center">
                                        <{if $w|in_array:$section.week_arr}>
                                            <{assign var="booking_arr" value=$block.booking_arr}>
                                            <{if $booking_arr.$date.$section_id}>
                                                <{foreach from=$booking_arr.$date.$section_id key=uid item=booking}>
                                                    <div class="status<{$booking.status}>"><{$booking.info.name}></div>
                                                <{/foreach}>
                                            <{elseif $date|strtotime > $smarty.now}>
                                                <span style="color: #8ba2ad;"><{$smarty.const._MB_TADBOOKING_NO}></span>
                                            <{else}>

                                            <{/if}>
                                        <{else}>
                                            <span class="text-danger">
                                                <i class="fa fa-times" aria-hidden="true"></i>
                                            </span>
                                        <{/if}>
                                    </td>
                                <{/foreach}>
                            </tr>
                        <{/foreach}>
                    </tbody>
                </table>
            </div>
        <{/foreach}>
    </div>
</div>
