function bcCollapsibleRow_Show(className, rowId) {
    jQuery("#bc_list_collapsible_image_" + rowId).attr("alt", "\u2191");
    jQuery("#bc_list_collapsible_image_" + rowId).attr("class", className + " collapsed");
    jQuery("#bc_list_collapsible_row_" + rowId).show();
}

function bcCollapsibleRow_Hide(className, rowId) {
    jQuery("#bc_list_collapsible_image_" + rowId).attr("alt", "\u2193");
    jQuery("#bc_list_collapsible_image_" + rowId).attr("class", className);
    jQuery("#bc_list_collapsible_row_" + rowId).hide();
}

function bcCollapsibleRow_Toggle(className, sender, rowId) {
    if (jQuery(sender).attr("class") === className)
        bcCollapsibleRow_Show(className, rowId);
    else
        bcCollapsibleRow_Hide(className, rowId);
}

function bcPopup_Show(className, rowId) {
    jQuery("#bc_list_popupable_image_" + rowId).attr("alt", "\u2191");
    jQuery("#bc_list_popupable_image_" + rowId).attr("class", className + " popuped");
    jQuery("#bc_list_popup_" + rowId).fadeIn('slow');
}

function bcPopup_Hide(className, rowId) {
    jQuery("#bc_list_popupable_image_" + rowId).attr("alt", "\u2193");
    jQuery("#bc_list_popupable_image_" + rowId).attr("class", className);
    jQuery("#bc_list_popup_" + rowId).fadeOut('slow');
}

function bcPopup_Toggle(className, sender, rowId) {
    if (jQuery(sender).attr("class") === className)
        bcPopup_Show(className, rowId);
    else
        bcPopup_Hide(className, rowId);
}

