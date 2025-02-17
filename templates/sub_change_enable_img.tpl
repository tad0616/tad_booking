<{if $w|in_array:$section.week_arr}>
    <img src="images/yes.gif" id="<{$section.id}>_<{$w}>" onClick="change_section_enable(<{$section.id}>, <{$w}>);" style="cursor: pointer;">
<{else}>
    <img src="images/no.gif" id="<{$section.id}>_<{$w}>" onClick="change_section_enable(<{$section.id}>, <{$w}>);" style="cursor: pointer;">
<{/if}>