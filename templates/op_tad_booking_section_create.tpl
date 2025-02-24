<{$Bootstrap3EditableCode}>
<script type='text/javascript'>
  $(document).ready(function(){
    $('#sort').sortable({ opacity: 0.6, cursor: 'move', update: function() {
      var order = $(this).sortable('serialize');
      $.post('ajax.php?op=section_sort_save', order, function(theResponse){
        $('#save_msg').html(theResponse);
      });
    }
    });
  });

  function change_section_enable(section_id,w){
    $.post("ajax.php", {op: 'change_section_enable', section_id: section_id, week: w },
    function(data) {
      $('#'+section_id+'_'+w).attr('src',data);
    });
  }
</script>
<h2>「<{$title}>」<{$smarty.const._MD_TADBOOKING_SECTION_SETUP}></h2>

<div id="save_msg"></div>
<div class="row">
  <div class="col-sm-7">
    <form action="manager.php" method="post" id="myForm">
      <table class="table table-striped table-hover table-bordered">
        <thead>
          <tr class="bg-light">
            <th class="text-center">
              <!--時段標題-->
              <{$smarty.const._MD_TADBOOKING_SECTION_TITLE}>
            </th>
            <{section name=i loop=7}>
              <th class="text-center" style="background-color: #c1cff0;">
                <{assign var="w" value="_MD_TADBOOKING_W`$smarty.section.i.index`"}>
                <{$smarty.const.$w}>
              </th>
            <{/section}>
            <th class="text-center">
              <{$smarty.const._TAD_FUNCTION}>
            </th>
          </tr>
        </thead>

        <tbody id="sort">
          <{if $sections|default:false}>
            <{foreach from=$sections item=section}>
              <tr id='tr_<{$section.id}>'>
                <td class="text-center">
                  <!--時段標題-->
                  <div id="title_<{$section.id}>">
                    <span class="text-info">
                      <i class="fa-solid fa-arrows-up-down" title="<{$smarty.const._TAD_SORTABLE}>"></i>
                    </span>
                    <a href="#" class="editable" data-name="title" data-type="text" data-pk="<{$section.id}>" data-params="{op: 'tad_booking_section_update_title'}"><{$section.title|default:$smarty.const._MD_TADBOOKING_UNFILLED}></a></div>
                </td>

                <{section name=i loop=7}>
                  <td class="text-center">
                      <{include file="$xoops_rootpath/modules/tad_booking/templates/sub_change_enable_img.tpl" section=$section w=$smarty.section.i.index}>
                  </td>
                <{/section}>

                <td class="text-center">
                  <{if $section.booking_times!=""}>
                    <{$section.booking_times}>
                  <{else}>
                    <a href="javascript:delete_tad_booking_section_func(<{$section.id}>);" class="text-danger"><i class="fa fa-times" aria-hidden="true"></i></a>
                  <{/if}>
                </td>
              </tr>
            <{/foreach}>
          <{/if}>
        </tbody>
          <tr>
            <td>
              <input type="text" name="title" id="title" class="form-control validate[required] " value="" placeholder="<{$smarty.const._MD_TADBOOKING_TITLE}>">
            </td>
            <{section name=i loop=7}>
              <td class="text-center">
                <input type="checkbox" name="weeks[]" id="week<{$smarty.section.i.index}>" value="<{$smarty.section.i.index}>" <{if $smarty.section.i.index!=0 && $smarty.section.i.index!=6}>checked="checked"<{/if}> >
              </td>
            <{/section}>
            <td>
              <input type="hidden" name="sort" value="<{$sort}>">
              <input type="hidden" name="item_id" value="<{$id|default:''}>">
              <{$token_form|default:''}>
              <button type="submit" class="btn btn-primary" id="tad_booking_section_store" name="op" value="tad_booking_section_store"><i class="fa-solid fa-plus"></i></button>
            </td>
          </tr>
      </table>
    </form>
  </div>
  <{if !$sections}>
    <div class="col-sm-5">
      <div class="list-group">
        <a href="#" class="list-group-item active">
          <{$smarty.const._MD_TADBOOKING_IMPORT}>
        </a>
        <a href="manager.php?op=import_time&item_id=<{$id|default:''}>&type=18" class="list-group-item"><{$smarty.const._MD_TADBOOKING_IMPORT_18}></a>
        <a href="manager.php?op=import_time&item_id=<{$id|default:''}>&type=apm" class="list-group-item"><{$smarty.const._MD_TADBOOKING_IMPORT_APM}></a>
        <{foreach from=$item_section_count key=item_id item=item_section}>
          <{if $title != $item_section.title}>
          <a href="manager.php?op=copy_time&item_id=<{$item_id}>&to_item_id=<{$id|default:''}>" class="list-group-item"><{$smarty.const._MD_TADBOOKING_IMPORT_PLACE|sprintf:$item_section.title:$item_section.count}></a>
          <{/if}>
        <{/foreach}>
      </div>
    </div>
  <{/if}>
</div>
