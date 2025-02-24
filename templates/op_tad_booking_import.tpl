<{if $show_error}>
    <div class="alert alert-warning">
        <h3><{$msg}></h3>
    </div>
<{else}>
    <h3><{$smarty.const._MA_TADBOOKING_JB_MODULECONFIG}> (mid: <{$jill_booking_mid}> -> <{$mid}>)</h3>
    <ol>
        <li><{$smarty.const._MI_JILLBOOKIN_BOOKING_GROUP}>: <{','|implode:$jillBookingModuleConfig.booking_group}></li>
        <li><{$smarty.const._MI_JILLBOOKIN_MAX_BOOKINGWEEK}>: <{$jillBookingModuleConfig.max_bookingweek}></li>
        <li><{$smarty.const._MI_JILLBOOKIN_CAN_SEND_MAIL}>: <{$jillBookingModuleConfig.can_send_mail}></li>
    </ol>
    <form action="main.php" method="post">
        <div class="row">
            <div class="col-lg-6">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th><{$smarty.const._MA_TADBOOKING_OLD_TABLE}></th>
                            <th><{$smarty.const._MA_TADBOOKING_DATA_AMOUNT}></th>
                        </tr>
                    </thead>
                    <tbody>
                        <{foreach from=$jill_booking_content item=jill_booking_data}>
                            <tr>
                                <td>
                                    <!--場地編號-->
                                    <{$jill_booking_data.table_name}>
                                </td>
                                <td>
                                    <!--場地名稱-->
                                    <{$jill_booking_data.row_count}>
                                </td>
                            </tr>
                        <{/foreach}>
                    </tbody>
                </table>
            </div>
            <div class="col-lg-6">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th><{$smarty.const._MA_TADBOOKING_NEW_TABLE}></th>
                            <th><{$smarty.const._MA_TADBOOKING_DATA_AMOUNT}></th>
                        </tr>
                    </thead>
                    <tbody>
                        <{foreach from=$tad_booking_content item=tad_booking_data}>
                            <tr>
                                <td>
                                    <!--場地編號-->
                                    <{$tad_booking_data.table_name}>
                                </td>
                                <td>
                                    <!--場地名稱-->
                                    <{$tad_booking_data.row_count}>
                                </td>
                            </tr>
                        <{/foreach}>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="text-center">
            <input type="hidden" name="op" value="import_now">
            <button type="submit" class="btn btn-lg btn-primary <{if $tad_booking_content.0.row_count!=0}>disabled<{/if}>"><i class="fa fa-cloud-download" aria-hidden="true"></i> <{$smarty.const._MA_TADBOOKING_IMPORT}></button>
            <div class="alert alert-warning p-2 mt-3"><{$smarty.const._MA_TADBOOKING_IMPORT_NOTE}></div>
        </div>
    </form>
<{/if}>