<h2><{$smarty.const._MD_TADBOOKING_CATE_MANAGER}></h2>

<div class="alert alert-warning">

    <form action="manager.php" method="post" id="myForm" class="form-horizontal">
        <!--類別名稱-->
        <div class="form-group row mb-3">
            <label class="col-md-2 control-label col-form-label text-md-right text-md-end">
                <{$smarty.const._MD_TADBOOKING_CATE_TITLE}>
            </label>
            <div class="col-md-10">
                <input type="text" name="title" id="title" class="form-control " value="<{$title}>" placeholder="<{$smarty.const._MD_TADBOOKING_CATE_TITLE}>">
            </div>
        </div>

        <!--類別排序-->
        <div class="form-group row mb-3">
            <label class="col-md-2 control-label col-form-label text-md-right text-md-end">
                <{$smarty.const._MD_TADBOOKING_CATE_SORT}>
            </label>
            <div class="col-md-2">
                <input type="text" name="sort" id="sort" class="form-control " value="<{$sort}>" placeholder="<{$smarty.const._MD_TADBOOKING_CATE_SORT}>">
            </div>

            <!--狀態-->
            <label class="col-md-2 control-label col-form-label text-md-right text-md-end">
                <{$smarty.const._MD_TADBOOKING_CATE_ENABLE}>
            </label>
            <div class="col-md-4">

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

            <!--編號-->
            <div class="col-md-2">
                <input type='hidden' name="id" value="<{$id}>">
                <{$token_form|default:''}>
                <input type="hidden" name="op" value="<{$next_op|default:''}>">
                <input type="hidden" name="id" value="<{$id}>">

                <button type="submit" class="btn btn-primary"><i class="fa fa-floppy-disk" aria-hidden="true"></i> <{$smarty.const._TAD_SAVE}></button>
            </div>
        </div>

    </form>
</div>