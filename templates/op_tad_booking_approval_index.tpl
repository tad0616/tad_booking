<script type='text/javascript'>
    function update_booking_status(booking_id, booking_date, section_id, item_id, status){
        $.post("ajax.php", {op: 'update_booking_status', booking_id: booking_id, booking_date: booking_date, section_id: section_id, item_id: item_id ,status:1},
        function(data) {
            if(data=='1'){
                $('#pass').addClass('col-sm-2 alert alert-success') ;
                $('#pass').text('<{$smarty.const._MD_TADBOOKING_UPDATE_COMPLETED}>');
                $('.'+booking_id+'_'+booking_date+'_'+section_id).remove();
            } else {
                $('#pass').addClass('span2 alert alert-danger') ;
                $('#pass').text('<{$smarty.const._MD_TADBOOKING_UPDATE_FAILED}>');
            }
        });
    }
</script>

<div id="pass" class="my-2"></div>
<form action="approval.php" method="post" id="myForm" class="form-horizontal">
    <div class="row my-3">
        <div class="col-auto"><{include file="$xoops_rootpath/modules/tad_booking/templates/sub_item_menu.tpl" item_id=$smarty.get.item_id|default:0}></div>
        <{if $item.id|default:false}>
            <div class="col-auto">
                <div class="mt-1">
                    <a href="index.php?item_id=<{$item.id}>"><i class="fa-solid fa-pencil"></i>
                    <{$smarty.const._MD_TADBOOKING_ITEM|sprintf:$item.title}>
                    </a>
                </div>
            </div>
        <{/if}>
    </div>

    <table data-toggle="table" data-pagination="false" data-mobile-responsive="true">
        <thead>
            <th class="c" data-sortable="true"><{$smarty.const._MD_TADBOOKING_UID}></th>
            <th class="c" data-sortable="true"><{$smarty.const._MD_TADBOOKING_BOOKING_TIME}></th>
            <th class="c" data-sortable="true"><{$smarty.const._MD_TADBOOKING_DATA_WAITING}></th>
            <th class="c"><{$smarty.const._MD_TADBOOKING_CONTENT}></th>
            <th class="c"><{$smarty.const._TAD_FUNCTION}></th>
        </thead>
        <{foreach from=$booking_data item=data}>
            <{assign var="booking_id" value=$data.booking_id}>
            <{assign var="section_id" value=$data.section_id}>
            <tr class="<{$booking_id}>_<{$data.booking_date}>_<{$section_id}>">
                <td class="c">
                    <input type="checkbox" name="batch_booking[<{$section_id}>][<{$booking_id}>]" value="<{$data.booking_date}>" <{if $data.waiting==1}>checked<{/if}>>
                    <{$booking.$booking_id.info.name}>
                </td>
                <td class="c">
                    <{$data.booking_date}>
                    <{assign var="w" value="_MD_TADBOOKING_W`$data.week`"}>
                    (<{$smarty.const._MD_TADBOOKING_W}><{$smarty.const.$w}>)
                    <{$item.sections.$section_id.title}>
                </td>
                <td class="c"><{$data.waiting}></td>
                <td class="c"><{$booking.$booking_id.content}></td>
                <td class="c">
                    <a href="javascript:delete_booking('<{$item.sections.$section_id.item_id}>', '<{$data.booking_date}>', '<{$section_id}>', '<{$booking_id}>', '<{$booking.$booking_id.uid}>');" class="btn btn-sm btn-danger"><i class="fa fa-times" aria-hidden="true"></i> <{$smarty.const._TAD_DEL}></a>

                    <a href="javascript:update_booking_status(<{$booking_id}>,'<{$data.booking_date}>',<{$section_id}>,<{$item.sections.$section_id.item_id}>, 0);" class="btn btn-sm btn-warning"><i class="fa fa-times" aria-hidden="true"></i> <{$smarty.const._MD_TADBOOKING_DENY}></a>

                    <a href="javascript:update_booking_status(<{$booking_id}>,'<{$data.booking_date}>',<{$section_id}>,<{$item.sections.$section_id.item_id}>, 1);" class="btn btn-sm btn-primary"><i class="fa fa-check" aria-hidden="true"></i> <{$smarty.const._MD_TADBOOKING_PASS}></a>
                </td>
            </tr>
        <{/foreach}>
    </table>

    <div class="bar">
        <button type="submit" name="op" value="batch_delete_booking" class="btn btn-danger">
            <i class="fa fa-times" aria-hidden="true"></i> <{$smarty.const._MD_TADBOOKING_BATCH_DELETE}>
        </button>
        <button type="submit" name="op" value="batch_update_booking_deny" class="btn btn-warning">
            <i class="fa fa-times" aria-hidden="true"></i> <{$smarty.const._MD_TADBOOKING_BATCH_DENY}>
        </button>
        <button type="submit" name="op" value="batch_update_booking_pass" class="btn btn-primary">
            <i class="fa fa-check" aria-hidden="true"></i> <{$smarty.const._MD_TADBOOKING_BATCH_PASS}>
        </button>
    </div>
</form>


<{$BootstrapTable}>