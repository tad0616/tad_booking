


<form action="<{$smarty.server.PHP_SELF}>" method="post" id="myForm" enctype="multipart/form-data" class="form-horizontal">


    <!--是否核准-->
    <div class="form-group row mb-3">
        <label class="col-md-2 control-label col-form-label text-md-right text-md-end">
            <{$smarty.const._MD_TADBOOKING_DATA_STATUS}>
        </label>
        <div class="col-md-10">

            <div class="form-check-inline checkbox-inline pt-2">
                <label class="form-check-label">
                    <input type="radio" name="status" id="status_1" class="form-check-input" value="1" <{if $status == "1"}>checked="checked"<{/if}>>
                    <{$smarty.const._YES}>
                </label>
            </div>
            <div class="form-check-inline checkbox-inline pt-2">
                <label class="form-check-label">
                    <input type="radio" name="status" id="status_0" class="form-check-input" value="0" <{if $status == "0"}>checked="checked"<{/if}>>
                    <{$smarty.const._NO}>
                </label>
            </div>
        </div>
    </div>

    <div class="bar text-center">

        <!--審核者-->
        <input type='hidden' name="approver" value="<{$approver}>">
        <{$token_form|default:''}>
        <input type="hidden" name="op" value="<{$next_op|default:''}>">
        <input type="hidden" name="booking_date" value="<{$booking_date}>">
        <input type="hidden" name="booking_id" value="<{$booking_id}>">
        <input type="hidden" name="section_id" value="<{$section_id}>">

        <button type="submit" class="btn btn-primary"><i class="fa fa-floppy-disk" aria-hidden="true"></i> <{$smarty.const._TAD_SAVE}></button>
    </div>
</form>
