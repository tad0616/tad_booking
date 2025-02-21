<form action="<{$smarty.server.PHP_SELF}>" method="post" id="myForm" enctype="multipart/form-data">


    <!--類別編號-->
    <div class="row mb-3">
        <div class="col-md-2">
            <label class="control-label form-label">
                <{$smarty.const._MD_TADBOOKING_ITEM_CATE}>
            </label>
            <select name="cate_id" class="form-control form-select validate[required]">
                <{foreach from=$id_options item=opt}>
                    <option value="<{$opt.id}>" <{if $cate_id==$opt.id}>selected<{/if}>><{$opt.title}></option>
                <{/foreach}>
            </select>
        </div>

        <!--名稱-->
        <div class="col-md-4">
            <label class="control-label form-label">
                <{$smarty.const._MD_TADBOOKING_ITEM_TITLE}>
            </label>
            <input type="text" name="title" id="title" class="form-control validate[required]" value="<{$title}>" placeholder="<{$smarty.const._MD_TADBOOKING_ITEM_TITLE}>">
        </div>

        <!--啟用日期 date-->
        <div class="col-md-2">
            <label class="control-label form-label">
                <{$smarty.const._MD_TADBOOKING_ITEM_START}>
            </label>
            <input type="text" name="start" id="start" class="form-control validate[required]" value="<{$start}>" onClick="WdatePicker({dateFmt:'yyyy-MM-dd', startDate:'%y-%M-%d'})" placeholder="<{$smarty.const._MD_TADBOOKING_ITEM_START}>">
        </div>

        <!--停用日期 date-->
        <div class="col-md-2">
            <label class="control-label form-label">
                <{$smarty.const._MD_TADBOOKING_ITEM_END}>
            </label>
            <input type="text" name="end" id="end" class="form-control " value="<{$end}>" onClick="WdatePicker({dateFmt:'yyyy-MM-dd'})" placeholder="<{$smarty.const._MD_TADBOOKING_ITEM_END}>">
        </div>

        <!--是否可借-->
        <div class="col-md-2">
            <label class="control-label form-label">
                <{$smarty.const._MD_TADBOOKING_ITEM_ENABLE}>
            </label>
            <div>
                <div class="form-check-inline checkbox-inline pt-2">
                    <label class="form-check-label">
                        <input type="radio" name="enable" id="enable_1" class="form-check-input" value="1" <{if $enable == "1"}>checked="checked"<{/if}>>
                        <{$smarty.const._YES}>
                    </label>
                </div>
                <div class="form-check-inline checkbox-inline pt-2">
                    <label class="form-check-label">
                        <input type="radio" name="enable" id="enable_0" class="form-check-input" value="0" <{if $enable == "0"}>checked="checked"<{/if}>>
                        <{$smarty.const._NO}>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <!--說明-->
        <div class="col-md-12">
            <label class="control-label form-label">
                <{$smarty.const._MD_TADBOOKING_ITEM_DESC}>
            </label>
            <{$editor}>
        </div>
    </div>

    <!--審核人員-->
    <div class="row mb-3">
        <div class="col-md-12">
            <label class="control-label form-label">
                <{$smarty.const._MD_TADBOOKING_ITEM_APPROVAL}>
            </label>
            <{$token_form|default:''}>
            <input type="hidden" name="sort" value="<{$sort}>">
            <input type='hidden' name="id" value="<{$id}>">
            <{$tmt_box|default:''}>
        </div>
    </div>


    <div class="row mb-3">
        <!--相關圖片-->
        <div class="col-md-12">
            <label class="control-label form-label">
                <{$smarty.const._MD_TADBOOKING_ITEM_INFO}>
            </label>
            <{$tad_booking_item_files_create}>
        </div>
    </div>
</form>
