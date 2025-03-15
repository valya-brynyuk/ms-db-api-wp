/*------------------------------------------------------------------------------------*\
	SORTING API TABLE
\*------------------------------------------------------------------------------------*/

var listOptions = {
    valueNames: [ 'client','progress','matter-opened-date','case-details' ]
};

var userList = new List('sortable-bs', listOptions);

// userList.sort("matter-opened-date", {
//     order: "asc"
// })

//check if filter-100-percent checkbox is checked, if so, filter 100% lines
if (document.getElementById('filter-100-percent').checked) {
	userList.filter(function(item) {
		if (item.values().progress == '100%') {
		   return false;
		} else {
		   return true;
		}
	}); // Only items with progress != '100%' are shown in list
	userList.sort("matter-opened-date", {
		order: "asc"
	})
}
else {
	userList.sort("matter-opened-date", {
		order: "asc"
	})
	// userList.filter();
	// userList.sort("matter-opened-date", {
	// 	order: "asc"
	// })
}

jQuery('#filter-100-percent').click(function(){
	if (jQuery(this).is(':checked')) {
		userList.filter(function(item) {
			if (item.values().progress == '100%') {
			   return false;
			} else {
			   return true;
			}
		}); // Only items with progress != '100%' are shown in list
		userList.sort("matter-opened-date", {
			order: "asc"
		})
	}
	else {
		userList.filter();
		userList.sort("matter-opened-date", {
			order: "asc"
		})
	}
});