<form action="<{$smarty.server.PHP_SELF}>" method="post" id="myForm" enctype="multipart/form-data" class="form-horizontal">


    <!--預約理由-->
    <div class="form-group row mb-3">
        <label class="col-md-2 control-label col-form-label text-md-right text-md-end">
            <{$smarty.const._MD_TADBOOKING_CONTENT}>
        </label>
        <div class="col-md-10">
            <input type="text" name="content" id="content" class="form-control " value="<{$content}>" placeholder="<{$smarty.const._MD_TADBOOKING_CONTENT}>">
        </div>
    </div>

    <!--開始日期 date-->
    <div class="form-group row mb-3">
        <label class="col-md-2 control-label col-form-label text-md-right text-md-end">
            <{$smarty.const._MD_TADBOOKING_START_DATE}>
        </label>
        <div class="col-md-5">
            <input type="text" name="start_date" id="start_date" class="form-control " value="<{$start_date}>" onClick="WdatePicker({dateFmt:'yyyy-MM-dd', startDate:'%y-%M-%d'})" placeholder="<{$smarty.const._MD_TADBOOKING_START_DATE}>">
        </div>
    </div>

    <!--結束日期 date-->
    <div class="form-group row mb-3">
        <label class="col-md-2 control-label col-form-label text-md-right text-md-end">
            <{$smarty.const._MD_TADBOOKING_END_DATE}>
        </label>
        <div class="col-md-5">
            <input type="text" name="end_date" id="end_date" class="form-control " value="<{$end_date}>" onClick="WdatePicker({dateFmt:'yyyy-MM-dd', startDate:'%y-%M-%d'})" placeholder="<{$smarty.const._MD_TADBOOKING_END_DATE}>">
        </div>
    </div>

    <!--相關資訊-->
    <div class="form-group row mb-3">
        <label class="col-md-2 control-label col-form-label text-md-right text-md-end">
            <{$smarty.const._MD_TADBOOKING_INFO}>
        </label>
        <div class="col-md-10">
            <textarea name="info" rows=8 id="info" class="form-control " placeholder="<{$smarty.const._MD_TADBOOKING_INFO}>"><{$info}></textarea>
        </div>
    </div>

    <div class="bar text-center">

        <!--預約者-->
        <input type='hidden' name="uid" value="<{$uid}>">
        <{$token_form|default:''}>
        <input type="hidden" name="op" value="<{$next_op|default:''}>">
        <input type="hidden" name="id" value="<{$id}>">

        <button type="submit" class="btn btn-primary"><i class="fa fa-floppy-disk" aria-hidden="true"></i> <{$smarty.const._TAD_SAVE}></button>
    </div>
</form>
